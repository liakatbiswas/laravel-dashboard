<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Older installs were seeded with the generic placeholder title.
        DB::table('site_settings')
            ->where('site_title', 'School Management System')
            ->update(['site_title' => 'EFA Bangladesh', 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')
            ->where('site_title', 'EFA Bangladesh')
            ->update(['site_title' => 'School Management System', 'updated_at' => now()]);
    }
};
