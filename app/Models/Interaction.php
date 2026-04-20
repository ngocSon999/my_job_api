<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $table = 'interactions';
    protected $fillable = [
        'post_id',
        'candidate_id',
        'employer_id',
        'message',
        'status'
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // Lấy thông tin người tìm việc
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
