// Script pour tester l'authentification JWT
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const apiTestBtn = document.getElementById('api-test-btn');
    const resultDiv = document.getElementById('result');
    
    // Préremplir avec un utilisateur valide des fixtures
    document.getElementById('email').value = 'jean.dupont@3innov.fr';
    document.getElementById('password').value = 'user123';
    
    let jwtToken = localStorage.getItem('jwt_token');
    
    // Mettre à jour l'interface en fonction de l'état de connexion
    function updateUI() {
        if (jwtToken) {
            document.getElementById('login-section').style.display = 'none';
            document.getElementById('api-section').style.display = 'block';
        } else {
            document.getElementById('login-section').style.display = 'block';
            document.getElementById('api-section').style.display = 'none';
        }
    }
    
    // Initialiser l'interface
    updateUI();
    
    // Gérer la soumission du formulaire de connexion
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        // Afficher les données envoyées pour le débogage
        console.log('Tentative de connexion avec:', { email, password });
        
        fetch('/api/login_check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                email: email,
                password: password
            })
        })
        .then(response => {
            console.log('Statut de la réponse:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (data.token) {
                jwtToken = data.token;
                localStorage.setItem('jwt_token', jwtToken);
                resultDiv.innerHTML = '<div class="alert alert-success">Connexion réussie! Token JWT obtenu.</div>';
                updateUI();
            } else {
                resultDiv.innerHTML = '<div class="alert alert-danger">Échec de la connexion: ' + (data.message || JSON.stringify(data)) + '</div>';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            resultDiv.innerHTML = '<div class="alert alert-danger">Erreur: ' + error.message + '</div>';
        });
    });
    
    // Tester l'accès à l'API avec le token JWT
    apiTestBtn.addEventListener('click', function() {
        fetch('/api', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + jwtToken
            }
        })
        .then(response => {
            console.log('Statut de la réponse API:', response.status);
            if (!response.ok) {
                throw new Error('Réponse du serveur: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données API reçues:', data);
            resultDiv.innerHTML = '<div class="alert alert-success">Accès API réussi!</div><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            console.error('Erreur API:', error);
            resultDiv.innerHTML = '<div class="alert alert-danger">Erreur d\'accès à l\'API: ' + error.message + '</div>';
        });
    });
    
    // Bouton de déconnexion
    document.getElementById('logout-btn').addEventListener('click', function() {
        localStorage.removeItem('jwt_token');
        jwtToken = null;
        resultDiv.innerHTML = '<div class="alert alert-info">Déconnecté</div>';
        updateUI();
    });
});
