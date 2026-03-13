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
    });

    // -------------------------
    // Save current DOM values into slots array
    // -------------------------
    function saveDomValues() {
        slotBoxes.forEach((box, i) => {
            if (slots[i]) {
                slots[i].level = box.querySelector('.slot-level').value;
                slots[i].gender = box.querySelector('.slot-gender').value;
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
        slots.push(pokemon);
        syncSlots();
    }

    function removeSlot(index) {
        saveDomValues();
        slots.splice(index, 1);
        maxWarning.classList.add('hidden');
        syncSlots();
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