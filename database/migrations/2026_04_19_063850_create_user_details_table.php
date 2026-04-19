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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            // Liên kết 1-1 với bảng users
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // Thông tin cá nhân bổ sung
            $table->string('avatar')->nullable(); // Ảnh đại diện
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('address')->nullable();

            // Hồ sơ năng lực
            $table->string('title')->nullable(); // Ví dụ: Lập trình viên PHP, Kế toán trưởng...
            $table->text('experience')->nullable(); // Kinh nghiệm làm việc
            $table->text('education')->nullable();  // Học vấn
            $table->text('skill')->nullable();     // Các kỹ năng

            // File đính kèm
            $table->string('cv_file_path')->nullable(); // Đường dẫn file PDF hồ sơ

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
