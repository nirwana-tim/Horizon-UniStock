<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('program_levels')->where('code', 'ANG-2024')->update(['code' => '2425']);
        DB::table('program_levels')->where('code', 'ANG-2025')->update(['code' => '2526']);
        DB::table('program_levels')->where('code', 'ANG-2026')->update(['code' => '2627']);
    }

    public function down(): void
    {
        DB::table('program_levels')->where('code', '2425')->update(['code' => 'ANG-2024']);
        DB::table('program_levels')->where('code', '2526')->update(['code' => 'ANG-2025']);
        DB::table('program_levels')->where('code', '2627')->update(['code' => 'ANG-2026']);
    }
};
