<?php
// require 'vendor/autoload.php';
require_once("database.php");
// require_once('functions.php');
// require_once('mail.php');
require_once 'classes/UserDTO.php';
require_once 'classes/User.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use db\DB_PDO as DB;

session_start();


$config = require_once('settings/config.php');


$dbPDO = DB::getInstance($config);
$conn = $dbPDO->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    unset($_SESSION["errorMsg"]);
    unset($_SESSION["successMsg"]);

    if (isset($_POST["register-form"])) {

        $userDTO = new UserDTO($conn);
        $dbEmailList = [];
        foreach ($userDTO->getAllUsersEmail($config) as $mail) {
            $dbEmailList[] = $mail["email"];
        };
        var_dump($dbEmailList) ;
        // var_dump($dbEmailList);


        $firstName = htmlspecialchars(trim($_POST['firstname']));
        $lastName = htmlspecialchars(trim($_POST['lastname']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $remember = isset($_POST['remember me']) ? 1 : 0;


        echo $firstName . " " . $lastName . " " . $email . " " . $remember . " " . $password;

        if (strlen($firstName) < 2) {
            $_SESSION["errorMsg"] = "First name <b> " . $firstName . " </b>troppo corto";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (strlen($lastName) < 2) {
            $_SESSION["errorMsg"] = "Last name <b> " . $lastName . " </b>troppo corto";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (strlen($_POST['password']) < 8) {
            $_SESSION["errorMsg"] = "La password deve essere di almeno 8 caratteri";
            header("Location: http://localhost/register.php");
            exit();
        } elseif (in_array($email, $dbEmailList)) {
            $_SESSION["errorMsg"] = "Indirizzo email già presente nel database! 
                                     Inserisci una nuova email o fai il login";
            header("Location: http://localhost/register.php");
            exit();
        } else {
            $user = new User($firstName, $lastName, $email, $password);
            $userDTO = new UserDTO($conn);
            $userDTO->registerUser($config, $user);
            $_SESSION["successMsg"] = "Nuovo utente registrato!";
            header("Location: http://localhost/index.php");
            exit();
        }
    }

    if (isset($_POST['logout'])) {
        session_unset();
        setcookie("auth_token", "", time() - 1);
        header("Location: http://localhost/login.php");
        exit();
    }

    if (isset($_POST['login-form'])) {

        $userDTO = new UserDTO($conn);
        $allUsers = $userDTO->getAllUsers($config);

        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["errorMsg"] = "Invalid email";
            header("Location: http://localhost/login.php");
            exit();
        }
        if (strlen($password) < 8) {
            $_SESSION["errorMsg"] = "Password deve essere di almeno 8 caratteri";
            header("Location: http://localhost/login.php");
            exit();
        }

        $loggedMatch = false;
        foreach ($allUsers as $user) {
            if ($user["email"] == $email && password_verify($password, $user["password"])) {
                $loggedMatch = true;

                $_SESSION["isLogged"] = true;
                $_SESSION["userName"] = $user["firstname"];
                $_SESSION["lastName"] = $user["lastname"];
                $_SESSION["userEmail"] = $user["email"];
                $_SESSION["userID"] = $user["id"];
                break;
            }
        }

        if ($loggedMatch) {
            unset($_SESSION["errorMsg"]);
            $_SESSION["successMsg"] = "Loggato con successo";

            if (isset($_POST["remember_me"])) {
                $token = bin2hex(random_bytes(32));
                $userDTO->updateUserToken($user["id"], $token);
                
            } else {
                if (isset($_COOKIE['auth_token'])) {
                    setcookie("auth_token", "", time() - 3600);
                }
            }

            header("Location: http://localhost/index.php");
            exit();
        } else {
            $_SESSION["errorMsg"] = "Email o password errati";
            header("Location: http://localhost/login.php");
            exit();
        }
    }
}
