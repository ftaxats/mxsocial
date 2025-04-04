<?php

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
        Schema::create('template_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->index()->nullable()->constrained(table: 'ai_templates');
            $table->unsignedBigInteger('user_id')->index()->nullable()->constrained(table: 'users');
            $table->unsignedBigInteger('admin_id')->index()->nullable()->constrained(table: 'admins');
            $table->unsignedBigInteger('package_id')->index()->nullable()->constrained(table: 'packages');
            $table->longText('content')->nullable();
            $table->integer('total_words')->default(0);
            $table->longText('open_ai_usage')->nullable();
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_usages');
    }
};
