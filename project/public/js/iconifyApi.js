document.addEventListener('DOMContentLoaded', function () {
    const iconContainers = document.querySelectorAll('.icon-container');
    const searchInputs = document.querySelectorAll('.icon-input');
    const searchResultsLists = document.querySelectorAll('.icon-search-results');
    const currentPrefix = 'fa';
    let searchTimeout;

    // For each input field
    searchInputs.forEach((input, index) => {
        const container = iconContainers[index];
        const resultBox = searchResultsLists[index];

        // Create and add status message
        const statusMessage = document.createElement('div');
        statusMessage.className = 'text-sm text-gray-500 text-center mt-2 mb-2';
        statusMessage.textContent = 'Search for an icon';
        container.appendChild(statusMessage);

        input.addEventListener('input', () => {
            const query = input.value.trim();

            clearTimeout(searchTimeout);

            if (query.length > 0) {
                statusMessage.textContent = 'Searching...';
                resultBox.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    searchIcons(query, statusMessage, input, resultBox);
                }, 300);
            } else {
                resultBox.innerHTML = '';
                resultBox.classList.add('hidden');
                statusMessage.textContent = 'Search for an icon';
            }
        });
    });

    async function searchIcons(query, statusMessage, input, resultBox) {
        try {
            const url = `https://api.iconify.design/search?query=${encodeURIComponent(query)}&limit=42&prefix=${currentPrefix}`;
            const response = await fetch(url);
            const data = await response.json();

            if (data && data.icons && data.icons.length > 0) {
                displayIcons(data.icons, input, resultBox);
                statusMessage.textContent = `${data.icons.length} icons found`;
            } else {
                resultBox.innerHTML = '';
                statusMessage.textContent = 'No icons found';
            }
        } catch (error) {
            console.error('Error while searching for icons:', error);
            statusMessage.textContent = 'Search error';
        }
    }

    function displayIcons(icons, input, resultBox) {
        resultBox.innerHTML = '';
        resultBox.className = 'mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-64 overflow-y-auto p-3 border border-gray-200 rounded-lg';

        icons.forEach(iconData => {
            const iconName = iconData.split(':')[1] || iconData;
            const iconItem = document.createElement('div');

            iconItem.innerHTML = `
                <div class="text-xl mb-1"><iconify-icon icon="${currentPrefix}:${iconName}"></iconify-icon></div>
                <div class="text-xs text-gray-500 truncate w-full text-center">${iconName}</div>
            `;

            iconItem.addEventListener('click', () => {
                input.value = `${currentPrefix}:${iconName}`;
            });

            resultBox.appendChild(iconItem);
        });
    }
});
