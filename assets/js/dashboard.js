/*
 * Fonctionnalités du tableau de bord
 */
import Chart from 'chart.js/auto';

// Configuration globale des graphiques
Chart.defaults.color = '#a0a0a0';
Chart.defaults.borderColor = '#2a2d3a';

document.addEventListener('DOMContentLoaded', function() {
    // Récupération des données depuis le HTML
    const chartData = window.chartData || {};
    
    // Graphique d'évolution des tickets
    if (document.getElementById('ticketsEvolutionChart')) {
        const ctx1 = document.getElementById('ticketsEvolutionChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: chartData.ticketsEvolution || {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [
                    {
                        label: 'Nouveaux',
                        data: [5, 8, 12, 7, 10, 4, 6],
                        borderColor: '#ff6b35',
                        backgroundColor: 'rgba(255, 107, 53, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Résolus',
                        data: [3, 5, 10, 8, 12, 6, 4],
                        borderColor: '#4caf50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 29, 41, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#a0a0a0',
                        borderColor: '#2a2d3a',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' tickets';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#a0a0a0'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#a0a0a0',
                            stepSize: 5
                        }
                    }
                }
            }
        });
    }
    
    // Graphique de répartition par catégorie
    if (document.getElementById('categoryChart')) {
        const ctx2 = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: chartData.categoryDistribution || {
                labels: ['Matériel', 'Logiciel', 'Réseau', 'Sécurité', 'Autre'],
                datasets: [{
                    data: [25, 35, 15, 15, 10],
                    backgroundColor: [
                        '#ff6b35',
                        '#4caf50',
                        '#2196f3',
                        '#ffc107',
                        '#9c27b0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(26, 29, 41, 0.9)',
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '70%',
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }
    
    // Animation des cartes statistiques
    animateStats();
});

// Animation des compteurs
function animateStats() {
    const statValues = document.querySelectorAll('.stat-value');
    
    statValues.forEach(stat => {
        // Ne pas animer si la valeur n'est pas un nombre
        if (isNaN(parseInt(stat.textContent))) {
            return;
        }
        
        const finalValue = parseInt(stat.textContent.replace(/[^\d]/g, ''));
        const duration = 1500;
        const increment = finalValue / (duration / 16);
        let currentValue = 0;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(currentValue).toLocaleString('fr-FR');
        }, 16);
    });
}

// Fonction pour actualiser les statistiques du dashboard
async function refreshDashboardStats() {
    try {
        const response = await fetch('/api/dashboard/stats', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        
        if (!response.ok) {
            throw new Error('Erreur lors de la récupération des statistiques');
        }
        
        const data = await response.json();
        
        // Mettre à jour les statistiques
        updateStats(data.stats);
        
        // Afficher une notification si de nouveaux tickets critiques
        if (data.newCriticalTickets > 0) {
            window.showNotification(`${data.newCriticalTickets} nouveau(x) ticket(s) critique(s)`, 'danger');
        }
    } catch (error) {
        console.error('Erreur lors de l\'actualisation des statistiques:', error);
    }
}

// Mise à jour des statistiques sans rechargement
function updateStats(stats) {
    if (!stats) return;
    
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            const oldValue = parseInt(element.textContent.replace(/[^\d]/g, ''));
            const newValue = stats[key];
            
            if (oldValue !== newValue) {
                // Animation de changement
                element.classList.add('animate__animated', 'animate__flash');
                element.textContent = newValue.toLocaleString('fr-FR');
                
                setTimeout(() => {
                    element.classList.remove('animate__animated', 'animate__flash');
                }, 1000);
            }
        }
    });
}

// Exporter les fonctions pour utilisation externe
window.dashboardUtils = {
    animateStats,
    refreshDashboardStats,
    updateStats
};
