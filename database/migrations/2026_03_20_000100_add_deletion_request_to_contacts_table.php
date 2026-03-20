<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('deletion_pending')->default(false)->after('approved_at');
            $table->foreignId('deletion_requested_by')
                ->nullable()
                ->after('deletion_pending')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('deletion_requested_at')->nullable()->after('deletion_requested_by');
            $table->text('deletion_reason')->nullable()->after('deletion_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['deletion_requested_by']);
            $table->dropColumn([
                'deletion_pending',
                'deletion_requested_by',
                'deletion_requested_at',
                'deletion_reason',
            ]);
        });
    }
};
