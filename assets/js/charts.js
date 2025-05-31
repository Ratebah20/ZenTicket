/**
 * Charts JS - Graphiques pour le tableau de bord ZenTicket
 * Utilise Chart.js pour créer des visualisations interactives
 */

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si Chart.js est disponible
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js n\'est pas chargé. Les graphiques ne seront pas affichés.');
        return;
    }
    
    // Configuration globale des graphiques
    Chart.defaults.color = '#a0aec0';
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    
    // Graphique des tickets par statut
    const ticketStatusChart = document.getElementById('ticketStatusChart');
    if (ticketStatusChart) {
        // Récupérer les données du graphique depuis l'attribut data
        const chartData = JSON.parse(ticketStatusChart.getAttribute('data-chart') || '{}');
        
        new Chart(ticketStatusChart, {
            type: 'doughnut',
            data: {
                labels: chartData.labels || ['En attente', 'En cours', 'Résolu', 'Fermé'],
                datasets: [{
                    data: chartData.data || [12, 19, 8, 15],
                    backgroundColor: [
                        'rgba(255, 159, 64, 0.8)',  // Orange
                        'rgba(54, 162, 235, 0.8)',  // Bleu
                        'rgba(75, 192, 192, 0.8)',  // Vert
                        'rgba(201, 203, 207, 0.8)'  // Gris
                    ],
                    borderColor: [
                        'rgba(255, 159, 64, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(201, 203, 207, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Répartition des tickets par statut'
                    }
                }
            }
        });
    }
    
    // Graphique de l'évolution des tickets
    const ticketTrendChart = document.getElementById('ticketTrendChart');
    if (ticketTrendChart) {
        // Récupérer les données du graphique depuis l'attribut data
        const chartData = JSON.parse(ticketTrendChart.getAttribute('data-chart') || '{}');
        
        new Chart(ticketTrendChart, {
            type: 'line',
            data: {
                labels: chartData.labels || ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Nouveaux tickets',
                    data: chartData.data?.new || [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgba(255, 107, 53, 1)',
                    backgroundColor: 'rgba(255, 107, 53, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Tickets résolus',
                    data: chartData.data?.resolved || [7, 11, 5, 8, 3, 7],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Évolution des tickets sur 6 mois'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(160, 174, 192, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Graphique de catégories de tickets
    const ticketCategoryChart = document.getElementById('ticketCategoryChart');
    if (ticketCategoryChart) {
        // Récupérer les données du graphique depuis l'attribut data
        const chartData = JSON.parse(ticketCategoryChart.getAttribute('data-chart') || '{}');
        
        new Chart(ticketCategoryChart, {
            type: 'bar',
            data: {
                labels: chartData.labels || ['Matériel', 'Logiciel', 'Réseau', 'Sécurité', 'Autre'],
                datasets: [{
                    label: 'Nombre de tickets',
                    data: chartData.data || [25, 59, 30, 15, 10],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Tickets par catégorie'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(160, 174, 192, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
