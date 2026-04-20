<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InteractionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // App\Http\Resources\InteractionResource.php

    public function toArray($request): array
    {
        $userId = auth()->id();

        $isEmployer = $this->employer_id === $userId;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'message' => $this->message,
            'created_at' => $this->created_at->diffForHumans(),

            'type' => $isEmployer ? 'sent_request' : 'received_request',
            'role_label' => $isEmployer ? 'Bạn là Nhà tuyển dụng' : 'Bạn là Người tìm việc',

            'post' => [
                'id' => $this->post->id,
                'title' => $this->post->title,
                'content' => $this->post->content,
            ],

            // Đối tác (Người tương tác với mình)
            'partner' => [
                'id' => $isEmployer ? $this->candidate->id : $this->employer->id,
                'name' => $isEmployer ? $this->candidate->name : $this->employer->name,
                'email' => $isEmployer ? $this->candidate->email : $this->employer->email,
                'phone' => $isEmployer ? $this->candidate->phone : $this->employer->phone,
            ],

            // Chỉ hiển thị thông tin liên hệ chi tiết nếu đã accepted
            'contact_info' => $this->status === 'accepted' ? [
                'phone' => $this->post->contact_phone,
                'email' => $this->post->contact_email,
            ] : null,
        ];
    }
}
