<?php

use App\Enums\TransactionType;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('currency_id')->index()->nullable()->constrained(table: 'currencies');
            $table->double('amount',25,5)->default(0.00000);
            $table->double('post_balance',25,5)->default(0.00000);
            $table->double('charge',25,5)->default(0.00000);
            $table->double('final_amount',25,5)->default(0.00000);
            $table->string('trx_code',255)->index();
            $table->enum('trx_type',array_values(TransactionType::toArray()))->nullable()->comment('+ = plus , - = minus');
            $table->text('remarks')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
