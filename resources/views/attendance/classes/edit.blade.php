@php
    $pageTitle = 'Edit Kelas';
    $breadcrumbs = [
        ['label' => 'Data Kelas', 'url' => route('attendance.classes.index')],
        ['label' => 'Edit Kelas']
    ];
@endphp

<x-app-layout>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Kelas</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui informasi kelas {{ $class->nama_kelas }}</p>
        </div>

        {{-- Form Card --}}
        <x-card>
            <form method="POST" action="{{ route('attendance.classes.update', $class->id) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Nama Kelas --}}
                    <x-input
                        type="text"
                        name="nama_kelas"
                        label="Nama Kelas"
                        :value="old('nama_kelas', $class->nama_kelas)"
                        placeholder="Contoh: RPL A"
                        required
                        :error="$errors->first('nama_kelas')"
                    />

                    {{-- Tingkat & Jurusan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-select
                            name="tingkat"
                            label="Tingkat"
                            required
                            :error="$errors->first('tingkat')"
                        >
                            <option value="">Pilih...</option>
                            <option value="X" {{ old('tingkat', $class->tingkat) == 'X' ? 'selected' : '' }}>X (Kelas 10)</option>
                            <option value="XI" {{ old('tingkat', $class->tingkat) == 'XI' ? 'selected' : '' }}>XI (Kelas 11)</option>
                            <option value="XII" {{ old('tingkat', $class->tingkat) == 'XII' ? 'selected' : '' }}>XII (Kelas 12)</option>
                        </x-select>

                        <x-input
                            type="text"
                            name="jurusan"
                            label="Jurusan"
                            :value="old('jurusan', $class->jurusan)"
                            placeholder="Contoh: RPL, TKJ, MM"
                            :error="$errors->first('jurusan')"
                        />
                    </div>

                    {{-- Wali Kelas (optional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wali Kelas</label>
                        <select
                            name="wali_kelas_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="">— Tidak ada wali kelas —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ old('wali_kelas_id', $class->wali_kelas_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('wali_kelas_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $class->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-primary-600 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Kelas Aktif</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hanya kelas aktif yang dapat digunakan untuk absensi</p>
                    </div>

                    {{-- Info Box - Student Count --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>Total Siswa:</strong> {{ $class->students->count() ?? 0 }} siswa terdaftar di kelas ini
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <a
                            href="{{ route('attendance.classes.index') }}"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Update Kelas
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
