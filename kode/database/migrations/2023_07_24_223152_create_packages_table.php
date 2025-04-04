<?php

use App\Enums\PlanDuration;
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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('updated_by')->index()->nullable()->constrained(table: 'admins');
            $table->string('title',191)->index()->unique();
            $table->string('icon',191)->nullable();
            $table->string('slug',191)->index()->unique();
            $table->enum('duration',array_values(PlanDuration::toArray()))->comment('MONTHLY = 1; YEARLY = 2; UNLIMITED = -1');
            $table->double('price',25, 2)->default(0.00);
            $table->double('discount_price',25, 2)->default(0.00);
            $table->double('total_subscription_income', 25,5)->nullable()->default(0.00000);
            $table->longText('social_access')->nullable();
            $table->longText('ai_configuration')->nullable();
            $table->longText('template_access')->nullable();
            $table->text('description')->nullable();
            $table->enum('status',array_values(StatusEnum::toArray()))->default(StatusEnum::true->status())->comment('Active: 1, Inactive: 0');
            $table->enum('is_recommended',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->enum('is_feature',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1,No: 0');
            $table->enum('is_free',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('Yes: 1, No: 0');
            $table->double('affiliate_commission',25, 2)->default(0.00000);
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
