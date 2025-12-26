<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - UniScan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .login-header {
            background-color: #4f46e5; /* Couleur UniScan */
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 20px;
            text-align: center;
        }
        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>

    <div class="card login-card">
        <div class="login-header">
            <h3 class="mb-0">🎓 UniScan Admin</h3>
            <small>Portail de gestion</small>
        </div>
        <div class="card-body p-4">
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf <div class="mb-3">
                    <label for="email" class="form-label text-muted">Adresse Email</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-control" 
                           placeholder="admin@uniscan.com" 
                           required 
                           autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-muted">Mot de passe</label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-control" 
                           placeholder="••••••••" 
                           required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center py-3 bg-white border-0">
            <small class="text-muted">Accès réservé au personnel autorisé</small>
        </div>
    </div>

</body>
</html>