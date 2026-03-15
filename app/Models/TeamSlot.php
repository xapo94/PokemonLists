<?php

namespace App\Models;

use App\Enums\PokemonGenderEnum;
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
        'level',
        'gender',
    ];

    protected $casts = [
        'gender' => PokemonGenderEnum::class,
    ];

    protected $attributes = [
        'level' => 100,
        'gender' => PokemonGenderEnum::Male->value,
    ];

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pokemon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    //  A teamslot can have up to 4 moves
    public function moves(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Move::class, 'team_slot_move')->withPivot('position')->orderBy('position');
    }
}
