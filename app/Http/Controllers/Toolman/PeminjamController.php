<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Http\Request;

class PeminjamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $tab = $request->input('tab', 'pending'); // 'pending' | 'active' | 'suspended'
        $search = $request->input('search');
        $roleFilter = $request->input('role'); // 'siswa' | 'guru'

        // Base query untuk peminjam bengkel (Siswa bengkel ini + Guru)
        $baseQuery = User::where('role', 'peminjam')
            ->where(function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId)
                    ->orWhere('jenis_peminjam', 'guru');
            });

        // Metrik
        $totalCount = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->where('status', 'menunggu_acc')->count();
        $activeCount = (clone $baseQuery)->where('status', 'aktif')->count();
        $suspendedCount = (clone $baseQuery)->where('status', 'suspend')->count();

        // Query data sesuai tab aktif
        $query = clone $baseQuery;

        if ($tab === 'pending') {
            $query->where('status', 'menunggu_acc');
        } elseif ($tab === 'active') {
            $query->where('status', 'aktif');
        } elseif ($tab === 'suspended') {
            $query->where('status', 'suspend');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, ['siswa', 'guru'])) {
            $query->where('jenis_peminjam', $roleFilter);
        }

        $peminjams = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('toolman.peminjam.index', compact(
            'peminjams',
            'bengkel',
            'tab',
            'totalCount',
            'pendingCount',
            'activeCount',
            'suspendedCount'
        ));
    }

    public function show($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $peminjam = User::with(['bengkel', 'peminjamans' => function ($q) use ($bengkelId) {
            $q->where('bengkel_id', $bengkelId)->with('detailPeminjamans.barang')->latest();
        }])->where('role', 'peminjam')->findOrFail($id);

        return view('toolman.peminjam.show', compact('peminjam', 'bengkel'));
    }

    public function approveUser($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $targetUser = User::where('role', 'peminjam')
            ->where(function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId)
                    ->orWhere('jenis_peminjam', 'guru');
            })
            ->findOrFail($id);

        if ($targetUser->status !== 'menunggu_acc') {
            return redirect()->back()->with('error', "Pengguna {$targetUser->name} sudah tidak dalam status menunggu approval (status saat ini: {$targetUser->status}).");
        }

        $targetUser->update([
            'status' => 'aktif',
        ]);

        return redirect()->route('toolman.peminjam.index', ['tab' => 'active'])
            ->with('success', "Pendaftaran {$targetUser->name} berhasil disetujui! Akun kini aktif dan dapat meminjam alat/bahan.");
    }

    public function rejectUser(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $targetUser = User::where('role', 'peminjam')
            ->where(function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId)
                    ->orWhere('jenis_peminjam', 'guru');
            })
            ->findOrFail($id);

        if ($targetUser->status !== 'menunggu_acc') {
            return redirect()->back()->with('error', "Pengguna {$targetUser->name} sudah tidak dalam status menunggu approval.");
        }

        $name = $targetUser->name;
        $targetUser->delete();

        return redirect()->route('toolman.peminjam.index', ['tab' => 'pending'])
            ->with('success', "Pendaftaran akun {$name} telah ditolak.");
    }

    public function suspendUser(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $targetUser = User::where('role', 'peminjam')
            ->where(function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId)
                    ->orWhere('jenis_peminjam', 'guru');
            })
            ->findOrFail($id);

        if ($targetUser->id === $user->id) {
            return redirect()->back()->with('error', "Anda tidak dapat menangguhkan akun sendiri.");
        }

        if ($targetUser->status === 'suspend') {
            return redirect()->back()->with('error', "Akun {$targetUser->name} sudah dalam status ditangguhkan (suspend).");
        }

        $targetUser->update([
            'status' => 'suspend',
        ]);

        return redirect()->route('toolman.peminjam.index', ['tab' => 'suspended'])
            ->with('success', "Akun {$targetUser->name} berhasil ditangguhkan (suspend). Akses peminjaman dinonaktifkan.");
    }

    public function activateUser(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $targetUser = User::where('role', 'peminjam')
            ->where(function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId)
                    ->orWhere('jenis_peminjam', 'guru');
            })
            ->findOrFail($id);

        if ($targetUser->status === 'aktif') {
            return redirect()->back()->with('error', "Akun {$targetUser->name} sudah dalam status aktif.");
        }

        $targetUser->update([
            'status' => 'aktif',
        ]);

        return redirect()->route('toolman.peminjam.index', ['tab' => 'active'])
            ->with('success', "Akun {$targetUser->name} berhasil dipulihkan! Peminjam kini dapat membuat pengajuan alat/bahan kembali.");
    }
}
