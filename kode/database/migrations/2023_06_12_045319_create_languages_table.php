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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');
            $table->string('name',191)->unique();
            $table->string('code',100)->nullable();
            $table->enum('is_default',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Yes: 1, No: 0');
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1, Inactive: 0');
            $table->enum('ltr',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Yes: 1, No: 0');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
