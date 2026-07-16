<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->constrained('libraries')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_mode', ['cash','upi','bank','other'])->default('cash');
            $table->string('upi_ref')->nullable();
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->date('valid_from');
            $table->date('valid_till');
            $table->string('collected_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fee_payments'); }
};
