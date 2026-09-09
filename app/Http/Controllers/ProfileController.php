<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use App\Models\Pengadaan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('bengkel');

        // Tentukan role tampilan berdasarkan role akun yang sedang login
        if ($user->role === 'waka') {
            $role = 'superadmin';
        } elseif ($user->role === 'toolman') {
            $role = 'toolman';
        } else {
            $role = 'peminjam';
        }

        // Hitung statistik riil pengguna yang sedang login
        $stats = [];
        if ($role === 'superadmin') {
            $bengkelCount = Bengkel::count();
            $rabVerified = Pengadaan::whereIn('status', ['approved', 'rejected', 'revisi'])->count();
            $approvedRabs = Pengadaan::where('status', 'approved')->with('detailPengadaans')->get();
            $totalAcc = 0;
            foreach ($approvedRabs as $rab) {
                foreach ($rab->detailPengadaans as $detail) {
                    $totalAcc += ($detail->jumlah ?? 0) * ($detail->harga_estimasi ?? 0);
                }
            }
            $formattedAcc = $totalAcc >= 1000000
                ? 'Rp ' . number_format($totalAcc / 1000000, 1, ',', '.') . ' Jt'
                : 'Rp ' . number_format($totalAcc, 0, ',', '.');

            $stats = [
                ['label' => 'Bengkel Binaan', 'value' => $bengkelCount . ' Jurusan'],
                ['label' => 'RAB Diverifikasi', 'value' => $rabVerified . ' Usulan'],
                ['label' => 'Anggaran ACC', 'value' => $formattedAcc],
                ['label' => 'Wewenang', 'value' => 'Penuh (Waka)'],
            ];
        } elseif ($role === 'toolman') {
            $bengkelId = $user->bengkel_id;
            $barangCount = $bengkelId ? Barang::where('bengkel_id', $bengkelId)->count() : 0;
            $antreanPinjam = $bengkelId ? Peminjaman::where('bengkel_id', $bengkelId)->where('status', 'menunggu_persetujuan')->count() : 0;
            $rabCount = $bengkelId ? Pengadaan::where('bengkel_id', $bengkelId)->count() : 0;
            $bengkelKode = $user->bengkel ? ($user->bengkel->kode ?? $user->bengkel->nama) : '-';

            $stats = [
                ['label' => 'Barang Dikelola', 'value' => $barangCount . ' Jenis'],
                ['label' => 'Antrean Pinjam', 'value' => $antreanPinjam . ' Tiket'],
                ['label' => 'Usulan RAB', 'value' => $rabCount . ' Draf'],
                ['label' => 'Bengkel Binaan', 'value' => $bengkelKode],
            ];
        } else { // peminjam
            $totalPinjam = Peminjaman::where('user_id', $user->id)->count();
            $sedangDipinjam = Peminjaman::where('user_id', $user->id)->whereIn('status', ['dipinjam', 'terlambat'])->count();
            $selesai = Peminjaman::where('user_id', $user->id)->where('status', 'selesai')->count();
            $terlambat = Peminjaman::where('user_id', $user->id)->where('status', 'terlambat')->count();
            $tepatWaktuRate = $totalPinjam > 0 ? round(($selesai / $totalPinjam) * 100) . '%' : '100%';

            $stats = [
                ['label' => 'Total Pinjam', 'value' => $totalPinjam . ' Kali'],
                ['label' => 'Sedang Dipinjam', 'value' => $sedangDipinjam . ' Alat'],
                ['label' => 'Tepat Waktu', 'value' => $tepatWaktuRate],
                ['label' => 'Pelanggaran', 'value' => $terlambat . ' (Terlambat)'],
            ];
        }

        return view('profile.edit', compact('user', 'role', 'stats'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
