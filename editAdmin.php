<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('database.php');
require_once('classes/UserDTO.php');
require_once('classes/Admin.php');
require_once('classes/User.php');
require_once('classes/UserView.php');
require_once('classes/Form.php');


use db\DB_PDO as DB;
// use ADC\Form as Form;

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
?>



<h1 class="text-center">EDIT ADMIN DATA</h1>

<form class='mt-5' action='controller.php' method='POST'>
    <div class='mb-3'>
        <label for='firstname' class='form-label'>Firstname</label>
        <input type='text' id='firstname' name='firstname' value="<?= $admin->firstname ?>" class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='lastname' class='form-label'>Lastname</label>
        <input type='text' id='lastname' name='lastname' value="<?= $admin->lastname ?>"class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='email' class='form-label'>Email</label>
        <input type='email' id='email' name='email' value="<?= $admin->email ?>"class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='oldpassword' class='form-label'>Vecchia Password</label>
        <input type='password' id='oldpassword' name='oldpassword' class='form-control'>
    </div>

    <div class='mb-3'>
        <label for='password' class='form-label'>Nuova Password</label>
        <input type='password' id='password' name='password' class='form-control'>
    </div>

    <div class='mb-3'>
        <label for='password2' class='form-label'>Conferma Nuova Password</label>
        <input type='password' id='password2' name='password2' class='form-control'>
    </div>

    <button type='submit' name='editAdmin' id='editAdmin' class='btn btn-primary mb-5'>Save</button>
</form>
