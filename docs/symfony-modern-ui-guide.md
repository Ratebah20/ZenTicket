# Guide Complet - Interface Moderne avec Symfony & Twig

## 📋 Table des matières

1. [Vue d'ensemble](#vue-densemble)
2. [Prérequis et Installation](#prérequis-et-installation)
3. [Structure du projet](#structure-du-projet)
4. [Configuration Webpack Encore](#configuration-webpack-encore)
5. [Templates Twig](#templates-twig)
6. [Contrôleurs Symfony](#contrôleurs-symfony)
7. [Assets CSS/JS](#assets-cssjs)
8. [Composants réutilisables](#composants-réutilisables)
9. [Intégration des graphiques](#intégration-des-graphiques)
10. [Bonnes pratiques](#bonnes-pratiques)

## 1. Vue d'ensemble

Ce guide vous aidera à transformer votre interface ZenTicket actuelle en une interface moderne similaire à GestionStock. Le design inclut :
- Sidebar fixe avec navigation
- Dashboard avec cartes de statistiques
- Graphiques interactifs
- Tables stylisées
- Mode sombre par défaut
- Animations fluides

## 2. Prérequis et Installation

### 2.1 Installation des dépendances

```bash
# Backend - Symfony
composer require symfony/webpack-encore-bundle
composer require symfony/ux-chartjs
composer require symfony/stimulus-bundle

# Frontend - NPM
npm install
npm install --save bootstrap@5.3.0
npm install --save @fortawesome/fontawesome-free
npm install --save chart.js
npm install --save animate.css
```

### 2.2 Configuration package.json

```json
{
  "devDependencies": {
    "@symfony/webpack-encore": "^4.0.0",
    "@symfony/stimulus-bridge": "^3.2.0"
  },
  "dependencies": {
    "bootstrap": "^5.3.0",
    "@fortawesome/fontawesome-free": "^6.4.0",
    "chart.js": "^4.3.0",
    "animate.css": "^4.1.1"
  }
}
```

## 3. Structure du projet

```
project/
├── assets/
│   ├── styles/
│   │   ├── app.scss           # Styles principaux
│   │   ├── _variables.scss    # Variables SCSS
│   │   ├── _sidebar.scss      # Styles sidebar
│   │   ├── _dashboard.scss    # Styles dashboard
│   │   └── _components.scss   # Composants réutilisables
│   ├── js/
│   │   ├── app.js            # JS principal
│   │   ├── dashboard.js       # JS spécifique dashboard
│   │   └── charts.js          # Configuration graphiques
│   └── controllers/           # Contrôleurs Stimulus
├── templates/
│   ├── base.html.twig         # Template de base
│   ├── _partials/
│   │   ├── _sidebar.html.twig
│   │   ├── _navbar.html.twig
│   │   └── _footer.html.twig
│   ├── dashboard/
│   │   └── index.html.twig
│   ├── tickets/
│   │   ├── index.html.twig
│   │   └── _ticket_row.html.twig
│   └── components/
│       ├── _stat_card.html.twig
│       └── _data_table.html.twig
└── src/
    └── Controller/
        ├── DashboardController.php
        └── TicketController.php
```

## 4. Configuration Webpack Encore

### 4.1 webpack.config.js

```javascript
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    
    // Entries
    .addEntry('app', './assets/js/app.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addStyleEntry('global', './assets/styles/app.scss')
    
    // Features
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSingleRuntimeChunk()
    
    // Optimizations
    .splitEntryChunks()
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enablePostCssLoader()
;

module.exports = Encore.getWebpackConfig();
```

## 5. Templates Twig

### 5.1 base.html.twig - Template principal

```twig
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}ZenTicket{% endblock %}</title>
    
    {% block stylesheets %}
        {{ encore_entry_link_tags('global') }}
    {% endblock %}
</head>
<body class="dark-theme">
    <div class="app-wrapper">
        <!-- Sidebar -->
        {% include '_partials/_sidebar.html.twig' %}
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            {% include '_partials/_navbar.html.twig' %}
            
            <!-- Page Content -->
            <div class="page-content">
                <div class="container-fluid">
                    {% block body %}{% endblock %}
                </div>
            </div>
        </div>
    </div>

    {% block javascripts %}
        {{ encore_entry_script_tags('app') }}
    {% endblock %}
</body>
</html>
```

### 5.2 _partials/_sidebar.html.twig

```twig
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ path('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <span class="brand-text">ZenTicket</span>
        </a>
    </div>

    <div class="sidebar-menu">
        <!-- Menu Principal -->
        <div class="menu-section">
            <h6 class="menu-section-title">MENU PRINCIPAL</h6>
            
            {% set currentRoute = app.request.attributes.get('_route') %}
            
            <a href="{{ path('dashboard') }}" 
               class="menu-item {{ currentRoute == 'dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            
            <a href="{{ path('ticket_index') }}" 
               class="menu-item {{ currentRoute starts with 'ticket_' ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                <span>Tickets</span>
                {% if pendingTicketsCount > 0 %}
                    <span class="badge badge-warning ms-auto">{{ pendingTicketsCount }}</span>
                {% endif %}
            </a>
            
            <a href="{{ path('intervention_index') }}" 
               class="menu-item {{ currentRoute starts with 'intervention_' ? 'active' : '' }}">
                <i class="fas fa-tools"></i>
                <span>Interventions</span>
            </a>
            
            <a href="{{ path('client_index') }}" 
               class="menu-item {{ currentRoute starts with 'client_' ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Clients</span>
            </a>
        </div>

        <!-- Analytics -->
        <div class="menu-section">
            <h6 class="menu-section-title">ANALYTICS</h6>
            
            <a href="{{ path('reports') }}" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <span>Rapports</span>
            </a>
            
            <a href="{{ path('statistics') }}" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span>Statistiques</span>
            </a>
        </div>
    </div>
</nav>
```

### 5.3 dashboard/index.html.twig

```twig
{% extends 'base.html.twig' %}

{% block title %}Tableau de bord - ZenTicket{% endblock %}

{% block body %}
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Tableau de bord</h1>
                <p class="page-subtitle">Vue d'ensemble de l'activité</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                    <i class="fas fa-plus me-2"></i>Nouveau Ticket
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6">
            {% include 'components/_stat_card.html.twig' with {
                'icon': 'fa-ticket-alt',
                'iconColor': 'primary',
                'value': stats.totalTickets,
                'label': 'Tickets actifs',
                'change': '+5%',
                'changeType': 'positive',
                'changeLabel': 'ce mois'
            } %}
        </div>
        
        <div class="col-xl-3 col-lg-6">
            {% include 'components/_stat_card.html.twig' with {
                'icon': 'fa-clock',
                'iconColor': 'warning',
                'value': stats.pendingTickets,
                'label': 'En attente',
                'changeLabel': 'Dernières 24h'
            } %}
        </div>
        
        <div class="col-xl-3 col-lg-6">
            {% include 'components/_stat_card.html.twig' with {
                'icon': 'fa-check-circle',
                'iconColor': 'success',
                'value': stats.resolvedToday,
                'label': 'Résolus aujourd\'hui',
                'change': '5',
                'changeType': 'positive',
                'changeLabel': 'prioritaires'
            } %}
        </div>
        
        <div class="col-xl-3 col-lg-6">
            {% include 'components/_stat_card.html.twig' with {
                'icon': 'fa-exclamation-triangle',
                'iconColor': 'danger',
                'value': stats.criticalTickets,
                'label': 'Tickets critiques',
                'changeType': 'negative',
                'changeLabel': 'À traiter en urgence'
            } %}
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Évolution des tickets</h5>
                </div>
                <div class="card-body">
                    <canvas id="ticketsEvolutionChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Répartition par catégorie</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tickets Table -->
    <div class="card dashboard-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Tickets récents</h5>
            <a href="{{ path('ticket_index') }}" class="btn btn-sm btn-outline-primary">
                Voir tout <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            {% include 'components/_data_table.html.twig' with {
                'headers': ['ID', 'Titre', 'Client', 'Catégorie', 'Priorité', 'Statut', 'Technicien', 'Date', 'Actions'],
                'rows': recentTickets
            } %}
        </div>
    </div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {{ encore_entry_script_tags('dashboard') }}
    <script>
        // Données pour les graphiques
        const chartData = {{ chartData|json_encode|raw }};
    </script>
{% endblock %}
```

### 5.4 components/_stat_card.html.twig

```twig
<div class="stat-card">
    <div class="stat-card-body">
        <div class="stat-icon-container">
            <i class="fas {{ icon }} stat-icon text-{{ iconColor|default('primary') }}"></i>
        </div>
        <div class="stat-content">
            <h2 class="stat-value">{{ value|number_format(0, ',', ' ') }}</h2>
            <p class="stat-label">{{ label }}</p>
            {% if change is defined or changeLabel is defined %}
                <div class="stat-change {{ changeType|default('neutral') }}">
                    {% if change is defined %}
                        <i class="fas fa-arrow-{{ changeType == 'positive' ? 'up' : 'down' }}"></i>
                        {{ change }}
                    {% endif %}
                    {% if changeLabel is defined %}
                        <span>{{ changeLabel }}</span>
                    {% endif %}
                </div>
            {% endif %}
        </div>
    </div>
</div>
```

## 6. Contrôleurs Symfony

### 6.1 DashboardController.php

```php
<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private TicketRepository $ticketRepository,
        private InterventionRepository $interventionRepository
    ) {}

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        // Récupération des statistiques
        $stats = [
            'totalTickets' => $this->ticketRepository->countActiveTickets(),
            'pendingTickets' => $this->ticketRepository->countByStatus('pending'),
            'resolvedToday' => $this->ticketRepository->countResolvedToday(),
            'criticalTickets' => $this->ticketRepository->countByPriority('critical')
        ];

        // Tickets récents
        $recentTickets = $this->ticketRepository->findRecent(10);

        // Données pour les graphiques
        $chartData = [
            'ticketsEvolution' => $this->getTicketsEvolutionData(),
            'categoryDistribution' => $this->getCategoryDistributionData()
        ];

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
            'chartData' => $chartData,
            'pendingTicketsCount' => $stats['pendingTickets'] // Pour le badge sidebar
        ]);
    }

    private function getTicketsEvolutionData(): array
    {
        $data = $this->ticketRepository->getEvolutionData(7); // 7 derniers jours
        
        return [
            'labels' => array_column($data, 'date'),
            'datasets' => [
                [
                    'label' => 'Nouveaux',
                    'data' => array_column($data, 'new'),
                    'borderColor' => '#ff6b35',
                    'backgroundColor' => 'rgba(255, 107, 53, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Résolus',
                    'data' => array_column($data, 'resolved'),
                    'borderColor' => '#4caf50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    private function getCategoryDistributionData(): array
    {
        $data = $this->ticketRepository->getCategoryDistribution();
        
        return [
            'labels' => array_keys($data),
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => [
                    '#ff6b35',
                    '#4caf50',
                    '#2196f3',
                    '#ffc107',
                    '#9c27b0'
                ]
            ]]
        ];
    }
}
```

### 6.2 TicketController.php

```php
<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findAllWithRelations();
        
        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'pendingTicketsCount' => $ticketRepository->countByStatus('pending')
        ]);
    }

    #[Route('/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setStatus('nouveau');
            
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Ticket créé avec succès.');

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/take', name: 'ticket_take', methods: ['POST'])]
    public function take(Ticket $ticket, EntityManagerInterface $em): Response
    {
        $ticket->setTechnician($this->getUser());
        $ticket->setStatus('en_cours');
        $ticket->setUpdatedAt(new \DateTimeImmutable());
        
        $em->flush();

        $this->addFlash('success', 'Ticket pris en charge.');

        return $this->redirectToRoute('ticket_index');
    }
}
```

## 7. Assets CSS/JS

### 7.1 assets/styles/app.scss

```scss
// Variables
@import 'variables';

// Bootstrap
@import '~bootstrap/scss/bootstrap';

// Font Awesome
@import '~@fortawesome/fontawesome-free/css/all.css';

// Animate.css
@import '~animate.css';

// Custom styles
@import 'sidebar';
@import 'dashboard';
@import 'components';

// Base styles
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--bg-dark);
    color: var(--text-primary);
}

.app-wrapper {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    margin-left: 250px;
    transition: margin-left 0.3s ease;
}

.page-content {
    padding: 30px;
}

// Dark theme
.dark-theme {
    --bg-dark: #0f111a;
    --bg-card: #1a1d29;
    --bg-hover: rgba(255, 255, 255, 0.05);
    --text-primary: #ffffff;
    --text-secondary: #a0a0a0;
    --border-color: #2a2d3a;
    --primary: #ff6b35;
    --success: #4caf50;
    --warning: #ffc107;
    --danger: #f44336;
    --info: #2196f3;
}

// Animations
.animate__animated {
    animation-duration: 0.5s;
}

// Scrollbar
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-dark);
}

::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}
```

### 7.2 assets/styles/_components.scss

```scss
// Stat Cards
.stat-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 24px;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    
    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    &::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
    }
    
    &:hover::before {
        left: 100%;
    }
}

.stat-card-body {
    position: relative;
    z-index: 1;
}

.stat-icon-container {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
}

.stat-icon {
    font-size: 48px;
    opacity: 0.2;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--text-primary);
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-change {
    font-size: 12px;
    
    &.positive {
        color: var(--success);
    }
    
    &.negative {
        color: var(--danger);
    }
    
    &.neutral {
        color: var(--text-secondary);
    }
}

// Dashboard Cards
.dashboard-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 20px;
    }
    
    .card-title {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 18px;
    }
    
    .card-body {
        padding: 20px;
    }
}

// Data Tables
.data-table {
    width: 100%;
    
    thead {
        th {
            background: rgba(255, 107, 53, 0.1);
            border-bottom: 2px solid var(--primary);
            color: var(--text-primary);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            padding: 16px;
        }
    }
    
    tbody {
        tr {
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s;
            
            &:hover {
                background-color: var(--bg-hover);
            }
        }
        
        td {
            padding: 16px;
            color: var(--text-primary);
            vertical-align: middle;
        }
    }
}

// Badges
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-status {
    &.low {
        background: rgba(76, 175, 80, 0.2);
        color: var(--success);
    }
    
    &.normal {
        background: rgba(33, 150, 243, 0.2);
        color: var(--info);
    }
    
    &.high {
        background: rgba(255, 193, 7, 0.2);
        color: var(--warning);
    }
    
    &.critical {
        background: rgba(244, 67, 54, 0.2);
        color: var(--danger);
    }
}

// Buttons
.btn-primary {
    background: var(--primary);
    border: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
    
    &:hover {
        background: darken(#ff6b35, 10%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }
}

.btn-action {
    background: rgba(76, 175, 80, 0.2);
    color: var(--success);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
    
    &:hover {
        background: rgba(76, 175, 80, 0.3);
        color: var(--success);
    }
}
```

### 7.3 assets/js/dashboard.js

```javascript
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
            data: chartData.ticketsEvolution,
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
            data: chartData.categoryDistribution,
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
    
    // Actualisation automatique toutes les 30 secondes
    setInterval(refreshDashboard, 30000);
});

// Animation des compteurs
function animateStats() {
    const statValues = document.querySelectorAll('.stat-value');
    
    statValues.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
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

// Actualisation du dashboard
async function refreshDashboard() {
    try {
        const response = await fetch('/api/dashboard/stats');
        const data = await response.json();
        
        // Mettre à jour les statistiques
        updateStats(data.stats);
        
        // Afficher une notification si de nouveaux tickets critiques
        if (data.newCriticalTickets > 0) {
            showNotification('Nouveaux tickets critiques', 'danger');
        }
    } catch (error) {
        console.error('Erreur lors de l\'actualisation:', error);
    }
}

// Mise à jour des statistiques
function updateStats(stats) {
    Object.keys(stats).forEach(key => {
        const element = document.querySelector(`[data-stat="${key}"]`);
        if (element) {
            const oldValue = parseInt(element.textContent);
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

// Notification système
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} animate__animated animate__fadeInRight`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

// Export des fonctions pour utilisation externe
window.dashboardUtils = {
    animateStats,
    refreshDashboard,
    showNotification
};
```

## 8. Composants réutilisables

### 8.1 Modal de création de ticket

```twig
{# templates/components/_ticket_modal.html.twig #}
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Nouveau Ticket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ path('ticket_new') }}" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control bg-dark text-white" name="title" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client</label>
                            <select class="form-select bg-dark text-white" name="client_id" required>
                                <option value="">Sélectionner un client</option>
                                {% for client in clients %}
                                    <option value="{{ client.id }}">{{ client.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select bg-dark text-white" name="category" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="materiel">Matériel</option>
                                <option value="logiciel">Logiciel</option>
                                <option value="reseau">Réseau</option>
                                <option value="securite">Sécurité</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priorité</label>
                            <select class="form-select bg-dark text-white" name="priority" required>
                                <option value="low">Basse</option>
                                <option value="normal" selected>Normale</option>
                                <option value="high">Haute</option>
                                <option value="critical">Critique</option>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control bg-dark text-white" name="description" rows="4" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Créer le ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

### 8.2 Toast de notification

```twig
{# templates/components/_toast.html.twig #}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast" role="alert">
        <div class="toast-header bg-dark text-white">
            <i class="fas fa-info-circle me-2 text-primary"></i>
            <strong class="me-auto">Notification</strong>
            <small>À l'instant</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body bg-dark text-white">
            <!-- Message dynamique -->
        </div>
    </div>
</div>

<script>
function showToast(message, type = 'info') {
    const toast = document.getElementById('liveToast');
    const toastBody = toast.querySelector('.toast-body');
    const toastIcon = toast.querySelector('.toast-header i');
    
    // Mise à jour du contenu
    toastBody.textContent = message;
    
    // Mise à jour de l'icône selon le type
    toastIcon.className = `fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2 text-${type}`;
    
    // Affichage du toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>
```

## 9. Intégration des graphiques

### 9.1 Configuration avancée de Chart.js

```javascript
// assets/js/charts-config.js
export const defaultChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                    family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    size: 12
                },
                color: '#a0a0a0'
            }
        },
        tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(26, 29, 41, 0.95)',
            titleColor: '#ffffff',
            bodyColor: '#a0a0a0',
            borderColor: '#2a2d3a',
            borderWidth: 1,
            padding: 12,
            cornerRadius: 8,
            displayColors: true,
            bodyFont: {
                size: 13
            },
            titleFont: {
                size: 14,
                weight: '600'
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: true,
                color: 'rgba(255, 255, 255, 0.05)',
                drawBorder: false
            },
            ticks: {
                color: '#a0a0a0',
                font: {
                    size: 11
                }
            }
        },
        y: {
            grid: {
                display: true,
                color: 'rgba(255, 255, 255, 0.05)',
                drawBorder: false
            },
            ticks: {
                color: '#a0a0a0',
                font: {
                    size: 11
                }
            },
            beginAtZero: true
        }
    }
};

// Graphique mixte (barres + ligne)
export function createMixedChart(canvasId, data) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    type: 'bar',
                    label: data.barLabel,
                    data: data.barData,
                    backgroundColor: 'rgba(255, 107, 53, 0.8)',
                    borderColor: '#ff6b35',
                    borderWidth: 0,
                    borderRadius: 8,
                    barThickness: 40
                },
                {
                    type: 'line',
                    label: data.lineLabel,
                    data: data.lineData,
                    borderColor: '#4caf50',
                    backgroundColor: 'transparent',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#4caf50',
                    tension: 0.4
                }
            ]
        },
        options: {
            ...defaultChartOptions,
            scales: {
                ...defaultChartOptions.scales,
                y: {
                    ...defaultChartOptions.scales.y,
                    position: 'left',
                    title: {
                        display: true,
                        text: data.yAxisLabel,
                        color: '#a0a0a0'
                    }
                }
            }
        }
    });
}
```

## 10. Bonnes pratiques

### 10.1 Structure et organisation

1. **Séparation des responsabilités**
   - Templates Twig : présentation uniquement
   - Contrôleurs : logique métier légère
   - Services : logique métier complexe
   - Repository : requêtes base de données

2. **Réutilisabilité**
   - Créer des composants Twig réutilisables
   - Utiliser l'héritage de templates
   - Centraliser les styles communs

### 10.2 Performance

```twig
{# Optimisation des requêtes #}
{% for ticket in tickets %}
    {# Éviter les requêtes N+1 #}
    {{ ticket.client.name }} {# Utiliser jointure dans repository #}
{% endfor %}

{# Cache Twig #}
{% cache 'sidebar' ~ app.user.id %}
    {% include '_partials/_sidebar.html.twig' %}
{% endcache %}
```

### 10.3 Sécurité

```twig
{# Toujours échapper les données utilisateur #}
{{ ticket.description|e }}

{# Vérification des permissions #}
{% if is_granted('ROLE_ADMIN') %}
    <button class="btn btn-danger">Supprimer</button>
{% endif %}

{# Protection CSRF #}
{{ csrf_token('delete' ~ ticket.id) }}
```

### 10.4 Responsive Design

```scss
// Breakpoints
$breakpoints: (
    'xs': 0,
    'sm': 576px,
    'md': 768px,
    'lg': 992px,
    'xl': 1200px,
    'xxl': 1400px
);

// Mixin responsive
@mixin respond-to($breakpoint) {
    @media (min-width: map-get($breakpoints, $breakpoint)) {
        @content;
    }
}

// Utilisation
.sidebar {
    width: 100%;
    transform: translateX(-100%);
    
    @include respond-to('lg') {
        width: 250px;
        transform: translateX(0);
    }
}
```

### 10.5 Tests

```php
// tests/Controller/DashboardControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    public function testDashboardAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tableau de bord');
    }
    
    public function testStatsDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/dashboard');
        
        // Vérifier l'affichage des stats
        $this->assertCount(4, $crawler->filter('.stat-card'));
    }
}
```

## Commandes utiles

```bash
# Compilation des assets
npm run dev        # Mode développement
npm run watch      # Mode watch
npm run build      # Mode production

# Cache Symfony
php bin/console cache:clear
php bin/console cache:warmup

# Base de données
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Debug
php bin/console debug:router
php bin/console debug:twig
```

## Ressources supplémentaires

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation Twig](https://twig.symfony.com/doc/3.x/)
- [Bootstrap 5](https://getbootstrap.com/docs/5.3/)
- [Chart.js](https://www.chartjs.org/docs/latest/)
- [Font Awesome](https://fontawesome.com/icons)

---
