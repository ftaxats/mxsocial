<?php

use App\Enums\PriorityStatus;
use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('tickets', function (Blueprint $table) {
            $table->id()->index();
            $table->string('uid',100)->index();
            $table->string('ticket_number',100)->index()->unique();
            $table->foreignId('user_id')->nullable()->constrained(table: 'users');
            $table->longText('ticket_data')->nullable();
            $table->string('subject')->nullable();
            $table->longText('message')->nullable();
            $table->enum('status',array_values(TicketStatus::toArray()))->default(TicketStatus::PENDING->value)->comment('Open: 1, Pending: 2, Processing: 3, Solved: 4  ,On-Hold: 5 ,Closed: 5');
            $table->enum('priority',array_values(PriorityStatus::toArray()))->default(PriorityStatus::LOW->value)->comment('Urgent: 1, High: 2, Low: 3, Medium: 4');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
