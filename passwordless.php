<?php
/**
 * Includes
 */
require 'vendor/autoload.php';
require_once 'config.php';
require_once "LoginController.php";

$loginController = new LoginController($rublon_cfg);

/* user is logged-in */
if (!empty($_SESSION['user'])) {
    /* user wants to logout */
    if (!empty($_GET['action']) && $_GET['action'] === 'logout') {
        unset($_SESSION['user']);
        header('Location: passwordless.php');
    }
} else if (!empty($_POST['action']) && $_POST['action'] === 'passwordless') {
    /* user is trying to login */
    $rublonLoginBox = $loginController->passwordlessLogin();
    header('Location: passwordless.php');
}
?>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="validator.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="row">
                <div class="col-4 logo-container">
                    <img src="Rublon-favicon-128.png" class="mr-3 logo">
                    <a href="passwordless.php">Rublon Example</a>
                </div>
                <div class="col-4"></div>
                <div class="col-4 align-right">
                    <?php
                    if (!empty($_SESSION['user'])) {
                        ?>
                        <a href="?action=logout" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <?php
                    } else {
                        ?>
                        <a href="index.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Switch to 2factor login</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <?php
    include('flashmsg.php');

    /* user is logged-in */
    if (!empty($_SESSION['user'])) {
        /* show restricted page */
        include 'home.php';
    } else {
        /* show widget */
        $rublonLoginBox = $loginController->renderWidget();
        ?>
        <div id="rublonLoginBox">
            <?php
            echo $rublonLoginBox['rublonLoginBox'];
            ?>
        </div>
        <?php
    }
    ?>
</body>