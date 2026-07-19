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
                              programLevelId: '{{ old('program_level_id', $distributionSchedule->program_level_id) }}',
                              facultyId: '{{ old('faculty_id', $distributionSchedule->faculty_id) }}',
                              prodiId: '{{ old('study_program_id', $distributionSchedule->study_program_id ?? 'all') }}',
                              studentType: '{{ old('student_type', $distributionSchedule->student_type) }}',
                              prodiByFaculty: {{ json_encode($prodiByFaculty) }},
                              allProdi: {{ json_encode($allProdi) }},
                              itemHtml: '',
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
                                  this.$watch('programLevelId', () => { if (this.prodiId) this.fetchItems(); });
                                  this.$watch('facultyId', () => { if (this.prodiId) this.fetchItems(); });
                                  this.$watch('studentType', () => { if (this.prodiId) this.fetchItems(); });
                                  if (this.prodiId) this.fetchItems();
                              },
                              fetchItems() {
                                  let params = {
                                      program_level_id: this.programLevelId || '',
                                      faculty_id: this.facultyId || '',
                                      study_program_id: this.prodiId,
                                      student_type: this.studentType || '',
                                  };
                                  if (this.selectedItemIds.length) {
                                      params.checked_ids = this.selectedItemIds.join(',');
                                  }
                                  axios.get('{{ route('distribution.distribution-schedule.fetch-items') }}', { params })
                                      .then(res => this.itemHtml = res.data.html);
                              }
                          }">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="stage_id" :value="__('Stage')" :required="true" />
                                <select id="stage_id" name="stage_id" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">-- Select Stage --</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}" {{ old('stage_id', $distributionSchedule->stage_id) == $stage->id ? 'selected' : '' }}>{{ $stage->name }} (Tahap {{ $stage->stage_order }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('stage_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Schedule Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $distributionSchedule->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div @change="programLevelId = $event.target.value">
                                <x-input-label for="program_level_id" :value="__('Program Level')" />
                                <select id="program_level_id" name="program_level_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">-- All Levels --</option>
                                    @foreach($programLevels as $level)
                                        <option value="{{ $level->id }}" {{ old('program_level_id', $distributionSchedule->program_level_id) == $level->id ? 'selected' : '' }}>{{ $level->label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('program_level_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="semester" :value="__('Semester')" />
                                <select id="semester" name="semester" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                                    <option value="">-- Select Semester --</option>
                                    <option value="Ganjil" {{ old('semester', $distributionSchedule->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester', $distributionSchedule->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="student_type" :value="__('Student Type')" :required="true" />
                                <select id="student_type" name="student_type" x-model="studentType"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">All Student Types</option>
                                    <option value="year_1_sem_1">Year 1 Sem 1</option>
                                    <option value="continuing">Continuing</option>
                                </select>
                                <x-input-error :messages="$errors->get('student_type')" class="mt-2" />
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
                            <div>
                                <x-input-label for="session" :value="__('Session / Time')" />
                                <x-text-input id="session" name="session" type="text" class="mt-1 block w-full" :value="old('session', $distributionSchedule->session)" required />
                                <x-input-error :messages="$errors->get('session')" class="mt-2" />
                            </div>
                            
                            {{-- Items loaded via AJAX --}}
                            <div x-show="prodiId" class="md:col-span-2">
                                <x-input-label :value="__('Distributed Items')" />
                                <p class="mt-1 mb-4 text-xs text-gray-500">Select items to be distributed in this schedule.</p>
                                
                                <div x-html="itemHtml" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3"></div>
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
