<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progetto week 13</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css"/>
</head>
  <body>
<?php require_once("navbar.php")  ?>

<main class="container main-container mt-5 pt-5">
<?php  session_start();

if (isset($_SESSION["errorMsg"])) { ?>
    <div class="alert alert-danger text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["errorMsg"] ?>
    </div>
<?php unset($_SESSION["errorMsg"]);
} else if (isset($_SESSION["successMsg"])) { ?>
    <div class="alert alert-success text-center mt-3 w-50 mx-auto" role="alert" id="msg-box">
        <?= $_SESSION["successMsg"] ?>
    </div>
<?php unset($_SESSION["successMsg"]);
}
session_write_close()
?>