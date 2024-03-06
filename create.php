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
?>

<h1 class="text-center">CREATE USER</h1>

<form class='mt-5' action='controller.php' method='POST'>
    <div class='mb-3'>
        <label for='firstname' class='form-label'>Firstname</label>
        <input type='text' id='firstname' name='firstname' class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='lastname' class='form-label'>Lastname</label>
        <input type='text' id='lastname' name='lastname' class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='email' class='form-label'>Email</label>
        <input type='email' id='email' name='email'class='form-control'>
    </div>
    <div class='mb-3'>
        <label for='password' class='form-label'>Password</label>
        <input type='password' id='password' name='password'class='form-control'>
    </div>

    <button type='submit' name='create' id='create' class='btn btn-success mb-5'>CREATE</button>
</form>
