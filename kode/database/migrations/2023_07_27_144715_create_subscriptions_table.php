<?php

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('package_id')->index()->nullable()->constrained(table: 'packages');
            $table->bigInteger('old_package_id')->index()->nullable()->constrained(table: 'packages');
            $table->mediumInteger('word_balance')->default(0);
            $table->mediumInteger('remaining_word_balance')->default(0);
            $table->mediumInteger('carried_word_balance')->default(0);
            $table->mediumInteger('total_profile')->default(0);
            $table->mediumInteger('carried_profile')->default(0);
            $table->mediumInteger('post_balance')->default(0);
            $table->mediumInteger('carried_post_balance')->default(0);
            $table->mediumInteger('remaining_post_balance')->default(0);
            $table->double('payment_amount',25,5)->default(0.0000);
            $table->text('trx_code')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status',array_values(SubscriptionStatus::toArray()))->index()->comment('Expired: 3, Running: 1, Inactive: 2');
            $table->enum('payment_status',array_values(PaymentStatus::toArray()))->index()->comment('Initiate:-1 ,Pending: 0,Complete: 1, Rejected: 2');
            $table->date('expired_at')->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
