document.addEventListener('DOMContentLoaded', () => {

    // -------------------------
    // Elements
    // -------------------------
    const builder = document.getElementById('team-builder');
    const searchInput = document.getElementById('team_pokemon_search');
    const resultsDiv = document.getElementById('team_pokemon_results');
    const slotsDiv = document.getElementById('team_slots');
    const maxWarning = document.getElementById('team_max_warning');
    const duplicateWarning = document.getElementById('team_duplicate_warning');

    if (!searchInput) return;

    // -------------------------
    // State
    // -------------------------
    let slots = [];
    let timeout;
    let draggedIndex = null;

    // -------------------------
    // Init
    // -------------------------
    const existing = JSON.parse(builder.dataset.pokemon || '[]');
    existing.forEach(p => slots.push(p));
    renderSlots();

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
    });

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

        maxWarning.classList.add('hidden');
        slots.push(pokemon);
        renderSlots();
    }

    function removeSlot(index) {
        slots.splice(index, 1);
        maxWarning.classList.add('hidden');
        renderSlots();
    }

    // -------------------------
    // Drag and Drop
    // -------------------------
    function getBoxFromTouch(touch) {
        const el = document.elementFromPoint(touch.clientX, touch.clientY);
        return el ? el.closest('[data-index]') : null;
    }

    function applyDragEvents(box, index) {
        box.addEventListener('dragstart', () => {
            draggedIndex = index;
            setTimeout(() => box.classList.add('opacity-50'), 0);
        });

        box.addEventListener('dragend', () => {
            draggedIndex = null;
            box.classList.remove('opacity-50');
        });

        box.addEventListener('dragover', (e) => e.preventDefault());

        box.addEventListener('drop', () => {
            if (draggedIndex === null || draggedIndex === index) return;
            const dragged = slots.splice(draggedIndex, 1)[0];
            slots.splice(index, 0, dragged);
            renderSlots();
        });

        box.addEventListener('touchstart', () => {
            draggedIndex = index;
            box.classList.add('opacity-50');
        }, { passive: true });

        box.addEventListener('touchmove', (e) => e.preventDefault(), { passive: false });

        box.addEventListener('touchend', (e) => {
            const touch = e.changedTouches[0];
            const target = getBoxFromTouch(touch);

            if (target && target !== box) {
                const targetIndex = parseInt(target.dataset.index);
                if (!isNaN(targetIndex) && targetIndex !== draggedIndex) {
                    const dragged = slots.splice(draggedIndex, 1)[0];
                    slots.splice(targetIndex, 0, dragged);
                    renderSlots();
                }
            }

            draggedIndex = null;
            box.classList.remove('opacity-50');
        });
    }

    // -------------------------
    // Render
    // -------------------------
    function renderSlots() {
        slotsDiv.innerHTML = '';

        slots.forEach((pokemon, index) => {
            const box = document.createElement('div');
            box.className = 'relative flex flex-col items-center border border-border rounded-lg p-2 w-20 cursor-grab';
            box.dataset.index = index;
            box.draggable = true;

            box.innerHTML = `
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png" class="h-12 w-12 object-contain pointer-events-none" />
                <p class="text-xs text-center truncate w-full pointer-events-none">${pokemon.name}</p>
                <input type="hidden" name="pokemon_slots[${index}][pokemon_id]" value="${pokemon.id}" />
                <input type="hidden" name="pokemon_slots[${index}][slot]" value="${index + 1}" />
                <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center pointer-events-auto">×</button>
            `;

            box.querySelector('button').addEventListener('click', (e) => {
                e.stopPropagation();
                removeSlot(index);
            });

            applyDragEvents(box, index);
            slotsDiv.appendChild(box);
        });
    }
});