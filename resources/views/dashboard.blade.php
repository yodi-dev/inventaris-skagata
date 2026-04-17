<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight border-l-4 border-emerald-500 pl-4">
                {{ __('Home Center') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-emerald-500 hover:shadow-md transition">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Total Aset Barang</p>
                            <h2 class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $total_barang }}</h2>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-full text-3xl">📦</div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-emerald-500 hover:shadow-md transition">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Kategori Master</p>
                            <h2 class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $total_kategori }}</h2>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-full text-3xl">🗂️</div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-b-4 border-emerald-500 hover:shadow-md transition">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-1">Ruangan Dikelola</p>
                            <h2 class="text-4xl font-bold text-gray-800 dark:text-gray-100">{{ $total_ruangan }}</h2>
                        </div>
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-full text-3xl">🏢</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-1">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Jalan Pintas</h3>
                        <div class="space-x-2">
                            <a href="{{ route('items.create') }}">
                                <button
                                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-lg transition">
                                    <span>➕</span> Tambah Barang Masuk
                                </button>
                            </a>
                            <a href="{{ route('categories.index') }}">
                                <button
                                    class="w-full flex items-center justify-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-2.5 px-4 rounded-lg transition">
                                    <span>🏷️</span> Kelola Kategori
                                </button>
                            </a>
                            <a href="{{ route('rooms.index') }}">
                                <button
                                    class="w-full flex items-center justify-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium py-2.5 px-4 rounded-lg transition">
                                    <span>🚪</span> Kelola Ruangan
                                </button>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Barang Baru Masuk</h3>
                        <a href="{{ route('items.index') }}"
                            class="text-sm text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-medium">Lihat
                            Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-sm">
                                    <th class="px-6 py-3 font-medium">Nama Barang</th>
                                    <th class="px-6 py-3 font-medium">Kategori</th>
                                    <th class="px-6 py-3 font-medium">Ruangan</th>
                                    <th class="px-6 py-3 font-medium">Tanggal Masuk</th>
                                </tr>
                            </thead>
                            <tbody
                                class="text-gray-700 dark:text-gray-300 text-sm divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($recent_items as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-6 py-4 font-medium">{{ $item->name }}</td>
                                        <td class="px-6 py-4">{{ $item->category->name ?? 'Tanpa Kategori' }}</td>
                                        <td class="px-6 py-4">{{ $item->room->name ?? 'Tanpa Ruangan' }}</td>
                                        <td class="px-6 py-4">{{ $item->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada data barang masuk nih sob. 📦
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
