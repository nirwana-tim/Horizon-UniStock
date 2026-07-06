<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Jadwal Distribusi') }}</h2>
            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Kembali') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $prodiByFaculty = $studyPrograms->groupBy('faculty_id')->map(fn($group) => $group->map(fn($sp) => [
                            'value' => (string) $sp->id,
                            'label' => $sp->name,
                        ])->values()->toArray())->toArray();
                        $allProdi = $studyPrograms->map(fn($sp) => [
                            'value' => (string) $sp->id,
                            'label' => $sp->name,
                            'faculty_id' => (string) $sp->faculty_id,
                        ])->toArray();
                    @endphp

                    <form action="{{ route('distribution.distribution-schedule.store') }}" method="POST"
                          x-data="{
                              programLevelId: '{{ old('program_level_id') }}',
                              facultyId: '{{ old('faculty_id') }}',
                              prodiId: '{{ old('study_program_id') }}',
                              prodiByFaculty: {{ json_encode($prodiByFaculty) }},
                              allProdi: {{ json_encode($allProdi) }},
                              levelCodes: {{ json_encode($levelCodes) }},
                              facultyCodes: {{ json_encode($facultyCodes) }},
                              prodiCodes: {{ json_encode($prodiCodes) }},
                              entitlementMap: {{ json_encode($entitlementMap) }},
                              get filteredProdi() {
                                  if (this.facultyId && this.prodiByFaculty[this.facultyId]) {
                                      return this.prodiByFaculty[this.facultyId];
                                  }
                                  if (!this.facultyId) return this.allProdi;
                                  return [];
                              },
                              get entitlementCode() {
                                  let levelCode = this.levelCodes[this.programLevelId] || '';
                                  let facultyCode = this.facultyCodes[this.facultyId] || '';
                                  let prodiCode = this.prodiCodes[this.prodiId] || '';
                                  if (levelCode && facultyCode && prodiCode) {
                                      return levelCode + facultyCode + prodiCode;
                                  }
                                  return '';
                              },
                              isItemVisible(itemId) {
                                  let code = this.entitlementCode;
                                  if (!code) return true;
                                  let allowedItems = this.entitlementMap[code] || [];
                                  return allowedItems.includes(itemId);
                              },
                              init() {
                                  this.$watch('entitlementCode', (newCode) => {
                                      if (newCode) {
                                          let allowed = this.entitlementMap[newCode] || [];
                                          document.querySelectorAll('input[name=\'item_ids[]\']').forEach(input => {
                                              let itemId = parseInt(input.value);
                                              if (!allowed.includes(itemId)) {
                                                  input.checked = false;
                                              }
                                          });
                                      }
                                  });
                              }
                          }">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nama Jadwal')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div @change="programLevelId = $event.target.value">
                                <x-input-label for="program_level_id" :value="__('Angkatan')" />
                                @php
                                    $levelOptions = $programLevels->map(fn($l) => [
                                        'value' => $l->id,
                                        'label' => $l->name,
                                        'group' => '',
                                    ])->toArray();
                                @endphp
                                <x-searchable-select name="program_level_id" :options="$levelOptions" :value="old('program_level_id')" placeholder="-- Semua Angkatan --" />
                                <x-input-error :messages="$errors->get('program_level_id')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="faculty_id" :value="__('Fakultas')" />
                                    <select id="faculty_id" name="faculty_id" x-model="facultyId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">-- Semua Fakultas --</option>
                                        @foreach($faculties as $f)
                                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="study_program_id" :value="__('Program Studi')" />
                                    <select id="study_program_id" name="study_program_id" x-model="prodiId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">-- Semua Prodi --</option>
                                        <template x-for="sp in filteredProdi" :key="sp.value">
                                            <option x-bind:value="sp.value" x-text="sp.label"></option>
                                        </template>
                                    </select>
                                    <x-input-error :messages="$errors->get('study_program_id')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Status Aktif')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Tanggal')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date')" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="location" :value="__('Lokasi')" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="session" :value="__('Sesi / Jam')" />
                                <x-text-input id="session" name="session" type="text" class="mt-1 block w-full" :value="old('session')" required placeholder="09:00-12:00" />
                                <x-input-error :messages="$errors->get('session')" class="mt-2" />
                            </div>
                            
                            {{-- Simplified Grid of Checked Items for Schedule --}}
                            <div class="md:col-span-2">
                                <x-input-label :value="__('Item yang Dibagikan')" />
                                <p class="mt-1 mb-4 text-xs text-gray-500">Pilih item yang akan didistribusikan pada jadwal ini.</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($items as $item)
                                        @php
                                            $isChecked = in_array($item->id, old('item_ids', []));
                                        @endphp
                                        <label x-show="isItemVisible({{ $item->id }})"
                                               class="flex items-center space-x-2 p-3 border rounded-lg bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
                                            <input type="checkbox" 
                                                   name="item_ids[]" 
                                                   value="{{ $item->id }}" 
                                                   {{ $isChecked ? 'checked' : '' }} 
                                                   class="rounded border-gray-300 text-primary-700 shadow-sm focus:ring-primary-500">
                                            <span class="text-sm text-gray-700 font-semibold">{{ $item->name }} ({{ $item->code }})</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('item_ids')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
