<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Distribution Schedule') }}</h2>
            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('← Back') }}</a>
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

                    <form action="{{ route('distribution.distribution-schedule.update', $distributionSchedule) }}" method="POST"
                          x-data="{
                              facultyId: '{{ old('faculty_id', $distributionSchedule->faculty_id) }}',
                              prodiId: '{{ old('study_program_id', $distributionSchedule->study_program_id ?? 'all') }}',
                              studentLevel: '{{ old('student_level', $distributionSchedule->student_level) }}',
                              prodiByFaculty: {{ json_encode($prodiByFaculty) }},
                              allProdi: {{ json_encode($allProdi) }},
                              itemHtml: '',
                              itemError: '',
                              itemSearch: '',
                              selectedItemIds: @json($distributionSchedule->items->pluck('item_id')->toArray()),
                              get filteredProdi() {
                                  if (this.facultyId && this.prodiByFaculty[this.facultyId]) {
                                      return this.prodiByFaculty[this.facultyId];
                                  }
                                  if (!this.facultyId) return this.allProdi;
                                  return [];
                              },
                              init() {
                                  this.$watch('prodiId', () => this.fetchItems());
                                  this.$watch('facultyId', () => { if (this.prodiId) this.fetchItems(); });
                                  this.$watch('studentLevel', () => { if (this.prodiId) this.fetchItems(); });
                                  if (this.prodiId) this.fetchItems();
                              },
                              fetchItems() {
                                  let params = {
                                      faculty_id: this.facultyId || '',
                                      study_program_id: this.prodiId,
                                      student_level: this.studentLevel || '',
                                      search: this.itemSearch || '',
                                  };
                                  if (this.selectedItemIds.length) {
                                      params.checked_ids = this.selectedItemIds.join(',');
                                  }
                                  this.itemError = '';
                                  axios.get('{{ route('distribution.distribution-schedule.fetch-items') }}', { params })
                                      .then(res => this.itemHtml = res.data.html)
                                      .catch(err => {
                                          this.itemHtml = '';
                                          this.itemError = 'Gagal memuat item (status ' + (err.response?.status ?? 'network') + '). Muat ulang halaman atau coba lagi.';
                                      });
                              },
                              onItemChange(event) {
                                  const cb = event.target;
                                  if (cb.name !== 'item_ids[]') return;
                                  const val = String(cb.value);
                                  const idx = this.selectedItemIds.indexOf(val);
                                  if (cb.checked && idx === -1) this.selectedItemIds.push(val);
                                  if (!cb.checked && idx !== -1) this.selectedItemIds.splice(idx, 1);
                              }
                          }">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Schedule Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $distributionSchedule->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="period" :value="__('Period')" />
                                <x-text-input id="period" name="period" type="month" class="mt-1 block w-full" :value="old('period', $distributionSchedule->period)" />
                                <x-input-error :messages="$errors->get('period')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="student_level" :value="__('Student Level')" :required="true" />
                                <select id="student_level" name="student_level" x-model="studentLevel"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Student Levels</option>
                                    @foreach($studentLevels as $st)
                                        <option value="{{ $st->kode }}">{{ $st->deskripsi }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('student_level')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="faculty_id" :value="__('Faculty')" />
                                    <select id="faculty_id" name="faculty_id" x-model="facultyId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">-- All Faculties --</option>
                                        @foreach($faculties as $f)
                                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('faculty_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="study_program_id" :value="__('Study Program')" />
                                    <select id="study_program_id" name="study_program_id" x-model="prodiId"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">-- Select Study Program --</option>
                                        <option value="all">All Study Programs</option>
                                        <template x-for="sp in filteredProdi" :key="sp.value">
                                            <option x-bind:value="sp.value" x-text="sp.label"></option>
                                        </template>
                                    </select>
                                    <x-input-error :messages="$errors->get('study_program_id')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="is_active" :value="__('Active Status')" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="1" {{ old('is_active', $distributionSchedule->is_active) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !old('is_active', $distributionSchedule->is_active) ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $distributionSchedule->date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $distributionSchedule->location)" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="start_time" :value="__('Start Time')" />
                                    <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" :value="old('start_time', $distributionSchedule->start_time?->format('H:i'))" />
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="end_time" :value="__('End Time (Deadline)')" />
                                    <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" :value="old('end_time', $distributionSchedule->end_time?->format('H:i'))" />
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="session" :value="__('Session Label (Optional)')" />
                                    <x-text-input id="session" name="session" type="text" class="mt-1 block w-full" :value="old('session', $distributionSchedule->session)" placeholder="e.g. Pagi, Sesi 1" />
                                    <x-input-error :messages="$errors->get('session')" class="mt-2" />
                                </div>
                            </div>
                            
                            {{-- Items loaded via AJAX --}}
                            <div x-show="prodiId" class="md:col-span-2">
                                <x-input-label :value="__('Distributed Items')" />
                                <p class="mt-1 mb-4 text-xs text-gray-500">Select items to be distributed in this schedule.</p>

                                <input type="text" x-model="itemSearch"
                                       @input.debounce.300ms="fetchItems()"
                                       placeholder="Search items by name or code..."
                                       class="mb-4 w-full sm:w-80 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">

                                <div x-html="itemHtml" @change="onItemChange($event)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3"></div>
                                <p x-show="itemError" x-text="itemError" class="mt-2 text-sm text-red-600"></p>
                                <x-input-error :messages="$errors->get('item_ids')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                            <a href="{{ route('distribution.distribution-schedule.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
