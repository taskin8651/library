<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->constrained('libraries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seat_id')->nullable()->constrained('seats')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->string('uid')->unique();
            $table->string('profile_photo')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('aadhar', 12)->nullable();
            $table->enum('status', ['active','inactive','expired'])->default('active');
            $table->date('plan_start_date')->nullable();
            $table->date('plan_end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('members'); }
};
