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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('serial_id')->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');;
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');;
            $table->string('title',191)->nullable();
            $table->string('slug',191)->nullable();
            $table->longText("description");
            $table->string('meta_title',155)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1,Inactive: 0'); 
            $table->enum('show_in_header',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1,No: 0');   
            $table->enum('show_in_footer',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1,No: 0');  
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
