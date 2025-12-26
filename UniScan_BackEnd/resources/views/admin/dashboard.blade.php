<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - UniScan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #6c757d;
        }
        .status-badge {
            font-size: 0.85em;
            padding: 6px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">🎓 UniScan Admin</a>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Bonjour, {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light text-primary fw-bold">Se déconnecter</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Succès !</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erreur !</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>📂 Gestion des Candidatures</h3>
            <span class="badge bg-secondary fs-6">{{ count($applications) }} dossier(s) trouvé(s)</span>
        </div>

        <div class="card mb-4">
            <div class="card-body bg-white">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Filière</label>
                        <select name="filiere_id" class="form-select">
                            <option value="">-- Toutes les filières --</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom_filiere }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Statut du dossier</label>
                        <select name="status" class="form-select">
                            <option value="">-- Tous les statuts --</option>
                            <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente </option>
                            <option value="validé" {{ request('status') == 'validé' ? 'selected' : '' }}>Validé </option>
                            <option value="rejeté" {{ request('status') == 'rejeté' ? 'selected' : '' }}>Rejeté </option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                             Filtrer
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4"># ID</th>
                                <th>Date Soumission</th>
                                <th>Étudiant</th>
                                <th>Filière demandée</th>
                                <th>Note Bac</th>
                                <th class="text-center">Statut</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $app->id }}</td>
                                    <td>
                                        <small class="text-muted">{{ $app->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $app->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $app->user->name }}</span>
                                            <span class="small text-muted">{{ $app->user->email }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $app->filiere?->nom_filiere ?? 'Non défini' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ $app->note_bac_saisie }} / 20
                                    </td>
                                    <td class="text-center">
                                        @if($app->status === 'valide')
                                            <span class="badge bg-success status-badge">Validé</span>
                                        @elseif($app->status === 'rejete')
                                            <span class="badge bg-danger status-badge">Rejeté</span>
                                        @else
                                            <span class="badge bg-warning text-dark status-badge">En attente</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.show', $app->id) }}" class="btn btn-sm btn-primary px-3">
                                            Examiner 
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <h4> Aucun dossier trouvé</h4>
                                            <p>Essaie de modifier tes filtres de recherche.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5 text-muted small">
            &copy; {{ date('Y') }} UniScan - Système de gestion universitaire
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>