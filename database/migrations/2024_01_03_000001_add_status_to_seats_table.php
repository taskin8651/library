<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->enum('status', ['available', 'reserved', 'maintenance'])->default('available')->after('is_active');
        });
    }
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
