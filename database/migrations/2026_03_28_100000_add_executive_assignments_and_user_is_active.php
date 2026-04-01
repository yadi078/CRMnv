<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('approval_status');
            });
        }

        if (Schema::hasTable('companies') && ! Schema::hasColumn('companies', 'assigned_user_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->foreignId('assigned_user_id')->nullable()->after('ejecutivo_asignado')->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('contacts') && ! Schema::hasColumn('contacts', 'assigned_user_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->foreignId('assigned_user_id')->nullable()->after('company_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contacts') && Schema::hasColumn('contacts', 'assigned_user_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropColumn('assigned_user_id');
            });
        }

        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'assigned_user_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropColumn('assigned_user_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
