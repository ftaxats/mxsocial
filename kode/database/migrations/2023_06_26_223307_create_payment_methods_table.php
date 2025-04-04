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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->integer('serial_id')->index()->nullable();
            $table->unsignedBigInteger('currency_id')->nullable()->constrained(table: 'currencies');
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');
            $table->string("name",191)->unique();
            $table->string("code",191)->unique();
            $table->longText("parameters")->nullable();
            $table->longText("extra_parameters")->nullable();
            $table->double("percentage_charge",25, 2)->default(0.00);
            $table->double("fixed_charge",25, 2)->default(0.00);
            $table->double("minimum_amount",25, 2)->default(0.00);
            $table->double("maximum_amount",25, 2)->default(0.00);
            $table->text('note')->nullable();
            $table->text('gateway_response')->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1, Inactive: 0');   
            $table->enum('type',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Automatic: 1, Manual: 0');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
