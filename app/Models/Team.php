<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function slots(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamSlot::class);
    }

    public function pokemon(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'team_slots')->using(TeamSlot::class)->withPivot('slot', 'level', 'gender')->orderBy('slot');
    }
}
