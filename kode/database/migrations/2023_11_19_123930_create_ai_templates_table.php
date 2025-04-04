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
        Schema::create('ai_templates', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('category_id')->index()->nullable()->constrained(table: 'categories');
            $table->unsignedBigInteger('sub_category_id')->index()->nullable()->constrained(table: 'categories');
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->string('name',191)->index()->unique();
            $table->string('slug',191)->index()->unique();
            $table->string('icon',100);
            $table->text('description');
            $table->longText('prompt_fields')->nullable();
            $table->text('custom_prompt')->nullable();
            $table->integer('total_words')->default(0);
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->index()->comment('Active : 1,Inactive : 0');
            $table->enum('is_default',array_values(StatusEnum::toArray()))->index()->comment('Yes : 1,No : 0');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_templates');
    }
};
