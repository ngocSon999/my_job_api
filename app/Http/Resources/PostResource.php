<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'working_time' => $this->working_time,

            // Xử lý lương chuyên nghiệp
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'is_negotiable' => (bool) $this->is_negotiable,
            'salary_formatted' => $this->formatSalary(),

            // Thông tin liên hệ
            'contact' => [
                'name' => $this->contact_name,
                'phone' => $this->contact_phone,
                'email' => $this->contact_email,
            ],

            'status' => $this->status,
            'views' => $this->views ?? 0,

            // Ngày tháng
            'created_at' => $this->created_at->format('d/m/Y'),
            'human_time' => $this->created_at->diffForHumans(),

            'author' => [
                'id' => $this->user_id,
                'is_owner' => $this->user_id === auth()->id(),
            ],
        ];
    }

    /**
     * Hàm hỗ trợ định dạng hiển thị lương
     */
    private function formatSalary(): string
    {
        if ($this->is_negotiable) {
            return 'Thỏa thuận';
        }

        if ($this->salary_min > 0 && $this->salary_max > 0) {
            return number_format($this->salary_min, 0, ',', '.') . ' - ' . number_format($this->salary_max, 0, ',', '.') . ' VNĐ';
        }

        if ($this->salary_min > 0) {
            return 'Từ ' . number_format($this->salary_min, 0, ',', '.') . ' VNĐ';
        }

        return 'Thỏa thuận,';
    }
}
