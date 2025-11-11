<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Validation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class subscribeValidationController extends Controller
{
    # Function view subscribe page
    public function subscribeValidation(Request $request)
    {
        # log 
        logActivity(
            session('username'),
            'validation',
            'validation_page_visit',
        );
        return view('subscribeValidation');
    }

    # Function to send validation
    public function sendValidation(Request $request)
    {
        # Musoni 
        $api_username = env('API_USERNAME');
        $api_password = env('API_PASSWORD');
        $api_url = env('API_URL');

        # Gestion des erreurs Orange Money
        $errorMessages = [
            '302' => 'Requête Invalide',
            '303' => 'Erreur de format du numéro de ligne',
            '304' => 'Alias inconnu',
            '306' => 'Monnaie invalide',
            '307' => 'Code Service incorrecte',
            '601' => "L'alias existe déjà",
            '602' => "Délais de requête dépassé, relancer la demande de clé d'activation",
            '603' => "Clé d'activation expirée !",
        ];

        # Validation des inputs dans le formulaire
        $request->validate([
            'msisdn' => 'required|string|max:10',
            'key' => 'required|string|max:8',
        ]);
        $msisdn = $request->input('msisdn');
        $key = $request->input('key');

        $validations = DB::table('validation')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        # Récupération de toutes les clés existantes dans Subscription
        $active_keys = Subscription::pluck('key')->toArray();

        // Déterminer la clé de zone de référence: parent_name si disponible, sinon officeName
        $validator_officename = session('officeName');
        $validator_parentname = session('parent_name');
        $zoneLookupName = $validator_parentname ?: $validator_officename;
        # Ajout d'une information "active" à chaque validation
        foreach ($validations as $validation) {
            $validation->active = in_array($validation->key, $active_keys);
        }
        $get_zone_id = DB::table('zones')
            ->select('id')
            ->whereRaw(
                "REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?",
                $zoneLookupName
            )
            ->first();

        $allowed_offices = [];

        if ($get_zone_id) {
            # Si c'est une zone, récupérer toutes les agences qui en dépendent
            $get_agences = DB::table('agences')
                ->where('zone_id', $get_zone_id->id)
                ->pluck('nom')
                ->toArray();

            $allowed_offices = $get_agences;
        } else {
            # Sinon c'est juste une agence
            $allowed_offices = [$validator_officename];
        }

        $roles = [];

        $can = null;

        $current_user = session('username'); # matricule

        $user_id = session('id'); # Id user Musoni

        $user_office = session('officeName'); # __get user's office name__

        $user_roles = session('selectedRoles');

        # log 
        logActivity(
            session('username'),
            'validation',
            'validation_send_request',
        );

        return view('subscribeValidation', compact(
            'validations',
            'validator_officename',
            'current_user',
            'allowed_offices'
        ));
    }

    # Function to validate subscription
    public function doValidation(Request $request)
    {
        # Validation des inputs dans le formulaire
        $request->validate([
            'ticket' => 'required|string|max:15', # Ticket number to update value of status in table validation
            'commentaire' => 'required|string|min:10',
            'validation' => 'required|string',
        ]);

        $ticket = $request->input('ticket');

        $commentaire = $request->input('commentaire');

        $status = $request->input('validation');

        # Get current user
        $get_current_user = session('firstname') . ' ' . session('lastname');

        # Get key saved by id in validation
        $get_key = DB::table('validation')
            ->select('key')
            ->where('ticket', $ticket)
            ->first();

        $get_sub_status = DB::table('subscription')
            ->select('account_status')
            ->where('key', $get_key->key)
            ->first();

        try {
            $validation_update = DB::table('validation')
                ->where('ticket', $ticket)
                ->update([
                    'status' => $status,
                    'validator' => $get_current_user,
                    'motif_validation' => $commentaire
                ]);

            if ($validation_update > 0) {
                # log 
                logActivity(
                    session('username'),
                    'validation',
                    'validation_successfully_done',
                );
                return redirect()->back()->with('success', 'Demande enregistrée.');
            } else {
                # log 
                logActivity(
                    session('username'),
                    'validation',
                    'validation_error',
                );
                return redirect()->back()->with('error', 'Erreur de validation.');
            }
        } catch (\Exception $e) {
            Log::error("Erreur : " . $e->getMessage());
            # log 
            logActivity(
                session('username'),
                'validation',
                'validation_error_save_data',
            );
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('subscribeValidation', $get_sub_status);
    }
}
