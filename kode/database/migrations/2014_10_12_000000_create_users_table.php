<?php

use App\Enums\StatusEnum;
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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_id')->index()->nullable()->constrained(table: 'users');
            $table->mediumInteger('referral_code')->index()->nullable();
            $table->bigInteger('auto_subscription_by')->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('country_id')->index()->nullable()->constrained(table: 'countries');
            $table->string('uid',100)->index()->nullable();
            $table->string('o_auth_id',255)->nullable();
            $table->string('name',255)->index();
            $table->string('username',191)->index()->nullable()->unique();
            $table->string('phone',191)->nullable()->index()->unique();
            $table->double('balance',20,2)->default(0.00);
            $table->string('email',191)->index()->unique();
            $table->longText('notification_settings')->nullable();
            $table->longText('settings')->nullable();
            $table->longText('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status',[array_values(StatusEnum::toArray())])->index()->default(StatusEnum::true->status())->comment('Active: 1, Deactive: 0');
            $table->enum('auto_subscription', [array_values(StatusEnum::toArray())])->default(StatusEnum::false->status())->comment('Off: 0, On: 1');
            $table->enum('is_kyc_verified',[array_values(StatusEnum::toArray())])->index()->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->longText('custom_data')->nullable();
            $table->string('password',255)->nullable();
            $table->string('webhook_api_key',255)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
