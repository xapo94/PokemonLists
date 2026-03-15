<?php

use App\Models\Move;
use App\Models\Pokemon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('move_pokemon', function (Blueprint $table) {
            $table->foreignIdFor(Move::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Pokemon::class)->constrained()->cascadeOnDelete();
            $table->primary(['move_id', 'pokemon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('move_pokemon');
    }
};
