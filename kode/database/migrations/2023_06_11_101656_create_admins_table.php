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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('role_id')->index()->nullable()->constrained(table: 'roles');
            $table->string('username',191)->index()->unique();
            $table->string('name',100)->nullable();
            $table->string('phone',255)->index()->nullable();
            $table->string('email',191)->index()->unique();
            $table->longText('notification_settings')->nullable();
            $table->longText('permissions')->nullable();
            $table->longText('address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',255);
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1, Deactive: 0');
            $table->enum('super_admin',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
