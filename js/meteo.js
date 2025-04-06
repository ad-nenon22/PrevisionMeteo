// Fonction pour gérer les onglets
function afficherOnglet(ongletId) {
    // Cacher tous les contenus d'onglets
    const contenuOnglets = document.getElementsByClassName('contenu-onglet');
    for (let i = 0; i < contenuOnglets.length; i++) {
        contenuOnglets[i].style.display = 'none';
    }
    
    // Afficher le contenu de l'onglet sélectionné avec animation
    const ongletActif = document.getElementById(ongletId);
    ongletActif.style.display = 'block';
    ongletActif.classList.add('animate-fade-in');
    setTimeout(() => {
        ongletActif.classList.remove('animate-fade-in');
    }, 500);
    
    // Mettre à jour les classes des onglets
    const onglets = document.getElementsByClassName('onglet');
    for (let i = 0; i < onglets.length; i++) {
        onglets[i].classList.remove('actif');
    }
    
    // Ajouter la classe active à l'onglet cliqué
    event.currentTarget.classList.add('actif');
    
    // Sauvegarder l'onglet actif dans le localStorage
    localStorage.setItem('ongletActif', ongletId);
}

// Fonction pour améliorer l'interactivité de la carte de France
function setupMapInteractivity() {
    const mapAreas = document.querySelectorAll('map[name="francemap"] area');
    const franceMap = document.getElementById('main-france-map');
    
    if (!mapAreas.length || !franceMap) return;

    // Créer un overlay pour afficher les noms de régions au survol
    const overlay = document.createElement('div');
    overlay.className = 'map-overlay';
    overlay.style.cssText = `
        position: absolute;
        padding: 8px 12px;
        background-color: rgba(74, 137, 220, 0.9);
        color: white;
        border-radius: 4px;
        font-size: 14px;
        font-weight: bold;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    `;
    franceMap.parentNode.style.position = 'relative';
    franceMap.parentNode.appendChild(overlay);
    
    // Ajouter des événements pour chaque zone de la carte
    mapAreas.forEach(area => {
        // Survol des régions
        area.addEventListener('mouseover', function(e) {
            const title = this.getAttribute('title');
            overlay.textContent = title;
            overlay.style.opacity = '1';
            
            // Mettre à jour la position du tooltip
            updateTooltipPosition(e);
        });
        
        area.addEventListener('mousemove', updateTooltipPosition);
        
        area.addEventListener('mouseout', function() {
            overlay.style.opacity = '0';
        });
        
        // Fonction pour mettre à jour la position du tooltip
        function updateTooltipPosition(e) {
            const mapRect = franceMap.getBoundingClientRect();
            const x = e.clientX - mapRect.left;
            const y = e.clientY - mapRect.top;
            
            overlay.style.left = `${x + 15}px`;
            overlay.style.top = `${y + 15}px`;
        }
    });
}

// Mettre en évidence région/département sélectionnés sur la carte
function highlightSelectedRegion() {
    // Récupérer les paramètres d'URL pour voir quelle région est sélectionnée
    const urlParams = new URLSearchParams(window.location.search);
    const selectedRegion = urlParams.get('region');
    
    if (selectedRegion) {
        // Mettre en évidence l'area correspondante
        const regionArea = document.querySelector(`map[name="francemap"] area[href*="region=${selectedRegion}"]`);
        if (regionArea) {
            regionArea.classList.add('selected-region');
        }
    }
}

// Autocomplétion pour la recherche de ville
function setupAutocomplete() {
    const searchInput = document.getElementById('recherche_ville');
    if (!searchInput) return;
    
    // Créer un conteneur pour les suggestions
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'suggestions-container';
    suggestionsContainer.style.cssText = `
        position: absolute;
        width: calc(100% - 2px);
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e6e9ed;
        border-top: none;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: none;
    `;
    searchInput.parentElement.style.position = 'relative';
    searchInput.parentElement.appendChild(suggestionsContainer);
    
    // Variable pour stocker le timer de délai
    let debounceTimer;
    
    // Gérer la saisie dans le champ de recherche
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        // Attendre que l'utilisateur arrête de taper pendant 300ms
        debounceTimer = setTimeout(() => {
            // Simulation d'une requête d'autocomplétion (à remplacer par une vraie requête AJAX)
            // Dans un cas réel, vous feriez une requête à votre backend PHP
            fetchCitySuggestions(query);
        }, 300);
    });
    
    // Fonction pour récupérer les suggestions de villes (simulation)
    // Dans un cas réel, cette fonction ferait une requête AJAX vers votre backend
    function fetchCitySuggestions(query) {
        // Simuler un délai réseau
        setTimeout(() => {
            // Ces données devraient venir de votre API
            const dummyData = [
                { nom: "Paris", code_postal: "75000" },
                { nom: "Marseille", code_postal: "13000" },
                { nom: "Lyon", code_postal: "69000" },
                { nom: "Toulouse", code_postal: "31000" },
                { nom: "Nice", code_postal: "06000" },
                { nom: "Nantes", code_postal: "44000" },
                { nom: "Strasbourg", code_postal: "67000" },
                { nom: "Montpellier", code_postal: "34000" },
                { nom: "Bordeaux", code_postal: "33000" },
                { nom: "Lille", code_postal: "59000" }
            ];
            
            // Filtrer les suggestions basées sur la requête
            const filteredSuggestions = dummyData.filter(city => 
                city.nom.toLowerCase().includes(query.toLowerCase())
            );
            
            // Afficher les suggestions
            displaySuggestions(filteredSuggestions);
        }, 200);
    }
    
    // Afficher les suggestions
    function displaySuggestions(suggestions) {
        suggestionsContainer.innerHTML = '';
        
        if (suggestions.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        suggestions.forEach(city => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'suggestion-item';
            suggestionItem.textContent = `${city.nom} (${city.code_postal})`;
            suggestionItem.style.cssText = `
                padding: 8px 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            `;
            
            suggestionItem.addEventListener('mouseover', function() {
                this.style.backgroundColor = '#f5f7fa';
            });
            
            suggestionItem.addEventListener('mouseout', function() {
                this.style.backgroundColor = 'transparent';
            });
            
            suggestionItem.addEventListener('click', function() {
                searchInput.value = city.nom;
                suggestionsContainer.style.display = 'none';
            });
            
            suggestionsContainer.appendChild(suggestionItem);
        });
        
        suggestionsContainer.style.display = 'block';
    }
    
    // Cacher les suggestions en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput) {
            suggestionsContainer.style.display = 'none';
        }
    });
}

// Animation des résultats météo
function animateWeatherResults() {
    const weatherResult = document.querySelector('.resultat-meteo');
    if (!weatherResult) return;
    
    // Ajouter des classes pour l'animation des éléments météo
    const elementsToAnimate = [
        '.carte-meteo',
        '.jour-prevision'
    ];
    
    elementsToAnimate.forEach((selector, index) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            // Animation décalée
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 100 + (i * 150));
        });
    });
}

// Chargement de la dernière vue
function restoreLastView() {
    // Restaurer l'onglet actif depuis localStorage
    const lastActiveTab = localStorage.getItem('ongletActif');
    if (lastActiveTab) {
        const tabButton = document.querySelector(`.onglet[onclick*="${lastActiveTab}"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
}

// Fonction pour initialiser toutes les fonctionnalités JS
function initializeApplication() {
    // Mettre en place l'interactivité de la carte
    setupMapInteractivity();
    
    // Mettre en évidence la région sélectionnée
    highlightSelectedRegion();
    
    // Initialiser l'autocomplétion
    setupAutocomplete();
    
    // Animer les résultats météo
    animateWeatherResults();
    
    // Restaurer la dernière vue
    restoreLastView();
    
    // Vérifier l'état des données météo pour afficher des animations
    const weatherCard = document.querySelector('.carte-meteo');
    if (weatherCard) {
        weatherCard.classList.add('loaded');
    }
}

// Lancer l'initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', initializeApplication);