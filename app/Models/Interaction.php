<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $table = 'interactions';
    protected $fillable = [
        'candidate_id',
        'employer_id',
        'guest_name',
        'guest_contact',
        'message',
        'status'
    ];

    // Lấy thông tin người tìm việc (Bạn A)
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    // Lấy thông tin nhà tuyển dụng (nếu có tài khoản)
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }
}
