function initPokemonSearch() {
    const searchInput = document.getElementById('pokemon_search');
    const hiddenInput = document.getElementById('fav_pokemon_id');
    const resultsDivId = 'pokemon_results';

    if (!searchInput || !hiddenInput) return;

    // Create dropdown container if not exists
    let resultsDiv = document.getElementById(resultsDivId);
    if (!resultsDiv) {
        resultsDiv = document.createElement('div');
        resultsDiv.id = resultsDivId;
        resultsDiv.className = 'border rounded mt-1 bg-white max-h-40 overflow-auto hidden';
        searchInput.insertAdjacentElement('afterend', resultsDiv);
    }

    // Update input visual effect
    const updateInputEffect = () => {
        if (hiddenInput.value) {
            searchInput.classList.add('border-green-500', 'ring-2', 'ring-green-200');
        } else {
            searchInput.classList.remove('border-green-500', 'ring-2', 'ring-green-200');
        }
    };

    let timeout;

    // Input listener
    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const term = this.value.trim();

        // Reset hidden input on typing
        hiddenInput.value = '';
        updateInputEffect();

        if (!term) {
            resultsDiv.innerHTML = '';
            resultsDiv.classList.add('hidden');
            return;
        }

        // Debounce AJAX
        timeout = setTimeout(async () => {
            try {
                const res = await fetch(`/pokemons/search?search=${encodeURIComponent(term)}`);
                if (!res.ok) throw new Error('Network response was not ok');
                const data = await res.json();

                resultsDiv.innerHTML = '';

                if (data.length === 0) {
                    resultsDiv.classList.add('hidden');
                    return;
                }

                resultsDiv.classList.remove('hidden');

                // Populate dropdown
                data.forEach(pokemon => {
                    const div = document.createElement('div');
                    div.textContent = pokemon.name;
                    div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                    div.addEventListener('click', () => {
                        searchInput.value = pokemon.name;
                        hiddenInput.value = pokemon.id;
                        resultsDiv.classList.add('hidden');
                        updateInputEffect();
                    });
                    resultsDiv.appendChild(div);
                });
            } catch (error) {
                console.error('Error fetching Pokémon:', error);
            }
        }, 300);
    });

    // Click outside to hide dropdown
    document.addEventListener('click', (e) => {
        if (!resultsDiv.contains(e.target) && e.target !== searchInput) {
            resultsDiv.classList.add('hidden');
        }
    });

    // Reset on blur if input text doesn’t match a Pokémon
    searchInput.addEventListener('blur', () => {
        // Delay slightly to allow click on dropdown items
        setTimeout(() => {
            if (!hiddenInput.value) {
                searchInput.value = '';
                resultsDiv.classList.add('hidden');
                updateInputEffect();
            }
        }, 150);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initPokemonSearch();
});