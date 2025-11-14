<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
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
    // ============================================
    // FONCTIONS HELPER PRIVÉES
    // ============================================

    /**
     * Nettoie et normalise un nom d'office
     */
    private function cleanOfficeName($name) {
        if (!is_string($name)) return '';
        return trim(str_replace(["\n","\r","\t"], '', $name));
    }

    /**
     * Parse un office_name qui peut contenir plusieurs agences séparées par "-"
     */
    private function parseMultipleOffices($office_string) {
        if (empty($office_string)) return [];
        
        $cleaned = $this->cleanOfficeName($office_string);
        $offices = [];
        
        // Essayer d'abord avec " - " (avec espaces)
        if (strpos($cleaned, ' - ') !== false) {
            $parts = explode(' - ', $cleaned);
            foreach ($parts as $part) {
                $part = trim($part);
                if (!empty($part)) {
                    $offices[] = $part;
                }
            }
        } 
        // Sinon essayer avec "-" (sans espaces)
        elseif (strpos($cleaned, '-') !== false) {
            $parts = explode('-', $cleaned);
            foreach ($parts as $part) {
                $part = trim($part);
                if (!empty($part)) {
                    $offices[] = $part;
                }
            }
        } 
        // Sinon c'est un seul office
        else {
            if (!empty($cleaned)) {
                $offices[] = $cleaned;
            }
        }
        
        return $offices;
    }

    /**
     * Vérifie si un office_name correspond à un des offices autorisés
     * @param bool $strict Si true, utilise uniquement la correspondance exacte (pour les validateurs)
     */
    private function officeNameMatches($validation_office, $allowed_office, $strict = false) {
        $clean_validation = $this->cleanOfficeName($validation_office);
        $clean_allowed = $this->cleanOfficeName($allowed_office);
        
        // Correspondance exacte (insensible à la casse)
        if (strcasecmp($clean_validation, $clean_allowed) === 0) {
            return true;
        }
        
        // Si strict, ne pas faire de correspondance partielle
        if ($strict) {
            return false;
        }
        
        // Correspondance partielle (insensible à la casse) - seulement si pas strict
        if (!empty($clean_validation) && !empty($clean_allowed)) {
            // Parser les deux pour extraire les parties individuelles
            $validation_parts = $this->parseMultipleOffices($clean_validation);
            $allowed_parts = $this->parseMultipleOffices($clean_allowed);
            
            // Vérifier si au moins une partie correspond exactement (insensible à la casse)
            foreach ($validation_parts as $val_part) {
                $val_part = trim($val_part);
                if (empty($val_part)) continue;
                
                foreach ($allowed_parts as $allowed_part) {
                    $allowed_part = trim($allowed_part);
                    if (empty($allowed_part)) continue;
                    
                    // Correspondance exacte entre les parties
                    if (strcasecmp($val_part, $allowed_part) === 0) {
                        return true;
                    }
                    
                    // Correspondance partielle : si une partie contient l'autre
                    if (stripos($val_part, $allowed_part) !== false || 
                        stripos($allowed_part, $val_part) !== false) {
                        return true;
                    }
                }
            }
            
            // Vérifier aussi si l'un contient l'autre globalement (pour les cas simples)
            if (stripos($clean_validation, $clean_allowed) !== false || 
                stripos($clean_allowed, $clean_validation) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifie si un office_name est un office spécial
     */
    private function isSpecialOffice($office_name) {
        $special_offices = ['Head Office', 'Centre', 'Sud', 'Est', 'Ouest', 'Nord', 'CENTRE', 'SUD', 'EST', 'OUEST', 'NORD'];
        $cleaned = $this->cleanOfficeName($office_name);
        
        foreach ($special_offices as $special) {
            if (stripos($cleaned, $special) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Récupère toutes les agences d'une zone depuis la base de données
     */
    private function getAgencesFromZone($zone_name) {
        if (empty($zone_name)) {
            return [];
        }
        
        $cleaned_zone = $this->cleanOfficeName($zone_name);
        
        if (empty($cleaned_zone)) {
            return [];
        }
        
        // Utiliser DB::select() avec une requête brute pour garantir le binding correct
        try {
            // Essayer d'abord une correspondance exacte
            $sql = "SELECT id, nom FROM zones WHERE REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '')) = ? LIMIT 1";
            $results = DB::select($sql, [$cleaned_zone]);
            $zone = !empty($results) ? (object)$results[0] : null;
            
            // Si pas trouvé, essayer une correspondance insensible à la casse
            if (!$zone) {
                $upper_cleaned = strtoupper($cleaned_zone);
                $sql = "SELECT id, nom FROM zones WHERE UPPER(REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), ''))) = ? LIMIT 1";
                $results = DB::select($sql, [$upper_cleaned]);
                $zone = !empty($results) ? (object)$results[0] : null;
            }
            
            // Si toujours pas trouvé, essayer une correspondance partielle
            if (!$zone) {
                $upper_cleaned = strtoupper($cleaned_zone);
                $like_pattern = '%' . $upper_cleaned . '%';
                $sql = "SELECT id, nom FROM zones WHERE (
                    UPPER(REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), ''))) LIKE ? 
                    OR UPPER(?) LIKE CONCAT('%', UPPER(REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), ''))), '%')
                ) LIMIT 1";
                $results = DB::select($sql, [$like_pattern, $upper_cleaned]);
                $zone = !empty($results) ? (object)$results[0] : null;
            }
        } catch (\Exception $e) {
            Log::error('Error in getAgencesFromZone: ' . $e->getMessage(), [
                'zone_name' => $zone_name,
                'cleaned_zone' => $cleaned_zone,
                'error' => $e->getTraceAsString()
            ]);
            return [];
        }
        
        if ($zone && isset($zone->id)) {
            $agences = DB::table('agences')
                ->where('zone_id', $zone->id)
                ->pluck('nom')
                ->toArray();
            return array_map([$this, 'cleanOfficeName'], $agences);
        }
        
        return [];
    }

    /**
     * Récupère le nom d'une agence depuis la base de données
     */
    private function findAgenceInDB($agence_name) {
        $cleaned = $this->cleanOfficeName($agence_name);
        
        $agence = DB::table('agences')
            ->select('nom')
            ->where(function($query) use ($cleaned) {
                $query->whereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') LIKE ?", ['%' . $cleaned . '%'])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(TRIM(nom), CHAR(13), ''), CHAR(10), ''), CHAR(9), '') = ?", [$cleaned]);
            })
            ->first();
        
        if ($agence && isset($agence->nom)) {
            return $this->cleanOfficeName($agence->nom);
        }
        
        return $cleaned;
    }

    /**
     * Détermine les offices autorisés basés sur office_id, parentName et hierarchy
     */
    private function getAllowedOffices($is_network_director_or_chef = false) {
        $office_id = session('office_id');
        $office_name = session('officeName');
        $parent_name = session('parent_name');
        $hierarchy = session('hierarchy');

        $allowed_offices = [];
        $clean_office_name = $this->cleanOfficeName($office_name);
        $clean_parent_name = $this->cleanOfficeName($parent_name);

        // Cas 1: Office spécial (Head Office, Centre, etc.)
        // Pour DIRECTEUR DE RESEAU DAGENCES et CHEF DAGENCE, même si c'est une zone, on récupère les agences de cette zone
        // Pour les autres (Admin), on retourne *ALL* pour voir tout
        if ($this->isSpecialOffice($clean_office_name) || $this->isSpecialOffice($clean_parent_name)) {
            if ($is_network_director_or_chef) {
                // Pour DIRECTEUR DE RESEAU DAGENCES/CHEF DAGENCE, récupérer les agences de la zone
                $zone_name = $clean_office_name ?: $clean_parent_name;
                
                // Essayer de récupérer les agences de la zone
                $zone_agences = $this->getAgencesFromZone($zone_name);
                
                if (!empty($zone_agences)) {
                    // Agences trouvées, les retourner
                    return $zone_agences;
                }
                
                // Si pas d'agences trouvées, essayer aussi avec parent_name si différent
                if (!empty($clean_parent_name) && $clean_parent_name !== $zone_name) {
                    $zone_agences = $this->getAgencesFromZone($clean_parent_name);
                    if (!empty($zone_agences)) {
                        return $zone_agences;
                    }
                }
                
                // Si toujours pas d'agences, utiliser *ALL* pour permettre de voir toutes les demandes
                // car c'est un DIRECTEUR DE RESEAU DAGENCES avec une zone spéciale
                return ['*ALL*'];
            } else {
                // Pour les autres (Admin), voir tout
                return ['*ALL*']; // Marqueur spécial pour "toutes les agences"
            }
        }

        // Pour les validateurs normaux (pas DIRECTEUR DE RESEAU DAGENCES), extraire UNIQUEMENT l'agence exacte
        if (!$is_network_director_or_chef) {
            // Cas 2: parent_name existe (format "ZONE - AGENCE" ou "ZONE-AGENCE")
            if (!empty($clean_parent_name)) {
                // Si parent_name contient " - ", extraire uniquement la partie agence (la dernière partie)
                if (strpos($clean_parent_name, ' - ') !== false) {
                    $parts = explode(' - ', $clean_parent_name);
                    // Prendre la dernière partie qui est généralement l'agence
                    $agence_part = trim(end($parts));
                    if (!empty($agence_part) && !$this->isSpecialOffice($agence_part)) {
                        // Vérifier que ce n'est pas une zone
                        $zone_check = $this->getAgencesFromZone($agence_part);
                        if (empty($zone_check)) {
                            // C'est une agence, chercher dans la DB pour normaliser
                            $agence_name = $this->findAgenceInDB($agence_part);
                            $allowed_offices[] = $agence_name;
                        }
                    }
                } 
                // Sinon si parent_name contient "-" (sans espaces)
                elseif (strpos($clean_parent_name, '-') !== false) {
                    $parts = explode('-', $clean_parent_name);
                    $agence_part = trim(end($parts));
                    if (!empty($agence_part) && !$this->isSpecialOffice($agence_part)) {
                        $zone_check = $this->getAgencesFromZone($agence_part);
                        if (empty($zone_check)) {
                            $agence_name = $this->findAgenceInDB($agence_part);
                            $allowed_offices[] = $agence_name;
                        }
                    }
                }
                // Sinon, parent_name est directement l'agence
                else {
                    if (!$this->isSpecialOffice($clean_parent_name)) {
                        $zone_check = $this->getAgencesFromZone($clean_parent_name);
                        if (empty($zone_check)) {
                            $agence_name = $this->findAgenceInDB($clean_parent_name);
                            $allowed_offices[] = $agence_name;
                        }
                    }
                }
            }
            // Cas 3: Utiliser office_name directement (si parent_name n'existe pas)
            elseif (!empty($clean_office_name)) {
                // Vérifier que ce n'est pas une zone
                if (!$this->isSpecialOffice($clean_office_name)) {
                    $zone_check = $this->getAgencesFromZone($clean_office_name);
                    if (empty($zone_check)) {
                        // C'est probablement une agence
                        $agence_name = $this->findAgenceInDB($clean_office_name);
                        $allowed_offices[] = $agence_name;
                    }
                }
            }
        } 
        // Pour DIRECTEUR DE RESEAU DAGENCES et CHEF DAGENCE, utiliser la logique complète (zones + agences)
        else {
            // Cas 2: parent_name existe (format "ZONE - AGENCE - AGENCE2" ou "ZONE-AGENCE-AGENCE2")
            if (!empty($clean_parent_name)) {
                // Parser parent_name pour extraire les agences
                $parent_offices = $this->parseMultipleOffices($clean_parent_name);
                
                foreach ($parent_offices as $parent_office) {
                    // Vérifier si c'est une zone
                    $zone_agences = $this->getAgencesFromZone($parent_office);
                    if (!empty($zone_agences)) {
                        // C'est une zone, ajouter toutes ses agences
                        $allowed_offices = array_merge($allowed_offices, $zone_agences);
                    } else {
                        // C'est probablement une agence, chercher dans la DB
                        if (!$this->isSpecialOffice($parent_office)) {
                            $agence_name = $this->findAgenceInDB($parent_office);
                            if (!empty($agence_name) && !in_array($agence_name, $allowed_offices)) {
                                $allowed_offices[] = $agence_name;
                            }
                        }
                    }
                }
            }
            // Cas 3: Utiliser office_name directement (peut contenir plusieurs agences séparées par " - ")
            if (!empty($clean_office_name)) {
                // Parser office_name pour extraire les agences multiples (ex: "AMBANJA - ANTSIRANANA - AMBILOBE")
                $office_list = $this->parseMultipleOffices($clean_office_name);
                
                foreach ($office_list as $office) {
                    $office = trim($office);
                    if (empty($office)) continue;
                    
                    // Vérifier si c'est une zone
                    $zone_agences = $this->getAgencesFromZone($office);
                    if (!empty($zone_agences)) {
                        // C'est une zone, ajouter toutes ses agences
                        $allowed_offices = array_merge($allowed_offices, $zone_agences);
                    } else {
                        // C'est probablement une agence, chercher dans la DB
                        if (!$this->isSpecialOffice($office)) {
                            $agence_name = $this->findAgenceInDB($office);
                            if (!empty($agence_name) && !in_array($agence_name, $allowed_offices)) {
                                $allowed_offices[] = $agence_name;
                            }
                            // Ajouter aussi le nom brut au cas où il ne serait pas trouvé dans la DB
                            if (!in_array($office, $allowed_offices)) {
                                $allowed_offices[] = $office;
                            }
                        }
                    }
                }
            }
        }

        // Nettoyer et dédupliquer
        $allowed_offices = array_unique(array_filter($allowed_offices));

        // Fallback: si aucune agence trouvée, utiliser office_name brut (seulement si ce n'est pas une zone)
        if (empty($allowed_offices) && !empty($clean_office_name)) {
            // Pour les validateurs normaux, ne pas utiliser une zone comme fallback
            if (!$is_network_director_or_chef && $this->isSpecialOffice($clean_office_name)) {
                // C'est une zone et ce n'est pas un DIRECTEUR DE RESEAU DAGENCES, ne rien retourner
                return [];
            }
            // Sinon, utiliser office_name brut
            $allowed_offices = [$clean_office_name];
        }

        return $allowed_offices;
    }

    /**
     * Détermine le type d'accès de l'utilisateur
     */
    private function getUserAccess() {
        $user_roles = session('selectedRoles');
        $access = 0;
        $is_super_admin = false;
        $is_network_director_or_chef = false; // DIRECTEUR DE RESEAU DAGENCES ou CHEF DAGENCE

        if (empty($user_roles) || !is_array($user_roles)) {
            return ['access' => 0, 'is_super_admin' => false, 'is_network_director_or_chef' => false];
        }

        foreach ($user_roles as $role) {
            // Gérer différents formats de rôle
            $role_name = '';
            if (is_array($role)) {
                // Format: ['name' => 'CREATION CLIENT']
                $role_name = isset($role['name']) ? $role['name'] : (isset($role[0]) ? $role[0] : '');
            } else {
                // Format: 'CREATION CLIENT' ou 'cc'
                $role_name = $role;
            }
            
            // Normaliser le nom du rôle (insensible à la casse)
            $role_name_upper = strtoupper(trim($role_name));
            
            // IMPORTANT: Détection Validateur AVANT Admin pour éviter que "DIRECTEUR DE RESEAU DAGENCES" soit classé comme Admin
            // Détection Validateur - rôles spécifiques en premier
            if ($role_name_upper === 'DIRECTEUR DE RESEAU DAGENCES' || 
                    $role_name_upper === 'CHEF DAGENCE' || 
                    $role_name_upper === 'CHEF D AGENCE' ||
                    $role_name_upper === 'CHEF D\'AGENCE' ||
                    $role_name_upper === "APPROBATION 2 DU PRET_CHEF D'AGENCE" ||
                    $role_name_upper === "APPROBATION 2 DU PRET_CHEF D AGENCE" ||
                    $role_name_upper === 'APPROBATION 1 du PRET' || 
                    $role_name_upper === 'APPROBATION 2 du PRET' ||
                    $role_name_upper === 'APPROBATION 1 DU PRET_2' ||
                    $role_name_upper === "APPROBATION COMPTE" ||
                    $role_name_upper === "APPROBATION OPERATION DE CAISSE" ||
                    $role_name_upper === "ATTENTE APPROBATION 2 DU PRET" ||
                    $role_name_upper === 'VERIFICATEURS CENTRALISES' ||
                    stripos($role_name, 'directeur de reseau') !== false ||
                    stripos($role_name, 'directeur de reseau d\'agences') !== false ||
                    stripos($role_name, 'directeur de reseau dagences') !== false ||
                    stripos($role_name, 'chef d\'agence') !== false ||
                    stripos($role_name, 'chef dagence') !== false ||
                    stripos($role_name, 'chef d agence') !== false ||
                    (stripos($role_name, 'chef') !== false && stripos($role_name, 'agence') !== false) ||
                    (stripos($role_name, 'pret_chef') !== false && stripos($role_name, 'agence') !== false) ||
                    stripos($role_name, 'approbation') !== false ||
                    stripos($role_name, 'verificateur') !== false ||
                    stripos($role_name, 'juriste') !== false) {
                $access = 3; // Validateur
                
                // Vérifier si c'est DIRECTEUR DE RESEAU DAGENCES ou CHEF DAGENCE
                if ($role_name_upper === 'DIRECTEUR DE RESEAU DAGENCES' || 
                    $role_name_upper === 'CHEF DAGENCE' || 
                    $role_name_upper === 'CHEF D AGENCE' ||
                    $role_name_upper === 'CHEF D\'AGENCE' ||
                    $role_name_upper === "APPROBATION 2 DU PRET_CHEF D'AGENCE" ||
                    $role_name_upper === "APPROBATION 2 DU PRET_CHEF D AGENCE" ||
                    stripos($role_name, 'directeur de reseau') !== false ||
                    stripos($role_name, 'directeur de reseau d\'agences') !== false ||
                    stripos($role_name, 'directeur de reseau dagences') !== false ||
                    stripos($role_name, 'chef d\'agence') !== false ||
                    stripos($role_name, 'chef dagence') !== false ||
                    stripos($role_name, 'chef d agence') !== false ||
                    (stripos($role_name, 'chef') !== false && stripos($role_name, 'agence') !== false) ||
                    (stripos($role_name, 'pret_chef') !== false && stripos($role_name, 'agence') !== false)) {
                    $is_network_director_or_chef = true;
                }
                // Ne pas break ici, continuer à vérifier les autres rôles pour s'assurer qu'on détecte bien CHEF DAGENCE
            }
            // Détection CC - plusieurs variantes possibles
            elseif ($role_name_upper === 'CREATION CLIENT' ||
                $role_name_upper === 'CREATION CLIENT_2' ||
                $role_name_upper === 'CREATION COMPTE CAV' ||
                $role_name_upper === 'CREATION PRET' ||
                $role_name_upper === 'CREATION PRET_2' ||
                $role_name_upper === 'CC' || 
                stripos($role_name, 'creation') !== false || 
                stripos($role_name, 'client') !== false) {
                $access = 1; // CC
                break;
            } 
            // Détection Admin - APRÈS Validateur pour éviter les faux positifs
            elseif ($role_name_upper === 'DIRECTEUR' || 
                    $role_name_upper === 'INFORMATIQUE' || 
                    $role_name_upper === 'SUPER ADMIN' ||
                    stripos($role_name, 'admin') !== false) {
                // Vérifier que ce n'est PAS un "DIRECTEUR DE RESEAU DAGENCES" (déjà traité ci-dessus)
                if (stripos($role_name, 'directeur de reseau') === false && 
                    stripos($role_name, 'directeur de reseau d\'agences') === false &&
                    stripos($role_name, 'directeur de reseau dagences') === false) {
                    $access = 2; // Admin
                    if ($role_name_upper === 'SUPER ADMIN' || stripos($role_name, 'super admin') !== false) {
                        $is_super_admin = true;
                    }
                    break;
                }
            }
        }

        return [
            'access' => $access, 
            'is_super_admin' => $is_super_admin,
            'is_network_director_or_chef' => $is_network_director_or_chef
        ];
    }

    /**
     * Filtre les validations selon le type d'utilisateur
     */
    private function filterValidations($validations, $allowed_offices, $current_user, $access, $is_super_admin, $is_network_director_or_chef = false) {
        $filtered = [];

        foreach ($validations as $validation) {
            // Convertir en objet si c'est un array
            if (is_array($validation)) {
                $validation = (object) $validation;
            }
            
            $should_show = false;
            $validation_office = $this->cleanOfficeName($validation->office_name ?? '');

            // ADMIN - Voit TOUTES les demandes
            if ($access === 2) {
                $should_show = true;
            }
            // VALIDATEUR
            elseif ($access === 3) {
                    // DIRECTEUR DE RESEAU DAGENCES et CHEF DAGENCE : voient toutes les demandes (0, 1, 2) de leurs agences
                    if ($is_network_director_or_chef) {
                        // Utiliser allowed_offices qui contient déjà toutes les agences de leur zone/agence
                        if (in_array('*ALL*', $allowed_offices)) {
                            // Office spécial (zone comme Centre, Sud, Nord) - voir toutes les demandes
                            // Pour les DIRECTEURS DE RESEAU DAGENCES, ils doivent voir toutes les demandes en attente
                            // mais aussi les validées/refusées pour l'historique
                            $status = $validation->status ?? null;
                            $is_pending = ($status === "0" || $status === 0 || $status == 0);
                            $is_validated_or_refused = ($status === "1" || $status === 1 || $status == 1 || 
                                                        $status === "2" || $status === 2 || $status == 2);
                            // Voir toutes les demandes (en attente, validées, refusées)
                            $should_show = $is_pending || $is_validated_or_refused;
                        } else {
                            // Vérifier si validation->office_name correspond à une des agences autorisées
                            $matches_office = false;
                            
                            // Parser aussi validation_office pour comparer les parties individuelles
                            $validation_parts = $this->parseMultipleOffices($validation_office);
                            
                            foreach ($allowed_offices as $allowed_office) {
                                $clean_allowed = $this->cleanOfficeName($allowed_office);
                                
                                // Pour CHEF DAGENCE/DIRECTEUR DE RESEAU DAGENCES, utiliser un matching plus flexible (pas strict)
                                if ($this->officeNameMatches($validation_office, $allowed_office, false)) {
                                    $matches_office = true;
                                    break;
                                }
                                
                                // Vérifier aussi si une partie de validation_office correspond à allowed_office
                                foreach ($validation_parts as $val_part) {
                                    $val_part = trim($this->cleanOfficeName($val_part));
                                    if (!empty($val_part) && strcasecmp($val_part, $clean_allowed) === 0) {
                                        $matches_office = true;
                                        break 2; // Sortir des deux boucles
                                    }
                                }
                            }
                            // Montrer toutes les demandes (0, 1, 2) qui correspondent aux agences autorisées
                            $should_show = $matches_office;
                        }
                    }
                // Autres validateurs : voient les demandes EN ATTENTE (status = 0) ET validées/refusées (status = 1 ou 2) de leurs agences
                else {
                    $status = $validation->status ?? null;
                    $is_pending = ($status === "0" || $status === 0 || $status == 0);
                    $is_validated_or_refused = ($status === "1" || $status === 1 || $status == 1 || 
                                                $status === "2" || $status === 2 || $status == 2);
                    
                    // Voir les demandes en attente OU validées/refusées
                    $should_show_status = $is_pending || $is_validated_or_refused;
                    
                    if (in_array('*ALL*', $allowed_offices)) {
                        $should_show = $should_show_status;
                    } else {
                        $matches_office = false;
                        foreach ($allowed_offices as $allowed_office) {
                            // Utiliser un matching strict pour les validateurs (correspondance exacte uniquement)
                            // Nettoyer les deux noms avant comparaison
                            $clean_validation_office = $this->cleanOfficeName($validation_office);
                            $clean_allowed_office = $this->cleanOfficeName($allowed_office);
                            
                            // Correspondance exacte (insensible à la casse)
                            if (strcasecmp($clean_validation_office, $clean_allowed_office) === 0) {
                                $matches_office = true;
                                break;
                            }
                        }
                        $should_show = $matches_office && $should_show_status;
                    }
                }
            }
            // CC - Voit les demandes de ses agences OU ses propres demandes (tous statuts)
            elseif ($access === 1) {
                if (in_array('*ALL*', $allowed_offices)) {
                    $should_show = true;
                } else {
                    // Vérifier si c'est la demande de l'utilisateur actuel
                    $is_own_request = false;
                    if (!empty($validation->bank_agent)) {
                        // bank_agent contient le nom complet (firstname + lastname)
                        // Comparer avec le nom complet de session
                        $user_fullname = trim((session('firstname') ?? '') . ' ' . (session('lastname') ?? ''));
                        $clean_bank_agent = $this->cleanOfficeName($validation->bank_agent);
                        $clean_user_fullname = $this->cleanOfficeName($user_fullname);
                        
                        // Correspondance exacte du nom complet
                        if (!empty($clean_user_fullname)) {
                            $is_own_request = ($clean_bank_agent === $clean_user_fullname);
                        }
                        
                        // Aussi vérifier avec le username (matricule) au cas où
                        if (!$is_own_request && !empty($current_user)) {
                            $is_own_request = (stripos($validation->bank_agent, $current_user) !== false);
                        }
                    }
                    
                    // Vérifier si l'office de la validation correspond à une agence autorisée
                    $matches_office = false;
                    foreach ($allowed_offices as $allowed_office) {
                        if ($this->officeNameMatches($validation_office, $allowed_office)) {
                            $matches_office = true;
                            break;
                        }
                    }
                    
                    $should_show = $matches_office || $is_own_request;
                }
            }

            if ($should_show) {
                // Convertir en array pour pouvoir le stocker
                $filtered[] = (array) $validation;
            }
        }

        return $filtered;
    }

    // ============================================
    // MÉTHODES PUBLIQUES - ROUTES
    // ============================================

    /**
     * Route pour CC - Mes Demandes
     */
    public function showCCValidations(Request $request)
    {
        logActivity(session('username'), 'validation', 'cc_validation_page_visit');

        $current_user = session('username');
        $access_info = $this->getUserAccess();
        
        // Debug temporaire
        if (request()->has('debug') || session('debug_validation')) {
            $debug_info = [
                'selectedRoles' => session('selectedRoles'),
                'access_info' => $access_info,
                'current_user' => $current_user
            ];
            if (request()->has('debug')) {
                dd($debug_info);
            }
        }
        
        // Vérifier que l'utilisateur est bien CC - si non, rediriger vers la route appropriée
        if ($access_info['access'] !== 1) {
            if ($access_info['access'] === 3) {
                return redirect()->route('validations.validator');
            } elseif ($access_info['access'] === 2) {
                return redirect()->route('validations.admin');
            }
            // Si access = 0, peut-être que les rôles ne sont pas bien détectés
            // On essaie quand même d'afficher la page avec un message
            if ($access_info['access'] === 0) {
                // Log pour debug
                Log::warning('CC Validation: Access = 0', [
                    'username' => $current_user,
                    'selectedRoles' => session('selectedRoles')
                ]);
            }
            return redirect()->route('show.index')->with('error', 'Accès non autorisé. Type d\'utilisateur: ' . $access_info['access']);
        }

        $allowed_offices = $this->getAllowedOffices($access_info['is_network_director_or_chef'] ?? false);
        $active_keys = Subscription::pluck('key')->toArray();

        // Récupérer toutes les validations (sans pagination d'abord pour filtrer)
        $all_validations = DB::table('validation')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter info active à chaque validation
        foreach ($all_validations as $validation) {
            $validation->active = in_array($validation->key, $active_keys);
        }

        // Filtrer les validations (convertir en array pour le filtrage)
        $validations_array = json_decode(json_encode($all_validations), true);
        $filtered_validations = $this->filterValidations(
            $validations_array, 
            $allowed_offices, 
            $current_user, 
            $access_info['access'], 
            $access_info['is_super_admin'],
            $access_info['is_network_director_or_chef'] ?? false
        );

        // Créer une pagination manuelle avec les résultats filtrés
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($filtered_validations, $offset, $perPage);
        
        // Convertir les items en objets stdClass pour compatibilité avec la vue
        $items = array_map(function($item) {
            $obj = new \stdClass();
            foreach ($item as $key => $value) {
                $obj->$key = $value;
            }
            return $obj;
        }, $items);
        
        $validations = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($filtered_validations),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('validations.cc', compact('validations', 'allowed_offices', 'current_user', 'access_info'));
    }

    /**
     * Route pour Validateur - Validations en attente
     */
    public function showValidatorValidations(Request $request)
    {
        logActivity(session('username'), 'validation', 'validator_validation_page_visit');

        $current_user = session('username');
        $access_info = $this->getUserAccess();
        
        // Debug temporaire - à retirer après résolution
        if (request()->has('debug')) {
            $roles_detail = [];
            foreach (session('selectedRoles') ?? [] as $role) {
                if (is_array($role)) {
                    $roles_detail[] = [
                        'name' => $role['name'] ?? 'N/A',
                        'name_upper' => isset($role['name']) ? strtoupper(trim($role['name'])) : 'N/A',
                    ];
                } else {
                    $roles_detail[] = [
                        'name' => $role,
                        'name_upper' => strtoupper(trim($role)),
                    ];
                }
            }
            dd([
                'access_info' => $access_info,
                'selectedRoles' => session('selectedRoles'),
                'roles_detail' => $roles_detail,
                'office_name' => session('officeName'),
                'parent_name' => session('parent_name'),
            ]);
        }
        
        // Vérifier que l'utilisateur est bien validateur
        if ($access_info['access'] !== 3) {
            if ($access_info['access'] === 1) {
                return redirect()->route('validations.cc');
            } elseif ($access_info['access'] === 2) {
                return redirect()->route('validations.admin');
            }
            return redirect()->route('show.index')->with('error', 'Accès non autorisé.');
        }

        $allowed_offices = $this->getAllowedOffices($access_info['is_network_director_or_chef'] ?? false);
        $active_keys = Subscription::pluck('key')->toArray();

        // Récupérer toutes les validations (sans pagination d'abord pour filtrer)
        $all_validations = DB::table('validation')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ajouter info active à chaque validation
        foreach ($all_validations as $validation) {
            $validation->active = in_array($validation->key, $active_keys);
        }

        // Filtrer les validations (convertir en array pour le filtrage)
        $validations_array = json_decode(json_encode($all_validations), true);
        
        $filtered_validations = $this->filterValidations(
            $validations_array, 
            $allowed_offices, 
            $current_user, 
            $access_info['access'], 
            $access_info['is_super_admin'],
            $access_info['is_network_director_or_chef'] ?? false
        );
        
        // Debug temporaire - vérifier les validations filtrées
        if (request()->has('debug_validations')) {
            $sample = !empty($filtered_validations) ? $filtered_validations[0] : null;
            dd([
                'total_all' => count($all_validations),
                'total_filtered' => count($filtered_validations),
                'allowed_offices' => $allowed_offices,
                'is_network_director_or_chef' => $access_info['is_network_director_or_chef'] ?? false,
                'sample_validation' => $sample ? [
                    'ticket' => $sample['ticket'] ?? null,
                    'status' => $sample['status'] ?? null,
                    'status_type' => gettype($sample['status'] ?? null),
                    'office_name' => $sample['office_name'] ?? null,
                ] : null,
            ]);
        }

        // Créer une pagination manuelle avec les résultats filtrés
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($filtered_validations, $offset, $perPage);
        
        // Convertir les items en objets stdClass pour compatibilité avec la vue
        // Normaliser le statut pour garantir la cohérence (peut être 0, "0", etc.)
        $items = array_map(function($item) {
            $obj = new \stdClass();
            foreach ($item as $key => $value) {
                // Normaliser le statut en chaîne pour cohérence (la vue gère les deux formats)
                if ($key === 'status' && $value !== null) {
                    $obj->$key = (string)$value;
                } else {
                    $obj->$key = $value;
                }
            }
            return $obj;
        }, $items);
        
        $validations = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($filtered_validations),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('validations.validator', compact('validations', 'allowed_offices', 'current_user', 'access_info'));
    }

    /**
     * Route pour Admin - Toutes les validations
     */
    public function showAdminValidations(Request $request)
    {
        logActivity(session('username'), 'validation', 'admin_validation_page_visit');

        $current_user = session('username');
        $access_info = $this->getUserAccess();
        
        // Vérifier que l'utilisateur est bien admin
        if ($access_info['access'] !== 2) {
            return redirect()->route('sub.validation')->with('error', 'Accès non autorisé.');
        }

        $validations = DB::table('validation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $allowed_offices = $this->getAllowedOffices($access_info['is_network_director_or_chef'] ?? false);
        $active_keys = Subscription::pluck('key')->toArray();

        // Ajouter info active à chaque validation
        foreach ($validations as $validation) {
            $validation->active = in_array($validation->key, $active_keys);
        }

        // Les admins voient tout, pas besoin de filtrer

        return view('validations.admin', compact('validations', 'allowed_offices', 'current_user', 'access_info'));
    }

    /**
     * Route legacy - Redirige vers la bonne route selon le type d'utilisateur
     */
    public function subscribeValidation(Request $request)
    {
        logActivity(session('username'), 'validation', 'validation_page_visit');

        $access_info = $this->getUserAccess();
        
        // Rediriger selon le type d'utilisateur
        if ($access_info['access'] === 1) {
            return redirect()->route('validations.cc');
        } elseif ($access_info['access'] === 3) {
            return redirect()->route('validations.validator');
        } elseif ($access_info['access'] === 2) {
            return redirect()->route('validations.admin');
        }

        return redirect()->route('show.index')->with('error', 'Type d\'utilisateur non reconnu.');
    }

    /**
     * Fonction pour valider une demande
     */
    public function doValidation(Request $request)
    {
        $request->validate([
            'ticket' => 'required|string|max:15',
            'commentaire' => 'required|string|min:10',
            'validation' => 'required|string',
        ]);

        // Vérifier que l'utilisateur est bien un validateur
        $access_info = $this->getUserAccess();
        if ($access_info['access'] !== 3) {
            logActivity(session('username'), 'validation', 'validation_unauthorized_attempt');
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour valider des demandes.');
        }

        $ticket = $request->input('ticket');
        $commentaire = $request->input('commentaire');
        $status = $request->input('validation');
        $get_current_user = session('firstname') . ' ' . session('lastname');

        // Vérifier que la demande existe et est en attente
        $validation_record = DB::table('validation')
            ->where('ticket', $ticket)
            ->first();

        if (!$validation_record) {
            logActivity(session('username'), 'validation', 'validation_ticket_not_found', ['ticket' => $ticket]);
            return redirect()->back()->with('error', 'Demande introuvable.');
        }

        // Vérifier que la demande est en attente (status = 0)
        if ($validation_record->status !== "0" && $validation_record->status !== 0) {
            logActivity(session('username'), 'validation', 'validation_already_processed', ['ticket' => $ticket, 'status' => $validation_record->status]);
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        // Vérifier que l'utilisateur a le droit de valider cette demande (vérification des offices)
        $allowed_offices = $this->getAllowedOffices($access_info['is_network_director_or_chef'] ?? false);
        $validation_office = $this->cleanOfficeName($validation_record->office_name ?? '');
        
        $has_permission = false;
        if (in_array('*ALL*', $allowed_offices)) {
            $has_permission = true;
        } else {
            foreach ($allowed_offices as $allowed_office) {
                if ($this->officeNameMatches($validation_office, $allowed_office, $access_info['is_network_director_or_chef'] ? false : true)) {
                    $has_permission = true;
                    break;
                }
            }
        }

        if (!$has_permission) {
            logActivity(session('username'), 'validation', 'validation_office_not_authorized', [
                'ticket' => $ticket,
                'validation_office' => $validation_office,
                'allowed_offices' => $allowed_offices
            ]);
            return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour valider les demandes de cette agence.');
        }

        try {
            $validation_update = DB::table('validation')
                ->where('ticket', $ticket)
                ->update([
                    'status' => $status,
                    'validator' => $get_current_user,
                    'motif_validation' => $commentaire
                ]);

            if ($validation_update > 0) {
                logActivity(session('username'), 'validation', 'validation_successfully_done');
                return redirect()->back()->with('success', 'Demande enregistrée.');
            } else {
                logActivity(session('username'), 'validation', 'validation_error');
                return redirect()->back()->with('error', 'Erreur de validation.');
            }
        } catch (\Exception $e) {
            Log::error('VALIDATION: Erreur lors de la validation d\'une demande', [
                'username' => session('username'),
                'ticket' => $request->input('ticket'),
                'action' => $request->input('action'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            logActivity(session('username'), 'validation', 'validation_error_save_data');
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
