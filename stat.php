<?php
/**
 * Page Statistiques - Visualisation des statistiques d'utilisation
 * 
 * Cette page affiche les statistiques d'utilisation du site, notamment
 * l'histogramme des villes les plus consultées.
 * 
 * @author Admira 
 */

// Inclusion des fonctions
require_once 'include/functions.inc.php';

// Fonction pour lire le fichier CSV et compter les villes consultées
function getVillesConsultees() {
    $fichier_csv = 'data/consultations.csv';
    $villes = [];
    
    // Vérifier si le fichier existe
    if (file_exists($fichier_csv)) {
        // Ouvrir le fichier en lecture
        if (($handle = fopen($fichier_csv, "r")) !== FALSE) {
            // Ignorer la première ligne (en-têtes)
            fgetcsv($handle, 1000, ",");
            
            // Lire chaque ligne
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Format: date,ip,departement,ville,region,lat,lon
                if (isset($data[3]) && !empty($data[3])) {
                    $ville = $data[3];
                    $departement = isset($data[2]) ? $data[2] : '';
                    $region = isset($data[4]) ? $data[4] : '';
                    
                    $cle = $ville;
                    
                    if (!isset($villes[$cle])) {
                        $villes[$cle] = [
                            'nom' => $ville,
                            'departement' => $departement,
                            'region' => $region,
                            'count' => 1
                        ];
                    } else {
                        $villes[$cle]['count']++;
                    }
                }
            }
            fclose($handle);
        }
    }
    
    // Trier par nombre de consultations (décroissant)
    uasort($villes, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // Limiter aux 15 premières villes
    return array_slice($villes, 0, 15, true);
}

// Récupérer les données pour l'histogramme
$villes_consultees = getVillesConsultees();

// Calculer le nombre total de consultations
$total_consultations = 0;
foreach ($villes_consultees as $ville) {
    $total_consultations += $ville['count'];
}

// Récupérer le nombre de villes uniques consultées
$nb_villes_uniques = count($villes_consultees);

// Calculer le nombre total de consultations pour toutes les villes
$toutes_villes = count(array_unique(array_column($villes_consultees, 'region')));
// Générer des couleurs pour l'histogramme (palette cohérente)
function generateChartColors() {
    $primary_colors = ['#3498db', '#2ecc71', '#f1c40f', '#e67e22', '#9b59b6', 
        '#1abc9c', '#e74c3c', '#34495e', '#16a085', '#d35400',
        '#27ae60', '#2980b9', '#8e44ad', '#c0392b', '#7f8c8d'
    ];

    

    return $primary_colors;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>

  <?php
  $lang = "fr";
  if(isset($_GET["lang"])){
    $lang = $_GET["lang"];
}
if(isset($_GET['style'])) {
    $style = $_GET['style'];
    setcookie('style', $style, time() + 3600, '/');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit(); 
}
if(isset($_COOKIE['style'])) {
    if($_COOKIE['style']=="dark.css") {
        $style = "dark.css";
        $other_style = "styles.css";
        $sourceimage = "./images/ligth.png";
        $logo_image = "./images/logo.png"; // Logo pour le mode sombre
    }
    else if($_COOKIE['style']=="styles.css") {
        $style = "styles.css";
        $other_style = "dark.css";
        $sourceimage = "./images/dark.png";
        $logo_image = "./images/logo.png"; // Logo pour le mode clair pareil
    }
}
else {
    $style = "styles.css";
    $other_style = "dark.css";
    $sourceimage = "./images/dark.png";
    $logo_image = "./images/logo.png"; // Logo par défaut (mode cmlair)
}
?>
<meta charset="UTF-8"/>
<meta name="author" content="Admira"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="<?php echo $page_des ?? 'Mété news'; ?>"/>

<title><?php echo $title ?? 'Skyview Météo'; ?></title>
<link rel="stylesheet" href="<?php echo $style; ?>"/>
<link rel="icon" href="images/fav.ico" type="image/x-icon"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/statistique.js"></script>

</head>

<body>
    <header class="header">

     <div class = "header-top">
        <a href="index.php" class="logo">
            <img src="<?php echo $logo_image; ?>" alt="Logo du site"/>
        </a>
        <h1 class="title"> Météo News </h1>
    </div>
    <nav class="menu">
        <figure id="viewmode">
            <a href="?style=<?php echo $other_style ?>"><img src="<?php echo $sourceimage ?>" alt="Change viewmode"/></a>
        </figure>

        <ul>
            <li><a href="index.php"><i class="fas fa-home" style= "color:white"></i> Accueil</a></li>
            <li><a href="meteo.php"><i class="fas fa-cloud-sun-rain" style= "color:white"></i> Prévision</a></li>
            <li><a href="stat.php"><i class="fas fa-chart-line" style="color: white"></i> Statistique</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope" style= "color: white"></i> Contact</a></li>

        </ul>
    </nav>
</header>

    
    <main>
        <section

        <div class="conteneur">
            <div class="stats-dashboard">
                <div class="stats-header">
                    <h2>Tableau de bord statistique</h2>
                    <p>Analyse des consultations de prévisions météo par ville</p>
                </div>
                
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_consultations, 0, ',', ' '); ?></div>
                        <div class="stat-label">Consultations totales</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-city"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($nb_villes_uniques, 0, ',', ' '); ?></div>
                        <div class="stat-label">Villes consultées</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($toutes_villes, 0, ',', ' '); ?></div>
                        <div class="stat-label">Régions actives</div>
                    </div>
                </div>
                
                <?php if (empty($villes_consultees)): ?>
                <div class="data-container empty-state">
                    <div class="empty-icon">
                        <i class="far fa-chart-bar"></i>
                    </div>
                    <p class="empty-message">
                        Aucune donnée de consultation n'est disponible pour le moment.
                        Consultez des prévisions météo pour voir apparaître des statistiques.
                    </p>
                </div>
                <?php else: ?>
                
                <div class="view-controls">
                    <div class="view-tabs">
                        <button class="view-tab active" data-view="chart">
                            <i class="fas fa-chart-bar"></i> Graphique
                        </button>
                        <button class="view-tab" data-view="table">
                            <i class="fas fa-table"></i> Tableau
                        </button>
                        <span class="tab-indicator"></span>
                    </div>
                </div>
                
                <div class="data-container chart-view">
                    <div class="chart-title">Villes les plus consultées</div>
                    <div class="chart-container">
                        <canvas id="villesChart"></canvas>
                    </div>
                </div>
                
                <div class="data-container table-view" style="display: none;">
                    <div class="chart-title">Détails des consultations par ville</div>
                    <div class="table-container">
                        <table class="stats-table">
                           <thead>
    <tr>
        <th>Ville</th>
        <th>Département</th>
        <th>Région</th>
        <th>Consultations</th>
        <th>Pourcentage</th>
    </tr>
</thead>
<tbody>
    <?php 
    foreach ($villes_consultees as $ville): 
    $percentage = ($ville['count'] / $total_consultations) * 100;
    ?>
    <tr>
        <td><?php echo htmlspecialchars($ville['nom']); ?></td>
        <td><?php echo htmlspecialchars($ville['departement']); ?></td>
        <td><?php echo htmlspecialchars($ville['region']); ?></td>
        <td><?php echo number_format($ville['count'], 0, ',', ' '); ?></td>
        <td>
            <?php echo number_format($percentage, 1, ',', ' '); ?>%
            <div class="percentage-bar">
                <div class="percentage-value" style="width: <?php echo $percentage; ?>%;"></div>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </section>

    </main>
    
    <?php if (!empty($villes_consultees)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration des onglets
            const viewTabs = document.querySelectorAll('.view-tab');
            const tabIndicator = document.querySelector('.tab-indicator');
            const chartView = document.querySelector('.chart-view');
            const tableView = document.querySelector('.table-view');
            
            // Initialiser l'indicateur d'onglet actif
            function updateTabIndicator() {
                const activeTab = document.querySelector('.view-tab.active');
                tabIndicator.style.width = `${activeTab.offsetWidth}px`;
                tabIndicator.style.left = `${activeTab.offsetLeft}px`;
            }
            
            updateTabIndicator();
            
            // Gestion du changement d'onglet
            viewTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Mettre à jour les classes actives
                    viewTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Mettre à jour l'indicateur
                    updateTabIndicator();
                    
                    // Afficher la vue correspondante
                    const viewType = this.getAttribute('data-view');
                    if (viewType === 'chart') {
                        chartView.style.display = 'block';
                        tableView.style.display = 'none';
                    } else {
                        chartView.style.display = 'none';
                        tableView.style.display = 'block';
                    }
                });
            });
            
            // Initialiser le graphique
            const ctx = document.getElementById('villesChart').getContext('2d');
            
            // Préparer les données pour le graphique
            const labels = <?php echo json_encode(array_column($villes_consultees, 'nom')); ?>;
            const data = <?php echo json_encode(array_column($villes_consultees, 'count')); ?>;
            const backgroundColors = <?php echo json_encode(generateChartColors()); ?>;
            
            // Créer le graphique
            window.villesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nombre de consultations',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: 'transparent',
                        borderRadius: 0,
                        maxBarThickness: 60
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `Consultations: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            });
            
            // Animation des cartes de statistiques
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animate');
                }, 100 * index);
            });
            
            // Rendre le tableau responsive
            const tableContainer = document.querySelector('.table-container');
            if (tableContainer) {
                tableContainer.addEventListener('scroll', function() {
                    const scrolled = this.scrollTop > 5;
                    const headers = this.querySelectorAll('th');
                    
                    headers.forEach(header => {
                        if (scrolled) {
                            header.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                        } else {
                            header.style.boxShadow = 'none';
                        }
                    });
                });
            }
        });
    </script>
    <?php endif; ?>
<?php
    require "./include/footer.inc.php";
?>