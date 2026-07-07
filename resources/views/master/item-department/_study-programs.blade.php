<div class="ml-6 space-y-1">
    @foreach($faculty->studyPrograms as $prodi)
        <label class="flex items-center">
            <input type="checkbox" name="study_program_ids[]" value="{{ $prodi->id }}" class="prodi-check rounded border-gray-300 text-primary-600 focus:ring-primary-500" data-faculty="{{ $faculty->id }}" {{ in_array($prodi->id, $selectedIds) ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-600">{{ $prodi->name }}</span>
        </label>
    @endforeach
</div>