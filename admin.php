<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('database.php');
require_once('classes/UserDTO.php');
require_once('classes/Admin.php');
require_once('classes/User.php');
require_once('classes/UserView.php');

use db\DB_PDO as DB;

session_start();
$username = isset($_SESSION["userName"]) ? $_SESSION["userName"] : "guest";
$isLogged = isset($_SESSION["isLogged"]) && $_SESSION["isLogged"];
$isAdmin = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"];

$config = require_once 'settings/config.php';

$dbPDO = DB::getInstance($config);
$conn = $dbPDO->getConnection();

$userDTO = new UserDTO($conn);


if (!$isAdmin) {
    header("Location: http://localhost/index.php");
    exit();
}
require_once 'partials/header.php';

$admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
$allUsers = $userDTO->getAllUsers($config);
?>

<h1 class="text-center">Admin Page</h1>

<div class="container">

    <div class="my-5 pb-3">
        <a href="create.php"><button class="btn btn-outline-dark float-end">CREATE USER</button></a>
        <a href="editAdmin.php"><button class="btn btn-outline-info float-end me-3">EDIT ADMIN DATA</button></a>
    </div>
    <div class="my-5">
        <?php
        $userView = new UserView($allUsers, $admin);
        $userView->displayUsersAdmin(); ?>
    </div>
</div>



<?php
require_once 'partials/footer.php';
?>