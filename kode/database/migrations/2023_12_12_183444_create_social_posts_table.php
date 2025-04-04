<?php

use App\Enums\PostStatus;
use App\Enums\PostType;
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
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id()->index();
            $table->string('uid',100)->index()->nullable();
            $table->unsignedBigInteger('account_id')->index()->constrained(table: 'social_accounts');
            $table->unsignedBigInteger('platform_id')->index()->nullable();
            $table->unsignedBigInteger('subscription_id')->index()->nullable()->constrained(table: 'subscription');
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->longText('content')->nullable();
            $table->longText('link')->nullable();
            $table->longText('platform_response')->nullable();
            $table->enum('is_scheduled',array_values(StatusEnum::toArray()))->index()->default(StatusEnum::false->status())->comment('No: 0, Yes: 1');
            $table->timestamp('schedule_time')->nullable();
            $table->mediumInteger("repeat_every")->default(0)->comment('In minutes');
            $table->timestamp('repeat_schedule_end_date')->nullable();
            $table->enum('is_draft',array_values(StatusEnum::toArray()))->default(StatusEnum::false->status())->comment('No: 0, Yes: 1');
            $table->enum('status',array_values(PostStatus::toArray()))->index()->comment('Pending: 0, Success: 1 ,Failed:2,Schedule:3');
            $table->enum('post_type',array_values(PostType::toArray()))->comment('FEED: 0 ,Story:2,REELS:1');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
