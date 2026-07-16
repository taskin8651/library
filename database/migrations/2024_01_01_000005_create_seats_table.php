<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->constrained('libraries')->onDelete('cascade');
            $table->string('seat_number');
            $table->string('row_label')->nullable();
            $table->enum('type', ['regular','cabin','vip'])->default('regular');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('seats'); }
};
