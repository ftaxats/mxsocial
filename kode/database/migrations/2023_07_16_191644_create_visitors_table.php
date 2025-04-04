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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id()->index();
            $table->string('uid',100)->index();
            $table->foreignId('created_by')->nullable()->constrained(table: 'admins');
            $table->foreignId('updated_by')->nullable()->constrained(table: 'admins');
            $table->foreignId('country_id')->nullable();
            $table->string("ip_address")->nullable();
            $table->integer("times_visited")->default(1);
            $table->longText("agent_info")->nullable();
            $table->enum('is_blocked',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())
                                                                          ->comment('Yes: 1, No: 0');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
