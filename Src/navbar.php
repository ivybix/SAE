<?php
session_start();

$is_logged_in = isset($_SESSION['user']);

$is_formulaire_page = strpos($_SERVER['REQUEST_URI'], 'formulaire.php') !== false;
$is_adminweb_page =($_SESSION['role']) == 'adminweb';
$is_adminsystem = ($_SESSION['role']) == 'sysadmin';
?>
<nav class="uk-navbar-container uk-navbar-transparent uk-align-center">
    <div class="uk-container">
        <div class="uk-navbar">

            <div class="uk-navbar-center">

                <ul class="uk-navbar-nav menu">
                    <li>
                        <a class="uk-navbar-item uk-logo" href="index.php" target="_self" aria-label="Acceuil">
                            <img src="/Ressources/logo-nav.ico" alt="Logo InkWare (Acceuil)" style="height: 40px;">
                        </a>
                    </li>


                    <li class="menu-item">
                        <a href="index.php">
                            Accueil
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="inventaire.php">
                            Inventaire
                        </a>
                    </li>
                    <?php if ($is_adminweb_page): ?>
                        <li class="menu-item uk-active"><a href="adminweb.php">Gestion Web</a></li>

                    <?php endif;?>
                    <?php if ($is_adminsystem): ?>

                        <li class="menu-item uk-active"><a href="adminsystem.php">Logs Système</a></li>
                    <?php endif; ?>
                    <?php if( (!$is_logged_in)  && (!$is_formulaire_page)): ?>
                        <li class="menu-item">
                            <a href="formulaire.php" target="_self">
                                Connexion
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if  (($is_logged_in)&& (!$is_formulaire_page)) : ?>
                        <li class="menu-item">
                            <a href="logout.php" target="_self">
                                Déconnexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="uk-navbar-center-right">





                </div>
            </div>

        </div>

    </div>
</nav>