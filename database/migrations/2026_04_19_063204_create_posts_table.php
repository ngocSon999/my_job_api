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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('title');
            $table->text('content');

            // Hình thức & Thời gian
            $table->string('job_type'); // full-time, part-time, freelance, seasonal
            $table->string('working_time')->nullable();

            // Thông tin liên hệ (Quan trọng cho khách vãng lai)
            $table->string('contact_name')->nullable();
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();

            // Lương và Trạng thái
            $table->decimal('salary', 15, 3)->default(0);
            $table->string('status')->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
