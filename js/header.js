document.addEventListener('DOMContentLoaded', function() {
    // Variables pour l'animation des éléments
    const fadeElements = document.querySelectorAll('.carte-meteo, .jour-prevision, section');
    const weatherIcons = document.querySelectorAll('.icone-meteo img');
    
    // Animation au scroll pour les éléments
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    // Initialisation des animations au scroll
    fadeElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });
    
    // Animation des icônes météo
    weatherIcons.forEach(icon => {
        icon.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        icon.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Animation des onglets
    const tabs = document.querySelectorAll('.onglet');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Retirer la classe active de tous les onglets
            tabs.forEach(t => t.classList.remove('actif'));
            // Ajouter la classe active à l'onglet cliqué
            this.classList.add('actif');
            
            // Ici, vous pourriez ajouter la logique pour afficher le contenu de l'onglet
            const contentId = this.getAttribute('data-tab');
            const tabContents = document.querySelectorAll('.contenu-onglet');
            
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            const activeContent = document.getElementById(contentId);
            if (activeContent) {
                activeContent.style.display = 'block';
                activeContent.style.animation = 'fadeIn 0.4s ease';
            }
        });
    });
    
    // Effet hover pour les boutons
    const buttons = document.querySelectorAll('.bouton');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.2)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
        
        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(0)';
        });
        
        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-3px)';
        });
    });
    
    // Effet sticky pour le header
    const header = document.querySelector('.header');
    let lastScrollPosition = 0;
    
    window.addEventListener('scroll', function() {
        const currentScrollPosition = window.pageYOffset;
        
        if (currentScrollPosition > 100) {
            header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            header.style.height = '70px';
        } else {
            header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            header.style.height = '80px';
        }
        
        // Pour cacher/montrer le header au scroll
        if (currentScrollPosition > lastScrollPosition && currentScrollPosition > 300) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }
        
        lastScrollPosition = currentScrollPosition;
    });
    
    // Effet pour les liens du menu
    const menuLinks = document.querySelectorAll('.menu > ul > li > a');
    
    menuLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = '#3498db';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });
    
    // Animation pour les cartes météo
    const meteoCards = document.querySelectorAll('.carte-meteo');
    
    meteoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    // Simulation de chargement des données météo
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
                
                // Créer un effet de chargement
                const loadingSpan = document.querySelector('.loading');
                if (loadingSpan) {
                    loadingSpan.style.position = 'relative';
                    loadingSpan.innerHTML = 'Chargement...';
                    
                    // Animation de points
                    let dots = 0;
                    const loadingInterval = setInterval(() => {
                        dots = (dots + 1) % 4;
                        loadingSpan.innerHTML = 'Chargement' + '.'.repeat(dots);
                    }, 400);
                    
                    // Simuler un délai de chargement
                    setTimeout(() => {
                        clearInterval(loadingInterval);
                        this.disabled = false;
                        this.innerHTML = 'Rechercher';
                        
                        // Afficher les résultats (vous devrez adapter cette partie)
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
                    }, 1500);
                }
            }
        });
    }
});