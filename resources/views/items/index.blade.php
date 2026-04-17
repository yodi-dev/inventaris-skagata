<x-app-layout>
    <x-slot name="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight border-l-4 border-emerald-500 pl-4">
            {{ __('Daftar Barang Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6 flex justify-end">
                        <a href="{{ route('items.create') }}"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-150 ease-in-out">
                            + Tambah Barang
                        </a>
                    </div>

                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Kode</th>
                                    <th scope="col" class="px-6 py-3">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3">Kategori</th>
                                    <th scope="col" class="px-6 py-3">Ruangan</th>
                                    <th scope="col" class="px-6 py-3 text-center">Jml</th>
                                    <th scope="col" class="px-6 py-3 text-center">Kondisi</th>
                                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-bold">{{ $item->item_code }}</td>
                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $item->category->name ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $item->room->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($item->condition == 'good')
                                                <span
                                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Baik</span>
                                            @elseif($item->condition == 'slightly_damaged')
                                                <span
                                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Rusak
                                                    Ringan</span>
                                            @else
                                                <span
                                                    class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rusak
                                                    Berat</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex justify-center space-x-2">
                                            <a href="{{ route('items.edit', $item->id) }}"
                                                class="text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                            <form action="{{ route('items.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin mau hapus barang ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 dark:text-red-500 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center">Belum ada data barang.</td>
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
