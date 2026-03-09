<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleParticipant extends Model
{
    protected $fillable = ['sale_id', 'nombre', 'email', 'orden'];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
