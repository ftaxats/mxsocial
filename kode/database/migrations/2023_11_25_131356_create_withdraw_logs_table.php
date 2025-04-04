<?php

use App\Enums\WithdrawStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('withdraw_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('method_id')->index()->nullable()->constrained(table: 'withdraws');
            $table->unsignedBigInteger('currency_id')->index()->nullable()->constrained(table: 'currencies');
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->string('trx_code',255)->index()->nullable();
            $table->double('base_amount',25,5)->default(0.00000);
            $table->double('amount',25,5)->default(0.00000);
            $table->double('base_charge',25,5)->default(0.00000);
            $table->double('charge',25,5)->default(0.00000);
            $table->double('base_final_amount',25,5)->default(0.00000);
            $table->double('final_amount',25,5)->default(0.00000);
            $table->longText('custom_data')->nullable();
            $table->enum('status',array_values(WithdrawStatus::toArray()))->index()->comment('Pending : 3,Approved : 1 ,Rejected:2');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_logs');
    }
};
