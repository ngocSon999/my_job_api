<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
      'user_id',
      'title',
      'content',
      'location',
      'province_id',
      'job_type',
      'working_time',
      'contact_name',
      'contact_phone',
      'contact_email',
      'salary_min',
      'salary_max',
      'is_negotiable',
      'status',
      'views',
    ];

    protected $casts = [
      'salary' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
