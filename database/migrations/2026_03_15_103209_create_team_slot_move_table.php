<?php

use App\Models\Move;
use App\Models\TeamSlot;
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
        Schema::create('team_slot_move', function (Blueprint $table) {
            $table->foreignIdFor(TeamSlot::class, 'team_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Move::class)->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->primary(['team_slot_id', 'move_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_slot_move');
    }
};
