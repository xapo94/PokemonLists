<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $teams = auth()->user()->teams()->with('pokemon')->get();

        return view('pokemon.teams.index', [
            'teams' => $teams,
        ]);
    }

    public function create()
    {
        return view('pokemon.teams.create');
    }

    public function store(StoreTeamRequest $request)
    {
        $validated = $request->validated();

        $team = auth()->user()->teams()->create([
            'name' => $validated['name'],
        ]);

        $attachData = collect($validated['pokemon_slots'])->mapWithKeys(fn ($slot) => [
            $slot['pokemon_id'] => [
                'slot' => $slot['slot'],
                'level' => $slot['level'],
                'gender' => $slot['gender'],
            ],
        ]);

        $team->pokemon()->attach($attachData);

        $team->load('slots');

        foreach ($validated['pokemon_slots'] as $slotData) {
            if (empty($slotData['moves'])) {
                continue;
            }

            $teamSlot = $team->slots->firstWhere('pokemon_id', $slotData['pokemon_id']);

            if ($teamSlot) {
                $movesData = collect($slotData['moves'])->mapWithKeys(fn ($moveId, $position) => [
                    $moveId => ['position' => $position + 1],
                ]);

                $teamSlot->moves()->attach($movesData);
            }
        }

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);

        $team->load('pokemon', 'slots.moves');

        $slots = $team->pokemon->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'level' => $p->pivot->level,
            'gender' => $p->pivot->gender->value,
            'moves' => $team->slots
                ->firstWhere('pokemon_id', $p->id)
                ->moves
                ->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])
                ->values()
                ->toArray(),
        ]);

        return view('pokemon.teams.edit', [
            'team' => $team,
            'slots' => $slots,
        ]);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validated();

        $team->update([
            'name' => $validated['name'],
        ]);

        $syncData = collect($validated['pokemon_slots'])->mapWithKeys(fn ($slot) => [
            $slot['pokemon_id'] => [
                'slot' => $slot['slot'],
                'level' => $slot['level'],
                'gender' => $slot['gender'],
            ],
        ]);

        $team->pokemon()->sync($syncData);

        $team->load('slots');

        foreach ($validated['pokemon_slots'] as $slotData) {
            $teamSlot = $team->slots->firstWhere('pokemon_id', $slotData['pokemon_id']);

            if ($teamSlot) {
                $movesData = collect($slotData['moves'] ?? [])->mapWithKeys(fn ($moveId, $position) => [
                    $moveId => ['position' => $position + 1],
                ]);

                $teamSlot->moves()->sync($movesData);
            }
        }

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }
}
