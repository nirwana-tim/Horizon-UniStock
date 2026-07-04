<?php

namespace App\Services\Master;

use App\Models\ProgramLevel;
use App\Services\AuditService;

class ProgramLevelService
{
    public function store(array $data): ProgramLevel
    {
        $level = ProgramLevel::create($data);
        AuditService::log('create', 'program_level', $level->id, null, $data);
        return $level;
    }

    public function update(ProgramLevel $level, array $data): ProgramLevel
    {
        $old = $level->toArray();
        $level->update($data);
        AuditService::log('update', 'program_level', $level->id, $old, $level->fresh()->toArray());
        return $level;
    }

    public function destroy(ProgramLevel $level): void
    {
        AuditService::log('delete', 'program_level', $level->id, $level->toArray(), null);
        $level->delete();
    }
}
