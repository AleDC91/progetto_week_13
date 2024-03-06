<?php 

session_start();
$username = isset($_SESSION["userName"]) ? $_SESSION["userName"] : "guest";
$isLogged = isset($_SESSION["isLogged"]) && $_SESSION["isLogged"];
$isAdmin = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"];
// $currentPage = basename($_SERVER['PHP_SELF']);

if(!$isAdmin){
    header("Location: http://localhost/index.php");
    exit();
}
require_once 'partials/header.php';
?>
<h1 class="text-center">Login</h1>




<?php 
require_once 'partials/footer.php';
 ?>





