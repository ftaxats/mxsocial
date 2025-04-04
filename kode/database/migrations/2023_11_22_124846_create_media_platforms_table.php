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
        Schema::create('media_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->string("name",155)->index();
            $table->string("slug",155)->index();
            $table->string("url",255)->nullable();
            $table->string("description",255)->nullable();
            $table->longText("configuration")->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->index()->default(StatusEnum::true->status())->comment('Active: 1, Inactive: 0');
            $table->enum('is_feature',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->enum('is_integrated',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_platforms');
    }
};
