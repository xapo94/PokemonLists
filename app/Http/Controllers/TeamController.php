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
            $slot['pokemon_id'] => ['slot' => $slot['slot']],
        ]);

        $team->pokemon()->attach($attachData);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);

        $team->load('pokemon');

        $slots = $team->pokemon->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'level' => $p->pivot->level ?? 100,
            'gender' => $p->pivot->gender instanceof \App\Enums\PokemonGenderEnum ? $p->pivot->gender->value : ($p->pivot->gender ?? 'male'),
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
            $slot['pokemon_id'] => ['slot' => $slot['slot']],
        ]);

        $team->pokemon()->sync($syncData);

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }
}
