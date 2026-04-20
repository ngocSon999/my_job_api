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
        Schema::create('user_credit_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->integer('score_change');

            $table->integer('current_total_score');

            $table->string('reason')->nullable();

            $table->unsignedBigInteger('source_id')->nullable(); //ID của bản ghi gây ra sự thay đổi (ví dụ: id của bảng interactions).
            $table->string('source_type')->nullable(); //Tên Model của bản ghi đó

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credit_scores');
    }
};
