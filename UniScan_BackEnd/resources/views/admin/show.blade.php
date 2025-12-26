<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Candidature - UniScan</title>
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
            margin-bottom: 24px;
        }
        .card-header {
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        .btn-back {
            color: #6c757d;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .btn-back:hover {
            color: #0d6efd;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: #6c757d;
            letter-spacing: 0.5px;
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
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">🎓 UniScan Admin</a>
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

    <div class="container mb-5">
        
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('admin.dashboard') }}" class="btn-back me-3">
                ← Retour au tableau de bord
            </a>
            <div class="ms-auto text-muted small">
                Dossier #{{ $application->id }} • Soumis le {{ $application->created_at->format('d/m/Y') }}
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header bg-white text-primary border-bottom">
                        📄 Document Scanné
                    </div>
                    <div class="card-body text-center d-flex align-items-center justify-content-center bg-light rounded-bottom">
                        @if($doc)
                            <img src="{{ asset('storage/' . $doc->chemin_fichier) }}" class="img-fluid rounded shadow-sm" style="max-height: 500px;" alt="Document">
                        @else
                            <div class="text-center py-5">
                                <h1 class="display-4 text-muted">🚫</h1>
                                <p class="text-danger fw-bold">Aucun document trouvé</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header bg-white text-dark border-bottom d-flex justify-content-between align-items-center">
                        <span> Analyse Intelligente (IA)</span>
                        @if($application->status == 'valide')
                            <span class="badge bg-success status-badge">Dossier Validé</span>
                        @elseif($application->status == 'rejete')
                            <span class="badge bg-danger status-badge">Dossier Rejeté</span>
                        @else
                            <span class="badge bg-warning text-dark status-badge">En cours</span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        
                        <h6 class="text-uppercase text-muted fw-bold mb-3 small">🔍 Comparaison Saisie vs OCR</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Champ</th>
                                        <th style="width: 35%;">Saisi (Étudiant)</th>
                                        <th style="width: 30%;">Détecté (IA)</th>
                                        <th style="width: 15%;" class="text-center">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-bold">Nom</td>
                                        <td class="fw-bold">{{ $application->nom_saisi }}</td>
                                        <td class="text-primary">{{ $ocrData['nom'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(isset($ocrData['nom']) && strtoupper(trim($application->nom_saisi)) == strtoupper(trim($ocrData['nom'])))
                                                <span class="badge bg-success status-badge">Valide</span>
                                            @else
                                                <span class="badge bg-danger status-badge">Erreur</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-muted fw-bold">Prénom</td>
                                        <td class="fw-bold">{{ $application->prenom_saisi }}</td>
                                        <td class="text-primary">{{ $ocrData['prenom'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(isset($ocrData['prenom']) && strtoupper(trim($application->prenom_saisi)) == strtoupper(trim($ocrData['prenom'])))
                                                <span class="badge bg-success status-badge">Valide</span>
                                            @else
                                                <span class="badge bg-danger status-badge">Erreur</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-muted fw-bold">CNE</td>
                                        <td class="fw-bold">{{ $application->cne_saisi }}</td>
                                        <td class="text-primary">{{ $ocrData['cne'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(isset($ocrData['cne']) && strtoupper(trim($application->cne_saisi)) == strtoupper(trim($ocrData['cne'])))
                                                <span class="badge bg-success status-badge">Valide</span>
                                            @else
                                                <span class="badge bg-danger status-badge">Erreur</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-muted fw-bold">Année Bac</td>
                                        <td class="fw-bold">{{ $application->annee_bac_saisie }}</td>
                                        <td class="text-primary">{{ $ocrData['annee_bac'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(isset($ocrData['annee_bac']) && $application->annee_bac_saisie == $ocrData['annee_bac'])
                                                <span class="badge bg-success status-badge">Valide</span>
                                            @else
                                                <span class="badge bg-danger status-badge">Erreur</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-muted fw-bold">Note Bac</td>
                                        <td class="fw-bold fs-5">{{ $application->note_bac_saisie }}</td>
                                        <td class="text-primary fw-bold">{{ $ocrData['note_bac'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(isset($ocrData['note_bac']) && abs($application->note_bac_saisie - $ocrData['note_bac']) < 0.1)
                                                <span class="badge bg-success status-badge">Valide</span>
                                            @else
                                                <span class="badge bg-danger status-badge">Erreur</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4">
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-danger btn-lg me-md-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                 Rejeter
                            </button>
                            
                            <form action="{{ route('admin.validate', $application->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                                     Valider le dossier
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5 mb-4 text-muted small">
            &copy; {{ date('Y') }} UniScan - Système de gestion universitaire
        </div>

    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.reject', $application->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title fw-bold">Refus du dossier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-3">
                            Veuillez justifier le rejet de ce dossier. L'étudiant recevra ce motif.
                        </p>
                        <div class="mb-3">
                            <label for="motif_rejet" class="form-label fw-bold text-dark">Motif du rejet :</label>
                            <textarea class="form-control bg-light" name="motif_rejet" rows="4" required placeholder="Ex: Incohérence majeure entre la note saisie et le relevé..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmer le rejet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>