<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAudit extends Model
{
    protected $fillable = ['action', 'actor', 'reason', 'snapshot'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }
}
