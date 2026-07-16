<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->nullable()->constrained('libraries')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password');
            $table->enum('role', ['superadmin','owner','staff','student'])->default('student');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
