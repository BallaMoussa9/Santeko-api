<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Backup; // Assurez-vous d'importer le modèle Backup
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BackupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan; // Pour simuler le déclenchement réel
use Carbon\Carbon;

class BackupController extends Controller
{
    // Toutes les méthodes nécessitent le rôle 'admin'
    // public function __construct()
    // {
    //     $this->middleware('admin'); // Un middleware 'admin' serait idéal ici
    // }

    /**
     * Display a listing of the resource.
     * Accessible uniquement aux administrateurs.
     */
    public function index(): JsonResponse
    {
        $backups = Backup::all();
        return response()->json($backups);
    }

    /**
     * Store a newly created resource in storage.
     * Cette méthode est principalement pour enregistrer les métadonnées d'une sauvegarde déclenchée.
     * En général, une sauvegarde n'est pas "créée" manuellement par un POST avec des détails complets,
     * mais plutôt "déclenchée", et le système enregistre ensuite les détails.
     */
    public function store(BackupRequest $request): JsonResponse
    {
        // Dans un cas réel, cette méthode pourrait être utilisée pour enregistrer les métadonnées
        // d'une sauvegarde qui a été initiée par une autre fonction (ex: un Job).
        // Pour cet exemple, nous allons la laisser simple ou l'utiliser indirectement via `triggerBackup`.
        $backup = Backup::create($request->validated());
        return response()->json($backup, 201);
    }

    /**
     * Display the specified resource.
     * Accessible uniquement aux administrateurs.
     */
    public function show(Backup $backup): JsonResponse
    {
        return response()->json($backup);
    }

    /**
     * Update the specified resource in storage.
     * Accessible uniquement aux administrateurs.
     */
    public function update(BackupRequest $request, Backup $backup): JsonResponse
    {
        $backup->update($request->validated());
        return response()->json($backup);
    }

    /**
     * Remove the specified resource from storage.
     * Accessible uniquement aux administrateurs.
     */
    public function destroy(Backup $backup): JsonResponse
    {
        $backup->delete();
        return response()->json(['message' => 'Enregistrement de sauvegarde supprimé avec succès.']);
    }

    /**
     * Endpoint pour déclencher manuellement une nouvelle sauvegarde.
     * Accessible uniquement aux administrateurs.
     */
    public function triggerBackup(Request $request): JsonResponse
    {
        // Créer une entrée initiale "en cours"
        $backup = Backup::create([
            'status' => 'in_progress',
            'type' => $request->input('type', 'full'), // database, files, full
            'last_run_at' => Carbon::now(),
            'notes' => 'Sauvegarde manuellement déclenchée.',
        ]);

        // --- SIMULATION D'UNE SAUVEGARDE RÉELLE ---
        // Dans une application réelle, vous lanceriez ici un Job Laravel
        // qui exécuterait la commande de sauvegarde en arrière-plan.
        // Exemple avec spatie/laravel-backup:
        // Bus::dispatch(new RunBackupJob());

        // Pour l'exemple, nous allons simuler une exécution rapide
        // et mettre à jour le statut après un court délai (ou directement)
        try {
            // Ici, vous pourriez appeler une commande Artisan ou un service de sauvegarde
            // Par exemple: Artisan::call('backup:run');
            // Ou un service personnalisé: app(BackupService::class)->runBackup();

            // Simulation réussie
            $backup->update([
                'status' => 'successful',
                'filename' => 'backup-' . Carbon::now()->format('Y-m-d_H-i-s') . '.zip', // Nom fictif
                'path' => '/app/backups/', // Chemin fictif
                'size' => rand(1000000, 50000000), // Taille fictive en bytes
                'notes' => 'Sauvegarde terminée avec succès.',
            ]);
            return response()->json(['message' => 'Sauvegarde déclenchée et réussie.', 'backup' => $backup], 200);

        } catch (\Exception $e) {
            // Simulation échouée
            $backup->update([
                'status' => 'failed',
                'notes' => 'La sauvegarde a échoué: ' . $e->getMessage(),
            ]);
            return response()->json(['message' => 'La sauvegarde a échoué.', 'error' => $e->getMessage(), 'backup' => $backup], 500);
        }
    }
}
