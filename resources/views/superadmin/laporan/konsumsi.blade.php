@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-slate-50 p-6">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Laporan Konsumsi Bahan</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau penggunaan barang habis pakai (BHP) di setiap bengkel/jurusan.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-green-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Export Excel
                </button>
                <button
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
            <form action="{{ route('superadmin.laporan.konsumsi') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        onchange="this.form.submit()"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all">
                </div>

                <!-- Filter Tanggal Akhir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all">
                </div>

                <!-- Filter Bengkel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bengkel / Jurusan</label>
                    <select name="bengkel" onchange="this.form.submit()"
                        class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm py-2 px-3 border shadow-sm outline-none transition-all bg-white">
                        <option value="">Semua Bengkel</option>
                        @foreach ($bengkels as $bengkel)
                            <option value="{{ $bengkel->id }}" {{ request('bengkel') == $bengkel->id ? 'selected' : '' }}>
                                {{ $bengkel->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if (request()->anyFilled(['start_date', 'end_date', 'bengkel']))
                    <div class="col-span-1 md:col-span-3 mt-1 flex justify-end">
                        <a href="{{ route('superadmin.laporan.konsumsi') }}"
                            class="text-xs text-red-600 hover:text-red-800 font-medium inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Hapus Semua Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Barang & Kode</th>
                            <th class="py-3 px-4 font-semibold">Bengkel</th>
                            <th class="py-3 px-4 font-semibold text-center">Jumlah Dipakai</th>
                            <th class="py-3 px-4 font-semibold">Keterangan / Tujuan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                        @forelse ($konsumsi as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">{{ $item->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">{{ $item->barang->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->barang->kode_barang ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">{{ $item->barang->bengkel->nama ?? '-' }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M20 12H4"></path>
                                        </svg>
                                        {{ abs($item->jumlah) }} {{ $item->barang->satuan ?? 'unit' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-gray-900">{{ $item->keterangan ?? '-' }}</div>
                                    @if ($item->user)
                                        <div class="text-xs text-gray-500 mt-0.5">Oleh: {{ $item->user->name }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-gray-500">
                                    Tidak ada data konsumsi bahan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($konsumsi->hasPages())
                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    {{ $konsumsi->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
