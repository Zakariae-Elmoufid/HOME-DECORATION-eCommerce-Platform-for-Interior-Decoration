document.addEventListener('DOMContentLoaded', function() {

    const iconContainer = document.getElementById('icon-container');
    const searchInput = document.getElementById('icon-input');
    const iconSearchResults = document.getElementById('icon-search-results');
    
    const currentPrefix = 'fa'; 
    let searchTimeout;
    
  
    
    const statusMessage = document.createElement('div');
    statusMessage.className = 'text-sm text-gray-500 text-center mt-2 mb-2';
    statusMessage.textContent = 'searsh icon';
    
    iconContainer.appendChild(statusMessage);
    
    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();
        
        clearTimeout(searchTimeout); 
        
        if (query.length > 0) {
            statusMessage.textContent = 'Recherche en cours...';
            iconSearchResults.classList.remove('hidden');
            
            searchTimeout = setTimeout(() => {
                searchIcons(query);
            }, 300);
        } else {
            iconSearchResults.innerHTML = '';
            iconSearchResults.classList.add('hidden');
            statusMessage.textContent = 'Recherchez une icône';
        }
    });
    
    async function searchIcons(query) {
        try {
            const url = `https://api.iconify.design/search?prefix=${currentPrefix}&query=${encodeURIComponent(query)}&limit=40`;
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data && data.icons && data.icons.length > 0) {
                displayIcons(data.icons);
                statusMessage.textContent = `${data.icons.length} icônes trouvées`;
            } else {
                iconSearchResults.innerHTML = '';
                statusMessage.textContent = 'Aucune icône trouvée';
            }
        } catch (error) {
            console.error('Erreur lors de la recherche d\'icônes:', error);
            statusMessage.textContent = 'Erreur lors de la recherche';
        }
    }
    
 function displayIcons(icons) {
        iconSearchResults.innerHTML = '';
        iconSearchResults.className = 'mt-3 grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-64 overflow-y-auto p-3 border border-gray-200 rounded-lg';
        
        icons.forEach(iconData => {
            const iconName = iconData.split(':')[1] || iconData;
            const iconItem = document.createElement('div');
            
            iconItem.innerHTML = `
                <div class="text-xl mb-1"><iconify-icon icon="${currentPrefix}:${iconName}"></iconify-icon></div>
                <div class="text-xs text-gray-500 truncate w-full text-center">${iconName}</div>
            `;
            
            iconItem.addEventListener('click', () => {
                let iconCode = `${currentPrefix}:${iconName}`;
                searchInput.value = iconCode;
            });
            
            iconSearchResults.appendChild(iconItem);
        });
    }
});