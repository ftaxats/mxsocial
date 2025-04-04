<?php

use App\Enums\DepositStatus;
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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('method_id')->index()->nullable()->constrained(table: 'payment_methods');
            $table->bigInteger('currency_id')->index()->nullable()->constrained(table: 'currencies');
            $table->double('base_amount',20,5)->default(0.00000);
            $table->double('amount',20,5)->default(0.00000);
            $table->double('base_charge',20,5)->default(0.00000);
            $table->double('charge',20,5)->default(0.00000);
            $table->double('base_rate',20,5)->default(0.00000);
            $table->double('rate',20,5)->default(0.00000);
            $table->double('base_final_amount',20,5)->default(0.00000);
            $table->double('final_amount',20,5)->default(0.00000);
            $table->string('trx_code',255)->index();;
            $table->text('custom_data')->nullable();
            $table->text('feedback')->nullable();
            $table->text('remarks')->nullable();
            $table->longText('gateway_response')->nullable();
            $table->enum('status',array_values(DepositStatus::toArray()))->comment('Paid: 1, Cancel: 2, Pening: 3, Failed: 4, Rejected: 5, Initiate: -1');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
