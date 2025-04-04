<?php

use App\Enums\AccountType;
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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('platform_id')->index()->constrained(table: 'media_platforms');
            $table->unsignedBigInteger('subscription_id')->index()->nullable()->constrained(table: 'subscriptions');
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->string('name',155)->index()->nullable();
            $table->string('account_id',191)->index()->nullable();
            $table->text('account_information')->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->index()->default(StatusEnum::true->status())->comment('Disconnected: 0, Connected: 1');
            $table->enum('is_official',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Yes: 1, No: 1');
            $table->enum('is_connected',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Yes: 1, No: 1');
            $table->enum('account_type',array_values(AccountType::toArray()))->comment('Profile: 0, Page: 1 ,Group:2');
            $table->string('details',255)->nullable();

            $table->text('token');
            $table->dateTime('access_token_expire_at')->nullable();

            $table->text('refresh_token')->nullable();
            $table->dateTime('refresh_token_expire_at')->nullable();

            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
