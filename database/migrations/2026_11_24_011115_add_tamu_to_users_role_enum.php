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
        // Add 'tamu' to the role enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'mekanik', 'owner', 'tamu')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'tamu' from the role enum column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'mekanik', 'owner')");
    }
};
