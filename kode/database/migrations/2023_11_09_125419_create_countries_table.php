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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('updated_by')->index()->nullable()->constrained(table: 'admins');;
            $table->string('uid',100)->index()->nullable();
            $table->string('name',155)->index()->unique();
            $table->string('code',155)->index()->unique();
            $table->string('phone_code',155)->nullable();
            $table->enum('is_blocked',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('No: 0, Yes: 1');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
