<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Vista de filtros guardada por el usuario.
 * Estructura de filters: [ ['field' => '', 'operator' => '', 'value' => ''], ... ]
 */
class SavedFilter extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'entity',
        'filter_logic',
        'filters',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFiltersAsSpecs(): array
    {
        return collect($this->filters ?? [])
            ->map(fn ($f) => \App\DataTransferObjects\FilterSpec::fromArray($f))
            ->filter(fn ($s) => $s->isValid())
            ->values()
            ->all();
    }
}
