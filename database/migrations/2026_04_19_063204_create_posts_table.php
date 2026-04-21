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

            // Quan hệ: nullable nếu cho phép khách vãng lai đăng tin không cần login
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('title')->index(); // Thêm index để search tiêu đề nhanh hơn
            $table->text('content');

            // Địa điểm
            $table->string('location')->nullable(); // Ví dụ: "Quận 1, TP.HCM"
            $table->string('province_id')->nullable()->index(); // Dùng ID tỉnh thành để lọc chính xác

            // Hình thức & Thời gian
            // Dùng string nhưng nên quy định các giá trị: full-time, part-time...
            $table->string('job_type', 50)->default('full-time')->index();
            $table->string('working_time')->nullable(); // Ví dụ: "Thứ 2 - Thứ 6, 08:00 - 17:30"

            // Thông tin liên hệ
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20); // Giới hạn độ dài số điện thoại
            $table->string('contact_email')->nullable();

            // Lương: Tách ra khoảng lương để làm tính năng lọc (Filter)
            $table->unsignedBigInteger('salary_min')->default(0);
            $table->unsignedBigInteger('salary_max')->default(0);
            $table->boolean('is_negotiable')->default(false); // Lương thỏa thuận

            // Trạng thái & Thống kê
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('views')->default(0); // Đếm lượt xem tin

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
