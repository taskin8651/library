<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->constrained('libraries')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('seat_id')->nullable()->constrained('seats')->nullOnDelete();
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('attendance'); }
};
