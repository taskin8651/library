<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('libraries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone', 15);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('logo')->nullable();
            $table->string('stamp')->nullable();
            $table->string('banner')->nullable();
            $table->string('tagline')->nullable();
            $table->string('theme_color', 7)->default('#0d6efd');
            $table->foreignId('plan_id')->constrained('plans');
            $table->enum('status', ['pending','active','suspended','expired'])->default('pending');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('plan_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('libraries'); }
};
