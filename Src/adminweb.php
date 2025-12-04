<?php
session_start();
if (($_SESSION['role']) != 'adminweb'): {
    header('Location: index.php');
};
endif;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="Ressources/logo.ico"/>
    <title>Administration Web - Gestion</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css" />
    <link rel="stylesheet" href="CSS/uikit-rtl.css" />
</head>
<body>

<?php include_once 'navbar.php'; ?>
<div class="uk-container uk-margin-medium-top">
    <h1 class="uk-text-center sansation-bold">Espace Administrateur Web</h1>

    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom">
        <h2 class="uk-card-title sansation-regular">Gestion des Techniciens</h2>
        <div class="uk-overflow-auto">
            <table class="uk-table uk-table-middle uk-table-divider">
                <thead>
                    <tr>
                        <th>Identifiant</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>tech1</td>
                        <td>01/09/2025</td>
                        <td>
                            <button class="uk-button uk-button-danger uk-button-small">Supprimer</button>
                            <button class="uk-button uk-button-secondary uk-button-small">Reset MDP</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <h4 class="sansation-regular">Ajouter un technicien</h4>
        <form class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@s">
                <input class="uk-input" type="text" placeholder="Identifiant (ex: tech2)">
            </div>
            <div class="uk-width-1-3@s">
                <input class="uk-input" type="password" placeholder="Mot de passe">
            </div>
            <div class="uk-width-1-3@s">
                <button class="uk-button uk-button-primary">Créer le compte</button>
            </div>
        </form>
    </div>

    <div class="uk-grid-match uk-child-width-1-2@m" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Systèmes d'Exploitation (OS)</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_os.php">
                    <label>
                        <input class="uk-input uk-width-expand" type="text" name="os" id="os" placeholder="Nouvel OS">
                    </label>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Constructeurs</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_manufacturer.php">
                    <label>
                        <input class="uk-input uk-width-expand" type="text" name="manufacturer" id="manufacturer" placeholder="Nouveau Constructeur">
                    </label>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="uk-margin-medium-top uk-card uk-card-secondary uk-card-body">
        <h3 class="uk-card-title">Zone de Rebut</h3>
        <p>Verrouiller la liste du rebut empêche tout nouvel ajout ou modification par les techniciens pour préparer l'exportation définitive.</p>
        <button class="uk-button uk-button-danger">Verrouiller la liste du rebut (Audit)</button>
        <button class="uk-button uk-button-default">Exporter CSV Rebut</button>
    </div>
</div>

</body>
</html>
