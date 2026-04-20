<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCreditScore extends Model
{
    use HasFactory;

    protected $table = 'user_credit_scores';
    protected $fillable = [
        'user_id',
        'score_change',
        'current_total_score',
        'reason',
        'source_id',
        'source_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
