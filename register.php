<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'partials/header.php';
require_once('classes/Form.php');

use ADC\Form as Form;

?>

<h1 class="text-center">Register</h1>
<?php

$regForm = new Form("controller.php");
$regForm->setButtonName("register");
$regForm->setButtonId('register-form');
$regForm->setTextFields(["firstname", "lastname"]);
$regForm->drawForm();
?>


</main>

<?php include 'partials/footer.php';
