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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->string('name',191)->index()->unique();
            $table->string('slug',191)->index()->unique();
            $table->longText('content')->nullable();
            $table->string('notes',255)->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->comment('Active : 1,Inactive : 0');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
