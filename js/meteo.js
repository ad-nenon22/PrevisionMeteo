/**
 * Application Météo - Script principal optimisé
 * Version allégée pour améliorer les performances
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des fonctionnalités essentielles
    initializeWeatherApp();
});

/**
 * Initialise les fonctionnalités principales de l'application
 */
function initializeWeatherApp() {
    // Configuration du système d'onglets
    setupTabs();
    
    // Configuration de l'autocomplétion pour la recherche
    setupAutocomplete();
    
    // Configuration du formulaire de recherche météo
    setupWeatherForm();
    
    // Animation des résultats météo
    animateWeatherResults();
    
    // Configuration des interactions de la carte
    setupMapInteractivity();
}

/**
 * Configure le système d'onglets avec mise en évidence de l'onglet actif
 */
function setupTabs() {
    const tabButtons = document.querySelectorAll('.onglet-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Récupérer l'ID de l'onglet à afficher
            const tabId = this.getAttribute('data-onglet');
            
            if (tabId) {
                // Cacher tous les contenus d'onglets
                const tabContents = document.querySelectorAll('.contenu-onglet');
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                // Afficher le contenu de l'onglet sélectionné
                const activeTab = document.getElementById(tabId);
                if (activeTab) {
                    activeTab.style.display = 'block';
                }
                
                // Mettre à jour les classes des onglets
                tabButtons.forEach(btn => {
                    btn.classList.remove('actif');
                });
                
                // Ajouter la classe active à l'onglet cliqué
                this.classList.add('actif');
                
                // Sauvegarder l'état de l'onglet dans l'URL
                // Cela permet de conserver l'onglet actif lors du rechargement de la page
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('onglet', tabId);
                
                // Mettre à jour l'URL sans recharger la page
                const newUrl = window.location.pathname + '?' + urlParams.toString();
                history.replaceState(null, '', newUrl);
            }
        });
    });
}

/**
 * Configure l'autocomplétion pour la recherche de ville
 */
function setupAutocomplete() {
    const searchInput = document.getElementById('ville_nom');
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
            // Simulation d'une requête d'autocomplétion
            fetchCitySuggestions(query);
        }, 300);
    });
    
    /**
     * Récupère les suggestions de villes (simulation)
     * @param {string} query - Texte saisi par l'utilisateur
     */
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
    
    /**
     * Affiche les suggestions de villes dans le conteneur
     * @param {Array} suggestions - Liste de suggestions de villes
     */
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

/**
 * Configure le formulaire de recherche météo avec animation de chargement
 */
function setupWeatherForm() {
    const loadWeatherBtn = document.querySelector('.formulaire-meteo .bouton');
    
    if (loadWeatherBtn) {
        loadWeatherBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const region = document.querySelector('select[name="region"]')?.value;
            const ville = document.querySelector('input[name="ville"]')?.value;
            
            if (region || ville) {
                // Simulation d'un chargement
                this.disabled = true;
                this.innerHTML = '<span class="loading">Chargement...</span>';
                
                // Simuler un délai de chargement
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = 'Rechercher';
                    
                    // Afficher les résultats météo
                    const resultatMeteo = document.querySelector('.resultat-meteo');
                    if (resultatMeteo) {
                        resultatMeteo.style.display = 'block';
                        resultatMeteo.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    
                    // Mise à jour de la date de consultation
                    const dateConsultation = document.querySelector('.date-consultation');
                    if (dateConsultation) {
                        const now = new Date();
                        dateConsultation.textContent = `Dernière consultation: ${now.toLocaleDateString()} à ${now.toLocaleTimeString()}`;
                    }
                    
                    // Lancer l'animation des résultats
                    animateWeatherResults();
                }, 1000); // Délai réduit pour optimiser l'expérience utilisateur
            }
        });
    }
}

/**
 * Anime les résultats météo quand ils sont chargés
 */
function animateWeatherResults() {
    const weatherResult = document.querySelector('.resultat-meteo');
    if (!weatherResult) return;
    
    // Animation des éléments météo
    const elementsToAnimate = [
        '.meteo-actuelle',
        '.prevision-jour',
        '.jour-meteo'
    ];
    
    elementsToAnimate.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach((el, i) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            
            // Animation décalée
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 100 + (i * 120));
        });
    });
}

/**
 * Configure l'interactivité de la carte de France
 */
function setupMapInteractivity() {
    const mapAreas = document.querySelectorAll('map[name="image-map"] area');
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
    
    // Fonction pour mettre à jour la position du tooltip
    function updateTooltipPosition(e) {
        const mapRect = franceMap.getBoundingClientRect();
        const x = e.clientX - mapRect.left;
        const y = e.clientY - mapRect.top;
        
        overlay.style.left = `${x + 15}px`;
        overlay.style.top = `${y + 15}px`;
    }
    
    // Ajouter des événements pour chaque zone de la carte
    mapAreas.forEach(area => {
        // Survol des régions
        area.addEventListener('mouseover', function(e) {
            const title = this.getAttribute('title');
            overlay.textContent = title;
            overlay.style.opacity = '1';
            updateTooltipPosition(e);
        });
        
        area.addEventListener('mousemove', updateTooltipPosition);
        
        area.addEventListener('mouseout', function() {
            overlay.style.opacity = '0';
        });
    });

    // Permettre aux détails des prévisions journalières d'être dépliables
    const joursMeteo = document.querySelectorAll('.jour-meteo');
    joursMeteo.forEach(function(jour) {
        jour.addEventListener('click', function() {
            this.classList.toggle('ouvert');
        });
    });

    // Script pour la navigation par onglets
document.addEventListener('DOMContentLoaded', function() {
    const onglets = document.querySelectorAll('.onglet-btn');
    
    // Fonction pour activer un onglet
    function activerOnglet(ongletId) {
        // Masquer tous les contenus d'onglets
        document.querySelectorAll('.contenu-onglet').forEach(function(contenu) {
            contenu.style.display = 'none';
        });
        
        // Afficher le contenu de l'onglet sélectionné
        const tabContent = document.getElementById(ongletId);
        if (tabContent) {
            tabContent.style.display = 'block';
        }
        
        // Mettre à jour la classe active
        onglets.forEach(function(o) {
            o.classList.remove('actif');
        });
        
        const selectedTab = document.querySelector(`.onglet-btn[data-onglet="${ongletId}"]`);
        if (selectedTab) {
            selectedTab.classList.add('actif');
        }
    }
    
    // Configurer les écouteurs d'événements pour les clics sur les onglets
    onglets.forEach(function(onglet) {
        onglet.addEventListener('click', function() {
            const ongletId = this.getAttribute('data-onglet');
            activerOnglet(ongletId);
        });
    });
    
    // Trouver l'onglet actif au chargement de la page
    const activeTab = document.querySelector('.onglet-btn.actif');
    if (activeTab) {
        // Activer explicitement l'onglet qui a la classe 'actif'
        const activeTabId = activeTab.getAttribute('data-onglet');
        activerOnglet(activeTabId);
    }
    
    // Script pour afficher/masquer les détails des prévisions journalières
    const joursMeteo = document.querySelectorAll('.jour-meteo');
    
    joursMeteo.forEach(function(jour) {
        jour.addEventListener('click', function() {
            this.classList.toggle('ouvert');
        });
    });
});
}