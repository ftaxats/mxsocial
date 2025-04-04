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
        Schema::create('credit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id')->index()->constrained(table: 'subscriptions');
            $table->unsignedBigInteger('user_id')->index()->constrained(table: 'users');
            $table->string('trx_code',200)->index();
            $table->text('details')->nullable();
            $table->enum('type',array_values(TransactionType::toArray()))->index()->nullable();
            $table->mediumInteger('balance')->default(0);
            $table->mediumInteger('post_balance')->default(0);
            $table->string('remarks',155)->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_logs');
    }
};
