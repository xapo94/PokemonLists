<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamSlot extends Pivot
{
    use HasFactory;

    protected $table = 'team_slots';

    protected $fillable = [
        'team_id',
        'pokemon_id',
        'slot',
    ];

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pokemon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}
