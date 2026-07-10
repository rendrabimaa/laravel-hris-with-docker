<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Roles (Nama Peran)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // Contoh: 'super-admin'
            $table->string('display_name');         // Contoh: 'Super Admin'
            $table->timestamps();
        });

        // 2. Tabel Master Permissions (Hak Akses Spesifik)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // Contoh: 'manage-users'
            $table->string('display_name');         // Contoh: 'Kelola User'
            $table->timestamps();
        });

        // 3. Tabel Pivot: Menghubungkan Role dengan Permission (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        // 4. Tabel Pivot: Menghubungkan User dengan Role (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
