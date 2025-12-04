<?php
session_start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="CSS/Style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" type="image/x-icon" href="Ressources/logo.ico"/>
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>

    <title>InkWare</title>
</head>
<body>

<?php include_once "navbar.php"; ?>
<div class="uk-align-center">
    <article class="uk-article uk-text-center">
        <h1 class="sansation-bold">Comment notre platforme fonctionne ?</h1>

        <p class="uk-text-lead sansation-light">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis est
            illum incidunt, ipsum iste natus nobis odit officiis praesentium provident reprehenderit, saepe, sunt! Atque
            cupiditate, doloribus ducimus qui sequi vel.</p>
    </article>
</div>
<div class="uk-container uk-align-center">
    <article class="uk-article uk-text-center">
        <h1 class="sansation-bold">Vidéo explicative:</h1>
    </article>
</div>
<div class="uk-container uk-align-center">
    <div class="uk-text-center">
        <video src="Ressources/video_de_test.mp4"
               width="480" height="360" controls preload="auto" class="uk-video"></video>

    </div>
</div>
</body>
<footer>

</footer>
</html>
