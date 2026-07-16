<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('target_audience', ['all', 'active', 'expiring'])->default('all')->after('type');
            $table->timestamp('scheduled_at')->nullable()->after('target_audience');
        });
    }
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['target_audience', 'scheduled_at']);
        });
    }
};
