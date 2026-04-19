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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();

            // ID của người tìm việc được chọn (Bạn A)
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');

            // ID của nhà tuyển dụng nếu họ có đăng nhập
            $table->foreignId('employer_id')->nullable()->constrained('users')->onDelete('set null');


            $table->string('guest_name')->nullable();
            $table->string('guest_contact')->nullable();

            // Nội dung lời mời hoặc mô tả công việc muốn thuê
            $table->text('message')->nullable();

            // Trạng thái kết nối
            // pending: Chờ A đồng ý, accepted: A đồng ý, rejected: A từ chối
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
