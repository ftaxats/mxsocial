<?php

use App\Enums\MenuVisibilty;
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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('serial_id')->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable();
            $table->unsignedBigInteger('updated_by')->index()->nullable();
            $table->string('name',255)->index();
            $table->string('url',255);
            $table->longText('section')->nullable();
            $table->enum('menu_visibility', array_values(MenuVisibilty::toArray()))->default(MenuVisibilty::BOTH->value)->comment('Header: 0, Footer: 1, Both: 2');
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1,Inactive: 0');   
            $table->enum('is_default',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1,No: 0');
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
