<?php
$error_message = null;
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

$success = null;
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}

if ($error_message) {
    echo '<div class="uk-alert-danger uk-width-1-3@s uk-align-center uk-margin-medium" uk-alert style="background: #8F1E24">
    <a class="uk-alert-close" uk-close></a>
    <p>'. $error_message .'</p>
</div>';

}

if ($success){
   echo '<div class="uk-alert uk-width-1-3@s uk-align-center uk-margin-medium" uk-alert style="background: darkgreen">
        <a class="uk-alert-close" uk-close></a>
        <p>'. $success.'</p>
</div>';
}
?>