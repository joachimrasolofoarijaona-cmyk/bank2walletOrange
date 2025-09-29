@extends('layouts.sidebar')

@section('title', ':: Contrat ::')

@section('content')

<div class="container-fluid">
    {{-- Style section --}}
    <style>
        @page {
            size: A4;
            margin: 0.25cm;
        }

        @media print {

            nav,
            footer,
            .no-print {
                display: none;
                /* Cache tout ce que tu ne veux pas imprimer */
            }

            body * {
                font-family: 'Poppins', sans-serif;
                visibility: hidden;
                /* Cache tout */
            }

            #contract,
            #contract * {
                visibility: visible;
                /* Mais montre uniquement #contract */
            }

            #contract {
                position: absolute;
                right: auto;
                left: 0;
                top: 10px;
                width: 100%;
            }

            /* Ton autre style reste correct */
            .header-contract,
            .body-contract,
            .footer-contract {
                width: 100%;
                max-width: 100%;
                font-size: 10 pt;
            }

            h6,
            p {
                page-break-inside: avoid;
            }

            .page-break {
                page-break-before: always;
            }


        }

        .acep-logo img {
            display: block;
            margin-top: 15px;
            margin-right: auto;
            width: 250px;
        }

        .orange-logo img {
            display: block;
            margin-left: auto;
            width: 250px;
        }

        .header-contract {
            padding-top: -25px;
        }

        .information-clients h6 {
            border: 1px solid;
        }

        .info {
            border: 1px solid;
            padding-left: 15px;
        }

        .header-contract li {
            list-style: decimal;
        }

        #contract {
            font-size: 12px;
        }
    </style>
    <div class="row">
        {{-- filter by number and the conctract type / Subscription or Unsubscription --}}
        <div class="col-lg-4 col-md-6 col-xs-12 fixed">
            <div class="card text-white bg-dark">
                <div class="card-header d-flex align-items-start ">
                    <i class="ri-filter-line fs-5 me-2"></i>
                    <h4 class="card-title text-uppercase">Filtre</h4>
                </div>
                <div class="card-body text-white">
                    <form action="{{ route('generate.contract') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="msisdn" class="form-label">Numéro de ligne</label>
                            <input type="text" class="form-control bg-secondary text-white" id="msisdn" name="msisdn" placeholder="Numéro Orange ..." required>
                        </div>
                        <div class="mb-3">
                            {{-- dropdown for contract type --}}
                            <label for="contract_type" class="form-label">Type de contrat</label>
                            <select class="form-select bg-secondary text-white" id="contract_type" name="contract_type" required>
                                <option value="" disabled selected>Choisissez le type de contrat</option>
                                <option value="1">Souscription</option>
                                <option value="0">Résiliation</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-success">Filtrer</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- contract details --}}
        <div class="col-lg-8 col-md-12 col-xs-12 text-dark vh-auto">
            <div class="container">
                <script>
                    function imprimerContrat() {
                        var content = document.getElementById('contract').innerHTML;
                        window.print();
                    }
                </script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
                <script>
                    function downloadContract() {
                        const element = document.getElementById('contract');

                        const options = {
                            margin: 0.5,
                            filename: 'contrat.pdf',
                            image: {
                                type: 'jpeg',
                                quality: 0.98
                            },
                            html2canvas: {
                                scale: 2
                            },
                            jsPDF: {
                                unit: 'in',
                                format: 'a4',
                                orientation: 'portrait'
                            }
                        };

                        html2pdf().set(options).from(element).save();
                    }
                </script>
            </div>

            <div class="navbar navbar-dark bg-dark px-4">
                <div class="d-flex align-items-start ">
                    <i class="ri-file-add-line fs-5 me-2"></i>
                    <h4 class="card-title text-uppercase">Contrat</h4>
                </div>
            </div>

            {{-- IF SUBSCRIPTION REQUEST --}}
            {{-- show the contract details if the msisdn and contract type is set and the contract type is subscription --}}
            @if(isset($msisdn) && $contract_type == 1)
            {{-- Error message if no data found --}}
            @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- Success message if data found --}}
            <div class="alert alert-success mt-3">
                <strong>Contrat de souscription généré avec succès pour le numéro : {{ $msisdn }}</strong>
            </div>
            <div class="border rounded p-0 overflow-auto vh-100">
                <div class="card" id="contract">
                    <div class="card-body" style="font-size: 12px;">
                        <div class="contract">
                            <div class="header-contract text-center">
                                <div class="customer">
                                    <div class="row">
                                        <div class="col">
                                            <div class="acep-logo">
                                                <img src="{{asset('images/acep_madagascar_logo.png')}}" alt="acep_logo">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="orange-logo">
                                                <img src="{{asset('images/orange_money_logo.png')}}" alt="orange_logo">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Logo section -->
                                    <!-- Information client -->
                                    <p class="text-center"><strong>Formulaire de souscription</strong></p>
                                    <p class="text-center"><strong>ACEP/</strong><strong style="color: #FF6600;">Orange Money</strong></p><br>
                                    <p class="text-center">Liaison portefeuille électronique Orange Money et Compte ACEP MADAGASCAR</p>
                                    <div class="customer-info">
                                        <div class="information-clients">
                                            <h6 class="text-center"><strong>INFORMATIONS CLIENTS</strong></h6>
                                        </div>
                                        <div class="info text-start">
                                            <p>Code civilité (Mr/Mme)</p>
                                            <p>Je soussigné(e)</p>
                                            <table class="table table-borderless" style="line-height: 0.5em;">
                                                <tbody>
                                                    <tr>
                                                        <td width="195px;">Nom : <strong><u>{{$subscription_customer->client_lastname}}</u></strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="195px;">Prénoms : <strong><u>{{$subscription_customer->client_firstname}}</u></strong> </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="195px;">Date de naissance : <strong><u><strong><u>{{\Carbon\Carbon::parse($subscription_customer->client_dob)->translatedFormat('d F Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="195px;">N° de pièce d’identité : <strong><u>{{number_format($subscription_customer->client_cin, 0, '', ' ')}}</u></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="195px;">Type de pièce d’identité : <strong><u>Carte d'identité nationale / CIN</u></strong></td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div><br>
                                        <div class="information-clients">
                                            <h6 class="text-center"><strong>LIAISON DES COMPTES</strong></h6>
                                        </div>

                                        <div class="info text-start" style="line-height: 2em;">
                                            <p>Souhaite lier mon compte Orange Money, Numéro :
                                                <strong>{{ $subscription_customer->msisdn }}</strong>
                                            </p>
                                            <p>A mon (mes) comptes :</p>

                                            <ul class="list-unstyled ps-5">
                                                @foreach($subscription_data as $data)
                                                @if($data->account_status === "1")
                                                <li>
                                                    N° : <strong>{{ $data->msisdn }}</strong> &nbsp;&nbsp;&nbsp;
                                                    Clé d’activation : <strong>{{ $data->key }}</strong> &nbsp;&nbsp;&nbsp;
                                                    Libelle : <strong>{{ $data->libelle }}</strong>
                                                </li>
                                                @endif
                                                @endforeach
                                            </ul>

                                            <p>Agence ACEP MADAGASCAR</p>
                                        </div><br>

                                        <div class="other-informations text-start">
                                            <p>Je reconnais avoir pris connaissance des conditions générales d’utilisation du service ACEP/Orange Money dont un exemplaire m’est remis ce jour.</p>
                                            <p>Je déclare y adhérer sans réserve et assumer la responsabilité pleine et entière de l’utilisation du téléphone mobile mentionné supra pour effectuer des transactions au débit ou au crédit de mon (mes) compte(s), dans le cadre du service ACEP/Orange Money.</p>
                                            <p>Le service ACEP/Orange Money sera fonctionnel sous réserve de l’acceptation de la demande par Orange Money.</p><br>

                                            <p class="text-end pe-5">A <strong> {{$data->officeName}} </strong>, le {{\Carbon\Carbon::parse($subscription_customer->date_sub)->translatedFormat('d F Y') }}</p><br>
                                            <p class="text-center">
                                                Signature du titulaire ou du mandataire habilité
                                                Précédée de la mention « Lu et approuvé »
                                            </p>

                                        </div>
                                    </div>
                                    <!-- Information client -->
                                </div>
                                <div class="mb-5" style="page-break-before: always;"></div>
                                <!-- Logo section -->
                                <p class="text-right underlined"><u>Annexe 2</u></p>
                                <h6 class="m-0 text-center">
                                    <strong>
                                        CONDITIONS GÉNÉRALES <br>
                                        Accès aux comptes ACEP MADAGASCAR via le <br>
                                        portefeuille électronique ORANGE MONEY
                                    </strong>
                                </h6><br><br>
                            </div>
                            <div class="body-contract justify-content-center">
                                <p>
                                    Les présentes conditions régissent exclusivement le service de Liaison entre le portefeuille électronique ORANGE MONEY du client et ses comptes bancaires ouverts dans les livres ACEP MADAGASCAR <strong>(« ACEP MADAGASCAR »)</strong>
                                </p><br>
                                <p>Les relations entre ACEP MADAGASCAR et ses clients sont encadrées par les présentes conditions générales, ainsi que par celles disponibles dans les agences ACEP MADAGASCAR. </p><br>
                                <p>De leur côté, les relations entre ORANGE MONEY et ses clients sont régies par les conditions générales d’ORANGE MONEY.</p><br><br>


                                <h6><strong>Article 1 – Engagement</strong><br></h6>
                                <p>
                                    ACEP MADAGASCAR autorise son client à souscrire à la liaison entre son portefeuille électronique ORANGE MONEY et ses comptes bancaires détenus chez ACEP MADAGASCAR, conformément aux conditions établies dans le présent document ainsi qu’à celles applicables à ses comptes.
                                </p>
                                <h6><strong>Article 2 – Objet du service</strong></h6>
                                <p>Le service de liaison permet au client ACEP Madagascar d’effectuer :</p>
                                <ul>
                                    <li>Des virements entre son portefeuille électronique ORANGE MONEY et son ou ses comptes bancaires ouverts dans les livres ACEP MADAGASCAR ;</li>
                                    <li>Des demandes de solde ;</li>
                                    <li>Des demandes de mini-relevés ;</li>
                                </ul>
                                <p>La liaison est mise en place conjointement par ACEP Madagascar et ORANGE MONEY. Seul le titulaire du compte chez ACEP MADAGASCAR peut en faire la demande, toute souscription à ce service par un mandataire autorisé à gérer les comptes bancaires du client étant exclue.</p>
                                <br>

                                <!-- Modificatoion -->
                                <h6><strong>Article 3 – Demande de souscription au service</strong></h6>
                                <p>
                                    3.1 Pour souscrire à ce service, le client doit préalablement être titulaire d’un ou plusieurs comptes bancaires chez ACEP MADAGASCAR, disposer d’une ligne ORANGE active avec la carte SIM correspondante, ainsi que dʼun compte ORANGE MONEY activé
                                </p>
                                <p>La présentation d’identité est requise pour l’activation de la Liaison.</p>

                                <p>
                                    3.2 Toute demande de Liaison doit être accompagnée de la signature conjointe du client et dʼun agent ACEP MADAGASCAR sur le formulaire de souscription dûment complété par le client. La signature de ce formulaire vaut acceptation sans réserves des présentes conditions générales ainsi que de leurs éventuelles modifications ultérieures décidées soit par ACEP MADAGASCAR, soit en fonction des évolutions législatives ou réglementaires.
                                </p>
                                <p>
                                    3.3 a souscription au service ACEP/ORANGE MONEY peut être effectuée dans toute agence ACEP MADAGASCAR disposant du service. e client recevra un SMS lui indiquant que la Liaison est active.
                                </p>

                                <h6><strong>Article 4 – Contrôle</strong></h6>
                                <p>Le service de Liaison ne sera fonctionnel qu'après vérification et comparaison des listes noires des deux parties. </p>
                                <p>En cas de confirmation de fraude ou de suspicion de blanchiment de capitaux, ACEP MADAGASCAR notifie ORANGE MONEY, qui procède immédiatement à la résiliation du service de Liaison.</p>

                                <div class="mb-5" style="page-break-before: always;"></div>

                                <h6><strong>Article 5 – Tarification</strong></h6>
                                <p>5.1 Le service de Liaison est exempt de tout frais de gestion ou d’abonnement.</p>
                                <p>5.2 Aucune charge de gestion n’est appliquée pour la résiliation du service de Liaison.</p>
                                <p>5.3 Les transferts opérés du portefeuille électronique Orange Money vers le compte bancaire Acep Madagascar donnent lieu à des frais fixés
                                    selon la grille tarifaire affichée en agence ACEP MADAGASCAR et ORANGE. Ces frais sont automatiquement prélevés sur le compte ORANGE MONEY du client lors du virement, sans nécessité dʼun avis préalable, ce que le client reconnaît et accepte.
                                </p>
                                <p>5.4 De même, Lles frais liés à la consultation de solde, au mini-relevé et au bank to wallet sont définis selon la grille tarifaire affichée en agence ACEP MADAGASCAR et ORANGE. Ils sont automatiquement déduits du compte ACEP du client.</p>

                                <h6><strong>Article 6 – Code confidentiel (code secret) relevant du portefeuille électronique ORANGE MONEY</strong></h6>
                                <p>6.1 Un code personnel et confidentiel est choisi par chaque nouveau client lorsqu’il se connecte pour la première fois au portefeuille électronique Orange Money (code secret par défaut : 0000). Le code confidentiel doit obligatoirement contenir 4 caractères et sera effectif instantanément.</p>
                                <p>6.2 Ce code est essentiel pour utiliser le service. Il sera demandé pour confirmer chaque opération</p>
                                <p>6.3 Le client peut modifier son code secret à tout moment en accédant au menu « Mon compte » d’Orange MONEY et en suivant les instructions indiquées.</p>
                                <p>6.4 Lors d'une opération, le client dispose de trois essais pour saisir son code secret avant que le portefeuille électronique ORANGE MONEY ne soit bloqué</p>
                                <p>6.5 Pour tout problème lié au code confidentiel (réinitialisation, blocage, déblocage, etc.), le client doit contacter le service client ORANGE MONEY au 204, disponible de 7h à 21h, 7 jours sur 7.</p>
                                <p>Coût de l' appel suivant les tarifs en vigueur.</p>

                                <h6><strong>Article 7 – Cas de perte ou de vol de la carte SIM</strong></h6>
                                <p>7.1 Conformément aux conditions générales dʼORANGE MONEY, en cas de perte ou de vol de sa carte SIM, le client sʼengage à en informe sans délai ORANGE, soit par courrier avec accusé de réception, soit en contactant le service client au 204.</p>
                                <p>7.2 Pour récupérer lʼaccès à son portefeuille électronique ORANGE MONEY, le client devra se rendre en agence ou dans un shop ORANGE, muni dʼun justificatif dʼidentité. Il devra s’acquitter des frais d’achat d’une nouvelle carte SIM auprès de l’agence ou du shop Orange</p>

                                <div class="mb-5" style="page-break-before: always;"></div>

                                <h6><strong>Article 8 – Responsabilité du client</strong></h6>
                                <p>8.1 Le client est responsable de lʼutilisation correcte, ainsi que de la conservation de sa carte SIM et de son portefeuille électronique ORANGE MONEY. Il doit prendre toutes les mesures nécessaires pour protéger et préserver les dispositifs de sécurité associés à son compte, en particulier son code secret et s’interdit de le communiquer à un tiers.
                                    ACEP MADAGASCAR et ORANGE ne pourront être tenus responsables en cas dʼutilisation frauduleuse du code confidentiel par un tiers, quelles qu’en soient les conséquences qui en résultent..
                                    Sauf preuve contraire apportée par le client, toute connexion et/ou opération effectuée par son code confidentiel sera réputée avoir été effectuée par lui.
                                </p>
                                <p>8.2 En cas de perte ou de vol de sa carte SIM, le client est tenu d’en informer obligatoirement ORANGE et de déclarer l’incident en agence d’ACEP Madagascar. </p>
                                <p>8.3 La responsabilité du client reste engagée pour toutes les opérations antérieures à la notification auprès d’Orange Money et à la réalisation du service en agence, conformément à l’article 17. </p>
                                <p>8.4 Le client s’engage à ce que les informations personnelles et de sécurité qu’il renseigne soient complètes, réelles, exactes et à jour, et qu’elles n’aient ni pour objet, ni pour effet, de porter à confusion avec un tiers, d’induire en erreur sur son identité, ou de porter atteinte à l’ACEP Madagascar ou Orange Money Madagascar, ou à des droits des tiers. Il s’engage à les mettre systématiquement à jour auprès de l’ACEP Madagascar et de Orange Money Madagascar.</p>
                                <p>8.5 L’ACEP Madagascar reste étrangère à tout différend pouvant survenir entre Orange Money Madagascar et le client</p>

                                <h6><strong>Article 9 – Paiement électronique</strong></h6>
                                <p>9.1 L'ordre de paiement effectué via le portefeuille électronique ORANGE MONEY est irrévocable. En cas d'erreur de manipulation, le client peut néanmoins effectuer un virement inverse pour corriger l'opération, sous réserve des frais en vigueur et pour autant que la situation du compte bancaire le permet..</p>
                                <p>9.2 Les transferts peuvent être refusés pour plusieurs raisons (liste non exhaustive):
                                <ul>
                                    <li>Si le montant du transfert dépasse les plafonds autorisés définis à l'article 11 ;</li>
                                    <li>Si le plafond de disponibilité journalier, tel que spécifié à l'article 11, est dépassé ;</li>
                                    <li>Si la connexion au réseau est perdue pendant la transaction ;</li>
                                    <li>Si l'utilisateur commet une erreur en saisissant son code confidentiel après la saisie de trois codes confidentiels erronés.</li>
                                </ul>
                                </p>
                                <p>9.3 Les transferts peuvent être temporairement suspendus en raison de la maintenance des serveurs. La durée de cette interruption ne peut être prévue à l'avance. Dans ce cas, le client pourra réessayer de réaliser la transaction qui n'a pas été finalisée dès que le serveur sera de nouveau opérationnel.</p>

                                <h6><strong>Article 10 – Retrait d’espèces du portefeuille électronique ORANGE MONEY</strong></h6>
                                <p>Les clients peuvent à tout moment retirer les sommes disponibles sur leur portefeuille électronique ORANGE MONEY via le réseau de distribution dʼORANGE. ACEP MADAGASCAR ne peut être tenu responsable des difficultés rencontrées par le réseau de distribution dʼORANGE.</p>

                                <h6><strong>Article 11 – Plafonds du portefeuille électronique Orange Money</strong></h6>
                                <p>
                                    11.1 Le service Bank to Wallet est soumis à des limites approuvées par les autorités de régulation, lesquelles sont détaillées dans des conditions particulières annexées et faisant partie intégrante du présent contrat de souscription.
                                </p>
                                <p>
                                    11.2 Le client peut contacter à tout moment le service client ORANGE MONEY pour obtenir des informations concernant le service et le portefeuille électronique ORANGE MONEY.
                                </p>
                                <table class="table table-stripped text-center table-responsive">
                                    <thead class="bg-secondary">
                                        <tr>
                                            <th scope="col" rowspan="2">Type de compte</th>
                                            <th scope="col">Plafond Unitaire</th>
                                            <th scope="col">Plafond Journalier</th>
                                            <th scope="col">Plafond Hebdomadaire</th>
                                            <th scope="col">Plafond Mensuel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Grand Public</td>
                                            <td>5 000 000,00 ar</td>
                                            <td>10 000 000,00 ar</td>
                                            <td>40 000 000,00 ar</td>
                                            <td>120 000 000,00 ar</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p>

                                <div class="mb-5" style="page-break-before: always;"></div>

                                <h6><strong>Article 12 – Délai de conservation des documents et délai de réclamation</strong></h6>
                                <p>12.1 En cas de réclamation, ORANGE et ACEP MADAGASCAR s’engagent à fournir les meilleures informations possibles sur les conditions d’exécution de l’opération contestée. </p>
                                <p>Le client pourra contacter le 204 ou le 034 16 548 58 ou se rendre en agence ORANGE ou ACEP, selon les indications du SMS reçu</p>
                                <p>12.2 Tous les documents ou reproductions liés aux opérations mentionnées dans les présentes conditions générales et détenus par ACEP MADAGASCAR et ORANGE MONEY seront conservés pendant dix (10) ans aux fins de preuve, de conformité et de contrôle fiscal.</p>
                                <p>12.3 Toute réclamation du client doit être formulée dans un délai de 30 jours calendaire suivant la date de l’opération contestée. Passé ce délai, elle ne sera plus recevable.</p>


                                <h6><strong>Article 13 – Données personnelles</strong></h6>
                                <p>Conformément à la Loi n°2014-038 sur la protection des données à caractère personnel, ACEP MADAGASCAR utilisera les informations personnelles du client pour gérer sa relation avec celui-ci, se conformer à ses obligations légales en matière de transmission de données aux autorités compétentes et promouvoir ses services à travers des actions de marketing.</p>
                                <p>En acceptant les présentes conditions générales, le client consent au traitement de ses données personnelles tel que décrit ci-dessus.</p>

                                <p>Le client a le droit de recevoir des informations de la part dʼACEP MADAGASCAR et dʼORANGE concernant le traitement de ses données personnelles.
                                    A la demande du client ou de leur propre initiative, ACEP MADAGASCAR et ORANGE sʼengagent à corriger toute donnée incorrecte
                                </p>

                                <p>Par ailleurs, le client dispose dʼun droit d’accès, de rectification, de modification et de suppression des données le concernant.</p>

                                <h6><strong>Article 14 – Cas de force majeure</strong></h6>
                                <p>En cas de force majeure, telle que définie par la loi et la jurisprudence, ni ACEP MADAGASCAR ni ORANGE ne pourra être tenue responsable dʼun retard ou dʼune défaillance dans l’exécution de ses services.</p>
                                <p>Par force majeure, on entend tout événement imprévisible, irrésistible et indépendant de la volonté des parties, rendant l’exécution du présent contrat impossible. Cela inclut, sans sʼy limiter, une décision gouvernementale, une guerre, des émeutes, un sabotage, un incendie, une inondation, un cyclone, une épidémie, une quarantaine, une grève ou un lock-out, etc..</p>

                                <p>L’apparition d’un cas de force majeure aura effet de suspendre l’exécution de l’obligation devenue impossible, ainsi que les obligations corrélatives de l’autre partie, sans qu’aucune indemnité ne soit due.</p>
                                <p>Si la situation persiste au-delà dʼun (1) mois, les parties engageront des discussions afin de déterminer les mesures à adopter. En lʼabsence dʼun accord, les présentes conditions générales seront résiliées de plein droit.</p>

                                <h6><strong>Article 15 – Preuves des opérations effectuées</strong></h6>
                                <p>Le client reconnaît la validité des documents électroniques émis par ACEP MADAGASCAR et ORANGE dans le cadre des échanges avec ces derniers.</p>
                                <p>Toutes les opérations effectuées conformément aux présentes conditions générales seront attestées par les supports informatiques échangés quotidiennement entre ACEP MADAGASCAR et ORANGE. Les enregistrements des transactions et leur reproduction sur support informatique constituent une preuve valable et recevable de l’exécution de l’opération et de son enregistrement comptable. Le client accepte ces éléments comme justificatifs sans aucune réserve.</p>

                                <div class="mb-5" style="page-break-before: always;"></div>

                                <h6><strong>Article 16 – Durée et réalisation du service</strong></h6>
                                <p>16.1 La souscription au service de Liaison est établie pour une durée indéterminée</p>
                                <p>16.2 Le service de Liaison peut être résilié à tout moment, sans motif ni indemnité, par ORANGE, ACEP MADAGASCAR ou le client :
                                <ul>
                                    <li>Pour mettre fin au service, le client doit se rendre en agence ACEP MADAGASCAR et remplir un formulaire de résiliation ;</li>
                                    <li>Si la résiliation est initiée par ORANGE ou ACEP MADAGASCAR, elle sera notifiée au client par un message sécurisé sur son téléphone ;</li>
                                    <li>Dans tous les deux cas, la désinscription prend effet immédiatement.</li>
                                </ul>
                                </p>
                                <p>16.3 À compter de la date effective de résiliation, le client perd tout accès au service de Liaison. ACEP MADAGASCAR et ORANGE se réservent le droit de prendre toutes les mesures nécessaires pour en garantir l’arrêt.</p>

                                <h6><strong>Article 17 – Intégralité</strong></h6>
                                <p>17.1 L’invalidité ou la nullité de l’une des clauses des présentes conditions générales, pour quelque motif que ce soit, ne saurait en aucun cas affecter la validité et le respect des autres clauses des présentes conditions générales</p>
                                <p>17.2 Le fait qu’une partie n’exige pas l’application d’une clause des présentes conditions générales, de manière temporaire ou permanente, ne saurait être interprété comme une renonciation à ses droits découlant de cette clause.</p>

                                <p><strong>Modifications</strong></p>

                                <p>L’ACEP Madagascar ou Orange Money pourra apporter des modifications, notamment tarifaires, aux conditions générales du service de Liaison, lesquelles seront portées à la connaissance du client, par tout moyen nécessaire et adéquat, dans un délai d’un (1) mois avant la date de leur entrée en vigueur. L’absence de contestation par le client dans un délai de un mois (1mois) à compter de cette notification, vaut acceptation de ces modifications.</p>
                                <p>Dans le cas où le client n’accepterait pas ces modifications, il devra initier la résiliation du service de liaison dans les conditions prévues à l’article 16.2 ci-dessus.</p>

                                <h6><strong>Article 18 – Lutte contre le blanchiment de capitaux et le financement du terrorisme</strong></h6>
                                <p>Le client s’engage à n’utiliser le service de Liaison que dans le cadre autorisé par la loi et la règlementation en vigueur, notamment par les dispositions légales et réglementaires en matière de lutte contre le blanchiment de capitaux et le financement du terrorisme</p>
                                <p>Le client est informé qu’en raison des contraintes liées à l’application des dispositions légales et réglementaires en vigueur, l’ACEP MadagascarI ou Orange Money Madagascar pourront lui demander de justifier l’origine des fonds, en fonction du montant et/ou de l’opération effectuée.</p>


                                <h6><strong>Article 19 – Litiges et contestations</strong></h6>
                                <p>Tout litige né de l’interprétation et/ou àde l’exécution des présentes conditions générales fera au préalable l’objet d’un èglement à l’amiable, dans un délai maximal de trente (30) jours à compter de la réception, par ACEP MADAGASCAR ou par le client, de tout courrier l’informant dudit litige, le cachet de la poste ou l’accusé de réception ou l’horodatage faisant foi.</p>
                                <p>À défaut d’accord dans ce délai, le Tribunal de Première Instance d’Antananarivo sera seul compétent pour connaitre des litiges éventuels. La législation applicable est celle de Madagascar.</p>

                                <div class="mb-5" style="page-break-before: always;"></div>

                                <h6><strong>Article 20 – Langue utilisée</strong></h6>
                                <p>La langue de référence, tant pour la phase précontractuelle que pour l’exécution du contrat, est le français.</p>
                                <p>La souscription du contrat s’effectue exclusivement en français. Toute traduction faite dans une autre langue est fournie uniquement à titre informatif</p>
                                <br><br><br>
                            </div>
                            <div class="footer-contract">
                                <p class="text-end">
                                <p class="text-end pe-5">A <strong> {{$data->officeName}} </strong>, le {{\Carbon\Carbon::parse($subscription_customer->date_sub)->translatedFormat('d F Y') }}</p><br>
                                </p><br><br><br><br>
                                <p class="text-center">
                                    Signature du titulaire ou du mandataire habilité
                                    Précédée de la mention « Lu et approuvé »
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IF UNSUBSCRIPTION REQUEST --}}
            {{-- show the contract details if the msisdn and contract type is set and the contract type is unsubscription --}}
            @elseif(isset($msisdn) && $contract_type == 0)
            {{-- Error message if no data found --}}
            @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- Success message if data found --}}
            <div class="alert alert-success mt-3">
                <strong>Contrat de résiliation généré avec succès pour le numéro : {{ $msisdn }}</strong>
            </div>
            <div class="border rounded p-0 overflow-auto vh-100">
                <div class="card" id="contract">
                    <div class="card-body" style="font-size: 12px;">
                        <div class="contract">
                            <div class="container-fluid">
                                <div class="header-contract text-center">
                                    <div class="customer-details">
                                        <div class="row">
                                            <div class="col">
                                                <div class="acep-logo">
                                                    <img src="{{asset('images/acep_madagascar_logo.png')}}" alt="acep_logo">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="orange-logo">
                                                    <img src="{{asset('images/orange_money_logo.png')}}" alt="orange_logo">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="unsub-formulaire text-center">
                                            <p class="text-center"><strong>FORMULAIRE DE RÉSILIATION</strong></p>
                                            <p><strong>Bank to wallet / wallet to bank Orange Money_ACEP</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-contract justify-content-center">
                                    <p><strong>Informations du client :</strong></p>
                                    <div class="card p-3 mb-3">
                                        <!-- Infos client (affichées une seule fois) -->
                                        <p><strong>Nom :</strong> {{$unsubscription_customer->client_firstname}}</p>
                                        <p><strong>Prénom :</strong> {{$unsubscription_customer->client_lastname}}</p>
                                        <p><strong>Numéro Orange :</strong> {{$unsubscription_customer->msisdn}}</p>
                                        <p><strong>CIN :</strong> {{$unsubscription_customer->client_cin}}</p>
                                        <strong>Numéro de compte ACEP : </strong>
                                        <!-- Liste numérotée des comptes -->
                                        <ol class="list-group list-group-numbered border-0">
                                            @foreach($unsubscription_data as $data)
                                            @if(\Carbon\Carbon::parse($data->date_unsub)->format('d-m-Y') === now()->format('d-m-Y'))
                                            <li class="list-group-item">
                                                <strong>{{$data->account_no}}</strong> &nbsp;&nbsp;&nbsp;
                                            </li>
                                            @endif

                                            @endforeach
                                        </ol>
                                    </div>

                                    <div class="motif">
                                        <p><strong>Motif de la résiliation (cocher la case correspondante) :</strong></p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">Changement de numéro de téléphone</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">Insatisfaction du service</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">Frais jugés trop élevés</label>
                                        </div>
                                        <div class="form-check ">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">Utilisation d'un autre service de transfert d'argent</label>
                                        </div>
                                        <div class="form-check d-flex align-content-start">
                                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                            <label for="msisdn" class="form-label ps-2">Autre (précisez) : </label>
                                            <input type="text" class="border-0 mb-3 pb-2 ps-2 w-50" id="msisdn" name="msisdn" placeholder=".............................................................................................................................................." required>

                                        </div>
                                    </div>
                                    <div class="mb-5" style="page-break-before: always;"></div>
                                    <div class="declaration p-0 ">
                                        <p><strong>Déclaration du Client :</strong></p>
                                        <div class="soussigne">
                                            Je soussigné(e)<input type="text" class="border-0 ps-2 w-50" id="msisdn" name="msisdn" placeholder=".............................................................................................................................................." required>
                                            , titulaire du compte ACEP susmentionné, déclare souhaiter la résiliation de mon accès au service
                                            Bank to Wallet / Wallet to Bank lié à mon compte ACEP et mon numéro Orange Money.
                                            J'atteste avoir pris connaissance que cette résiliation est définitive et qu'une nouvelle souscription nécessitera une nouvelle
                                            demande d'adhésion.
                                        </div>
                                    </div>
                                </div>
                            </div> <br><br><br>
                            <div class="footer-contract px-4">
                                <div class="done-at">
                                    <p class="text-end pe-5">A <strong> {{$data->office_name}} </strong>, le {{\Carbon\Carbon::parse($unsubscription_customer->date_unsub)->translatedFormat('d F Y') }}</p><br>
                                </div>
                                <div class="customer-sign">
                                    <p>Signature du client :</p>
                                </div><br><br><br>
                                <div class="reserved-part">
                                    <p><strong>Partie Réservée à l'Agence ACEP :</strong></p>
                                </div>
                                <div class="agent-sign" style="line-height: 0.5em;">
                                    <p>Date de réception de la demande : <strong>{{\Carbon\Carbon::parse($unsubscription_customer->date_unsub)->translatedFormat('d F Y') }}</strong> </p>
                                    <p>Nom et signature de l'agent ACEP : ……………………………………………………………</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            {{-- alerte for none to show --}}
            <div class="border rounded px-0 pt-3 py-0 overflow-auto vh-50">
                <div class="alert alert-warning text-center">
                    <strong class="text-uppercase">Aucun contrat à afficher.</strong>
                    <p>Veuillez vérifier les informations saisies ou contacter le support si le problème persiste.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection