<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $table = 'user_details';
    protected $fillable = [
        'user_id',
        'avatar',
        'birthday',
        'gender',
        'address',
        'title',
        'experience',
        'education',
        'skill',
        'cv_file_path'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
