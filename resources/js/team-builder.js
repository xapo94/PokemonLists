document.addEventListener('DOMContentLoaded', () => {

    // -------------------------
    // Elements
    // -------------------------
    const builder = document.getElementById('team-builder');
    const searchInput = document.getElementById('team_pokemon_search');
    const resultsDiv = document.getElementById('team_pokemon_results');
    const slotsDiv = document.getElementById('team_slots');
    const slotBoxes = slotsDiv.querySelectorAll('.slot-box');
    const maxWarning = document.getElementById('team_max_warning');
    const duplicateWarning = document.getElementById('team_duplicate_warning');

    if (!searchInput) return;

    // -------------------------
    // State
    // -------------------------
    let slots = [];
    let timeout;
    let draggedIndex = null;
    let activeMoveBox = null;

    // -------------------------
    // Init
    // -------------------------
    const existing = JSON.parse(builder.dataset.pokemon || '[]');
    existing.forEach(p => slots.push(p));
    syncSlots();

    // -------------------------
    // Search
    // -------------------------
    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const term = this.value.trim();

        if (!term) {
            resultsDiv.innerHTML = '';
            resultsDiv.classList.add('hidden');
            return;
        }

        timeout = setTimeout(async () => {
            try {
                const res = await fetch(`/pokemons/search?search=${encodeURIComponent(term)}`);
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();

                resultsDiv.innerHTML = '';

                if (data.length === 0) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                resultsDiv.classList.remove('hidden');

                data.forEach(pokemon => {
                    const div = document.createElement('div');
                    div.textContent = pokemon.name;
                    div.className = 'px-2 py-1 hover:bg-gray-100 cursor-pointer text-sm';
                    div.addEventListener('click', () => {
                        addSlot(pokemon);
                        searchInput.value = '';
                        resultsDiv.classList.add('hidden');
                    });
                    resultsDiv.appendChild(div);
                });
            } catch (error) {
                console.error('Error fetching Pokémon:', error);
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!resultsDiv.contains(e.target) && e.target !== searchInput) {
            resultsDiv.classList.add('hidden');
        }

        if (activeMoveBox && !activeMoveBox.contains(e.target)) {
            closeMoveResults();
        }
    });

    // -------------------------
    // Save current DOM values into slots array
    // -------------------------
    function saveDomValues() {
        slotBoxes.forEach((box, i) => {
            if (slots[i]) {
                slots[i].level = box.querySelector('.slot-level').value;
                slots[i].gender = box.querySelector('.slot-gender').value;
                slots[i].moves = Array.from(box.querySelectorAll('.slot-move-id')).map((input, m) => ({
                    id: input.value || null,
                    name: box.querySelectorAll('.slot-move-box')[m].dataset.moveName || null,
                }));
            }
        });
    }

    // -------------------------
    // Slot Management
    // -------------------------
    function addSlot(pokemon) {
        if (slots.length >= 6) {
            maxWarning.classList.remove('hidden');
            return;
        }

        if (slots.find(s => s.id === pokemon.id)) {
            duplicateWarning.classList.remove('hidden');
            setTimeout(() => duplicateWarning.classList.add('hidden'), 2000);
            return;
        }

        saveDomValues();
        maxWarning.classList.add('hidden');
        slots.push({ ...pokemon, moves: [null, null, null, null] });
        syncSlots();
    }

    function removeSlot(index) {
        saveDomValues();
        slots.splice(index, 1);
        maxWarning.classList.add('hidden');
        syncSlots();
    }

    // -------------------------
    // Get all selected move IDs across all slots
    // -------------------------
    function getAllSelectedMoveIds() {
        return slots.flatMap(s => (s.moves || []).map(m => m ? m.id : null)).filter(Boolean);
    }

    // -------------------------
    // Move Selection
    // -------------------------
    function closeMoveResults() {
        if (activeMoveBox) {
            const results = activeMoveBox.querySelector('.slot-move-results');
            if (results) results.classList.add('hidden');
            activeMoveBox = null;
        }
    }

    function setMoveSelected(moveBox, moveInput, move) {
        moveBox.textContent = move.name;
        moveBox.dataset.moveName = move.name;
        moveBox.classList.remove('border-dashed', 'border-zinc-300', 'text-zinc-400');
        moveBox.classList.add('border-solid', 'border-zinc-500', 'text-zinc-900');
        moveInput.value = move.id;
    }

    function setMoveEmpty(moveBox, moveInput) {
        moveBox.textContent = '+ Add Move';
        moveBox.dataset.moveName = '';
        moveBox.classList.remove('border-solid', 'border-zinc-500', 'text-zinc-900');
        moveBox.classList.add('border-dashed', 'border-zinc-300', 'text-zinc-400');
        moveInput.value = '';
    }

    function wireMoveBoxes(box, slotIndex) {
        const moveBoxes = box.querySelectorAll('.slot-move-box');
        const moveResults = box.querySelector('.slot-move-results');

        moveBoxes.forEach((moveBox, moveIndex) => {
            // Stop touch events from bubbling to slot box drag handlers
            moveBox.addEventListener('touchstart', (e) => e.stopPropagation(), { passive: true });
            moveBox.addEventListener('touchmove', (e) => e.stopPropagation(), { passive: true });
            moveBox.addEventListener('touchend', (e) => e.stopPropagation(), { passive: true });

            moveBox.addEventListener('click', async (e) => {
                e.stopPropagation();

                closeMoveResults();

                const pokemonId = slots[slotIndex]?.id;
                if (!pokemonId) return;

                activeMoveBox = box;

                try {
                    const res = await fetch(`/pokemons/${pokemonId}/moves`);
                    if (!res.ok) throw new Error('Network error');
                    const allMoves = await res.json();

                    moveResults.innerHTML = '';
                    moveResults.classList.remove('hidden');

                    const moveSearchInput = document.createElement('input');
                    moveSearchInput.type = 'text';
                    moveSearchInput.placeholder = 'Search move...';
                    moveSearchInput.className = 'w-full px-2 py-1 text-sm border-b border-border outline-none sticky top-0 bg-white';
                    moveResults.appendChild(moveSearchInput);

                    const renderMoves = (filter = '') => {
                        Array.from(moveResults.children).slice(1).forEach(el => el.remove());

                        const selectedMoveIds = getAllSelectedMoveIds();

                        allMoves
                            .filter(move => !selectedMoveIds.includes(move.id))
                            .filter(move => move.name.includes(filter.toLowerCase()))
                            .forEach(move => {
                                const div = document.createElement('div');
                                div.textContent = move.name;
                                div.className = 'px-2 py-1 hover:bg-gray-100 cursor-pointer text-sm';
                                div.addEventListener('click', (e) => {
                                    e.stopPropagation();

                                    const moveInputs = box.querySelectorAll('.slot-move-id');
                                    setMoveSelected(moveBox, moveInputs[moveIndex], move);

                                    if (!slots[slotIndex].moves) slots[slotIndex].moves = [null, null, null, null];
                                    slots[slotIndex].moves[moveIndex] = { id: move.id, name: move.name };

                                    closeMoveResults();
                                });
                                moveResults.appendChild(div);
                            });
                    };

                    renderMoves();

                    moveSearchInput.addEventListener('input', (e) => {
                        e.stopPropagation();
                        renderMoves(e.target.value.trim());
                    });

                    moveSearchInput.addEventListener('click', (e) => e.stopPropagation());

                    setTimeout(() => moveSearchInput.focus(), 0);

                } catch (error) {
                    console.error('Error fetching moves:', error);
                }
            });
        });
    }

    // -------------------------
    // Sync slots array to blade boxes
    // -------------------------
    function syncSlots() {
        slotBoxes.forEach((box, i) => {
            const pokemon = slots[i];
            const pokemonIdInput = box.querySelector('.slot-pokemon-id');
            const slotNumberInput = box.querySelector('.slot-number');

            if (pokemon) {
                box.querySelector('.slot-image').src = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png`;
                box.querySelector('.slot-name').textContent = pokemon.name;
                if (pokemon.level) box.querySelector('.slot-level').value = pokemon.level;
                if (pokemon.gender) box.querySelector('.slot-gender').value = pokemon.gender;
                pokemonIdInput.value = pokemon.id;
                pokemonIdInput.disabled = false;
                slotNumberInput.value = i + 1;
                slotNumberInput.disabled = false;
                box.classList.remove('hidden');

                const moveBoxes = box.querySelectorAll('.slot-move-box');
                const moveInputs = box.querySelectorAll('.slot-move-id');
                const moves = pokemon.moves || [null, null, null, null];

                moves.forEach((move, m) => {
                    if (move && move.id) {
                        setMoveSelected(moveBoxes[m], moveInputs[m], move);
                    } else {
                        setMoveEmpty(moveBoxes[m], moveInputs[m]);
                    }
                });

            } else {
                box.querySelector('.slot-image').src = '';
                box.querySelector('.slot-name').textContent = '';
                pokemonIdInput.value = '';
                pokemonIdInput.disabled = true;
                slotNumberInput.value = i + 1;
                slotNumberInput.disabled = true;
                box.classList.add('hidden');
            }
        });

        wireRemoveButtons();
        wireDragEvents();
        slotBoxes.forEach((box, i) => wireMoveBoxes(box, i));
    }

    // -------------------------
    // Wire Remove Buttons
    // -------------------------
    function wireRemoveButtons() {
        slotBoxes.forEach((box, i) => {
            const btn = box.querySelector('.slot-remove');
            btn.onclick = (e) => {
                e.stopPropagation();
                removeSlot(i);
            };
        });
    }

    // -------------------------
    // Drag and Drop
    // -------------------------
    function getBoxFromTouch(touch) {
        const el = document.elementFromPoint(touch.clientX, touch.clientY);
        return el ? el.closest('.slot-box') : null;
    }

    function wireDragEvents() {
        slotBoxes.forEach((box, index) => {
            if (!slots[index]) {
                box.draggable = false;
                box.ondragstart = null;
                box.ondragend = null;
                box.ondragover = null;
                box.ondrop = null;
                box.ontouchstart = null;
                box.ontouchmove = null;
                box.ontouchend = null;
                return;
            }

            box.draggable = true;

            box.ondragstart = () => {
                draggedIndex = index;
                setTimeout(() => box.classList.add('opacity-50'), 0);
            };

            box.ondragend = () => {
                draggedIndex = null;
                box.classList.remove('opacity-50');
            };

            box.ondragover = (e) => e.preventDefault();

            box.ondrop = () => {
                if (draggedIndex === null || draggedIndex === index) return;
                saveDomValues();
                const temp = slots[draggedIndex];
                slots[draggedIndex] = slots[index];
                slots[index] = temp;
                syncSlots();
            };

            box.ontouchstart = () => {
                draggedIndex = index;
                box.classList.add('opacity-50');
            };

            box.ontouchmove = (e) => e.preventDefault();

            box.ontouchend = (e) => {
                const touch = e.changedTouches[0];
                const target = getBoxFromTouch(touch);

                if (target && target !== box) {
                    const targetIndex = parseInt(target.dataset.index);
                    if (!isNaN(targetIndex) && targetIndex !== draggedIndex) {
                        saveDomValues();
                        const temp = slots[draggedIndex];
                        slots[draggedIndex] = slots[targetIndex];
                        slots[targetIndex] = temp;
                        syncSlots();
                    }
                }

                draggedIndex = null;
                box.classList.remove('opacity-50');
            };
        });
    }
});