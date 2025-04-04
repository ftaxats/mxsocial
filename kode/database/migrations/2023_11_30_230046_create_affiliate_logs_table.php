<?php

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
        Schema::create('affiliate_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('referred_to')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('subscription_id')->index()->nullable()->constrained(table: 'subscriptions');
            $table->double('commission_amount',25,5)->default(0.00000);
            $table->double('commission_rate',25,5)->default(0.00000);
            $table->string('trx_code',200);
            $table->string('note',255)->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_logs');
    }
};
