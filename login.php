<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php 
require_once('database.php');
require_once('classes/Form.php');
use ADC\Form as Form;
use db\DB_PDO as DB;

$config = require_once 'settings/config.php';

$dbPDO = DB::getInstance($config);
$conn = $dbPDO->getConnection();
require_once('partials/header.php'); ?>

<?php
$isLogged = false;

session_start();
if (isset($_SESSION["isLogged"])) {
    $isLogged = true;
    header("Location: http://localhost/index.php");
    exit();
}

?>

<h1 class="text-center">Login</h1>

<?php 

$form = new Form("controller.php");
$form->setButtonName("Login");
$form->setButtonId("login-form");
$form->setTextFields([]);
$form->setCheckboxFields(["remember me"]);
$form->drawForm();

?>


<?php require_once('partials/footer.php'); ?>