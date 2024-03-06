<?php

use db\DB_PDO as DB;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'classes/User.php';
require_once 'classes/UserDTO.php';
require_once 'database.php';
require_once 'classes/UserView.php';
$config = require_once 'settings/config.php';

$dbPDO = DB::getInstance($config);
$conn = $dbPDO->getConnection();

$userDTO = new UserDTO($conn);

$isLogged = false;
$isAdmin = false;

session_start();
if (isset($_SESSION["isLogged"])) {
    $isLogged = true;

} else if (isset($_COOKIE["auth_token"])) {
    $user = $userDTO->getUserByToken($_COOKIE["auth_token"]);

    $_SESSION["isLogged"] = true;
    $isLogged = true;
    $_SESSION["userName"] = $user["firstname"];
    $_SESSION["lastName"] = $user["lastname"];
    $_SESSION["userEmail"] = $user["email"];
    $_SESSION["userID"] = $user["id"];

} else {
    header("Location: http://localhost/login.php");
    exit();
}
session_write_close();
require_once 'partials/header.php';
?>

<h1 class="text-center my-4">Home Page</h1>
<?php

$allUsers = $userDTO->getAllUsers($config);
$userView = new UserView($allUsers);
$userView->displayUsers();

?>





<?php include 'partials/footer.php';
