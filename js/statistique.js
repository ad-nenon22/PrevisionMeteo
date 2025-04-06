document.addEventListener('DOMContentLoaded', function() {
  // Récupération des éléments principaux
  const statsContainer = document.querySelector('.stats-section');
  const chartContainer = document.querySelector('.chart-container');
  const tableStats = document.querySelector('.table-stats');
  
  // Amélioration des cartes de statistiques
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(card => {
    card.addEventListener('mouseenter', () => card.classList.add('hover'));
    card.addEventListener('mouseleave', () => card.classList.remove('hover'));
  });
  
  // Ajout de boutons pour basculer entre le graphique et le tableau
  const viewToggleContainer = document.createElement('div');
  viewToggleContainer.className = 'view-toggle';
  viewToggleContainer.innerHTML = `
    <button class="view-btn active" data-view="chart">Vue Graphique</button>
    <button class="view-btn" data-view="table">Vue Tableau</button>
  `;
  
  // Insérer les boutons avant le conteneur du graphique
  statsContainer.insertBefore(viewToggleContainer, chartContainer);
  
  // Masquer le tableau initialement
  tableStats.style.display = 'none';
  
  // Fonctionnalité de bascule graphique/tableau
  viewToggleContainer.addEventListener('click', function(e) {
    if (!e.target.classList.contains('view-btn')) return;
    
    // Mettre à jour les classes actives
    viewToggleContainer.querySelectorAll('.view-btn').forEach(btn => 
      btn.classList.remove('active'));
    e.target.classList.add('active');
    
    // Afficher/masquer la vue appropriée
    const viewType = e.target.getAttribute('data-view');
    chartContainer.style.display = viewType === 'chart' ? 'block' : 'none';
    tableStats.style.display = viewType === 'table' ? 'block' : 'none';
  });
  
  // S'assurer que le graphique est correctement initialisé
  if (typeof Chart !== 'undefined') {
    // Rechercher le graphique existant s'il existe déjà
    if (!window.villesChart && document.getElementById('villesChart')) {
      // Si le graphique n'est pas initialisé mais que l'élément canvas existe,
      // nous devons attendre que le graphique d'origine soit initialisé
      const checkChartInterval = setInterval(function() {
        if (window.villesChart) {
          clearInterval(checkChartInterval);
          enhanceChart();
        }
      }, 100);
      
      // Si après 2 secondes le graphique n'est toujours pas initialisé, arrêter la vérification
      setTimeout(() => clearInterval(checkChartInterval), 2000);
    } else if (window.villesChart) {
      // Si le graphique est déjà initialisé, améliore-le directement
      enhanceChart();
    }
  }
  
  // Fonction pour améliorer le graphique existant
  function enhanceChart() {
    window.villesChart.options.animation = {
      duration: 1000,
      easing: 'easeOutQuart'
    };
    
    window.villesChart.options.plugins.tooltip = {
      callbacks: {
        label: function(context) {
          const value = context.raw;
          const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
          const percentage = Math.round((value / total) * 1000) / 10;
          return `${value} consultations (${percentage}%)`;
        }
      },
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      padding: 10,
      cornerRadius: 6
    };
    
    window.villesChart.update();
  }
  
  // Ajouter des styles CSS dynamiquement
  const styleElement = document.createElement('style');
  styleElement.textContent = `
    .stats-resume {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 40px;
    }
    
    .stat-card {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      border-radius: 12px;
      padding: 20px 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      min-width: 180px;
      text-align: center;
    }
    
    .stat-card.hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .stat-value {
      font-size: 2.5rem;
      font-weight: bold;
      color: #2c3e50;
    }
    
    .stat-label {
      font-size: 1rem;
      color: #7f8c8d;
      margin-top: 5px;
    }
    
    .view-toggle {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }
    
    .view-btn {
      background-color: #f1f2f6;
      border: none;
      padding: 8px 16px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .view-btn:first-child {
      border-radius: 6px 0 0 6px;
    }
    
    .view-btn:last-child {
      border-radius: 0 6px 6px 0;
    }
    
    .view-btn.active {
      background-color: #74b9ff;
      color: white;
    }
    
    .chart-container {
      height: 400px;
      margin-bottom: 30px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      border-radius: 8px;
      padding: 20px;
      background-color: white;
    }
    
    .table-stats table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      border-radius: 8px;
      overflow: hidden;
    }
    
    .table-stats thead {
      background-color: #74b9ff;
      color: white;
    }
    
    .table-stats th, .table-stats td {
      padding: 12px 15px;
      text-align: left;
    }
    
    .table-stats tbody tr:nth-child(even) {
      background-color: #f8f9fa;
    }
    
    .table-stats tbody tr:hover {
      background-color: #e9ecef;
    }
    
    @media (max-width: 768px) {
      .stats-resume {
        flex-direction: column;
        align-items: center;
      }
      
      .stat-card {
        width: 80%;
      }
    }
  `;
  
  document.head.appendChild(styleElement);
});