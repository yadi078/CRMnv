<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (! Schema::hasColumn('reminders', 'last_recurring_notify_at')) {
                $table->timestamp('last_recurring_notify_at')->nullable()->after('notification_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (Schema::hasColumn('reminders', 'last_recurring_notify_at')) {
                $table->dropColumn('last_recurring_notify_at');
            }
        });
    }
};
