<?php
// require 'vendor/autoload.php';
require_once("database.php");
// require_once('functions.php');
// require_once('mail.php');
require_once 'classes/UserDTO.php';
require_once 'classes/User.php';
require_once 'classes/Admin.php';
require_once 'classes/Logger.php';


$logger = Logger::getInstance();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use db\DB_PDO as DB;

session_start();


$config = require_once('settings/config.php');


$dbPDO = DB::getInstance($config);
$conn = $dbPDO->getConnection();
$userDTO = new UserDTO($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    unset($_SESSION["errorMsg"]);
    unset($_SESSION["successMsg"]);

    if (isset($_POST["register-form"])) {

        $userDTO = new UserDTO($conn);
        $dbEmailList = [];
        foreach ($userDTO->getAllUsersEmail($config) as $mail) {
            $dbEmailList[] = $mail["email"];
        };
        var_dump($dbEmailList);
        // var_dump($dbEmailList);


        $firstName = htmlspecialchars(trim($_POST['firstname']));
        $lastName = htmlspecialchars(trim($_POST['lastname']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $remember = isset($_POST['remember me']) ? 1 : 0;


        echo $firstName . " " . $lastName . " " . $email . " " . $remember . " " . $password;

        if (strlen($firstName) < 2) {
            $_SESSION["errorMsg"] = "First name <b> " . $firstName . " </b>troppo corto";
            $logger->log("Registrazione nome troppo corto");
            header("Location: http://localhost/register.php");
            exit();

        } elseif (strlen($lastName) < 2) {
            $_SESSION["errorMsg"] = "Last name <b> " . $lastName . " </b>troppo corto";
            $logger->log("Registrazione lastname troppo corto");

            header("Location: http://localhost/register.php");
            exit();
        } elseif (strlen($_POST['password']) < 8) {
            $_SESSION["errorMsg"] = "La password deve essere di almeno 8 caratteri";
            $logger->log("Registrazione password troppo corta");
            header("Location: http://localhost/register.php");
            exit();
        } elseif (in_array($email, $dbEmailList)) {
            $logger->log("Email $email già presente");
            $_SESSION["errorMsg"] = "Indirizzo email già presente nel database! 
                                     Inserisci una nuova email o fai il login";
            header("Location: http://localhost/register.php");
            exit();
        } else {
            $user = new User($firstName, $lastName, $email, $password);
            $userDTO = new UserDTO($conn);
            if($userDTO->registerUser($config, $user)){
            $userData = $userDTO->getUserByEmail($email);
            $_SESSION["successMsg"] = "Nuovo utente registrato!";
            $_SESSION["isLogged"] = true;
            $_SESSION["userName"] = $firstName;
            $_SESSION["lastName"] = $lastName;
            $_SESSION["userEmail"] =  $email;
            $_SESSION["userID"] = $userData->id;
            $_SESSION["userPassword"] = $userData->password;
            $logger->log("Registrazione effettuata utente: $firstName, ID: ". $userData['id']);
        }
            header("Location: http://localhost/index.php");
            exit();
        }
    }

    if (isset($_POST['logout'])) {
        $logger->log("Logout " . $_SESSION['userName']);
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
            $logger->log("Registrazione password troppo corta");
            header("Location: http://localhost/login.php");
            exit();
        }

        $loggedMatch = false;
        foreach ($allUsers as $user) {
            if ($user["email"] == $email && password_verify($password, $user["password"])) {
            $logger->log("Utente ID: " . $user['id'] . " Loggato" );

                $loggedMatch = true;
                $_SESSION["isLogged"] = true;
                $_SESSION["userName"] = $user["firstname"];
                $_SESSION["lastName"] = $user["lastname"];
                $_SESSION["userEmail"] = $user["email"];
                $_SESSION["userID"] = $user["id"];
                $_SESSION["userPassword"] = $user["password"];
                $_SESSION["isAdmin"] = $user["isAdmin"];
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
            $logger->log("Tentativo login");

            header("Location: http://localhost/login.php");
            exit();
        }
    }

    if (isset($_POST["delete-user"])) {
        if (isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"]) {
            $userData = $userDTO->getUserById($_POST["userId"]);
            if($userData){
                $userToDelete = new User($userData['firstname'], $userData['lastname'], $userData['email'], $userData['password'], $userData['id']);
                $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
                $admin->setPDO($conn);
                if ($admin->deleteUser($userToDelete)) {
                    $_SESSION["successMsg"] = "Utente Eliminato";
                $logger->log("Utente ID: ".$userData['id'] . "eliminato");
    
                } else {
                    $logger->log("Utente ID: ".$userData['id'] . "NON eliminato correttamente", "ERROR");
    
                    $_SESSION["errorMsg"] = "Errore nell'eliminazione dell'utente";
                };
                header("Location: http://localhost/admin.php");
                exit();
            } else {
                $logger->log("Utente non trovato nel DB", "ERROR");
                $_SESSION['errorMsg'] = "Errore durante l'eliminazione dell'utente: ";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit(); 
    
            }

        }
    }

    if (isset($_POST["upgrade-user"])) {
        if (isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"]) {
            $userData = $userDTO->getUserById($_POST["userId"]);
            $userToUpgrade = new User($userData['firstname'], $userData['lastname'], $userData['email'], $userData['password'], $userData['id']);
            $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
            $admin->setPDO($conn);
            if ($admin->upgradeUserToAdmin($userToUpgrade)) {
                $_SESSION["successMsg"] = "Nuovo admin aggiunto";
            } else {
                $_SESSION["errorMsg"] = "Errore nella modifica ad admin";
            };
            header("Location: http://localhost/admin.php");
            exit();
        }
    }

    if (isset($_POST["downgrade-user"])) {
        if (isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"]) {
            $userData = $userDTO->getUserById($_POST["userId"]);
            $adminToDowngrade = new Admin($userData['firstname'], $userData['lastname'], $userData['email'], $userData['password'], $userData['id']);
            $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
            $admin->setPDO($conn);
            if ($admin->downgradeAdminToUser($adminToDowngrade)) {
                $_SESSION["successMsg"] = "Admin rimosso!";
            } else {
                $_SESSION["errorMsg"] = "Errore nella rimozione dell'admin";
            };
            header("Location: http://localhost/admin.php");
            exit();
        }
    }

    if (isset($_POST["edit"])) {
        if (isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"]) {
            $userData = $userDTO->getUserById($_POST["userId"]);

            $userToUpdate = new User(
                $_POST['firstname'],
                $_POST['lastname'],
                $_POST['email'],
                $userData['password'],
                $_POST["userId"]
            );

            $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
            $admin->setPDO($conn);
            if ($admin->updateUser($userToUpdate)) {
                $_SESSION["successMsg"] = "Dati utente aggiornati!";
            } else {
                $_SESSION["errorMsg"] = "Errore nell'aggiornamento dei dati";
            };
            header("Location: http://localhost/admin.php");
            exit();
        }
    }

    if (isset($_POST['editAdmin'])) {
        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {
            $userData = $userDTO->getUserById($_SESSION['userID']);
            $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
            $admin->setPDO($conn);

            $firstName = htmlspecialchars(trim($_POST['firstname']));
            $lastName = htmlspecialchars(trim($_POST['lastname']));
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $oldPassword = $_POST['oldpassword'];
            $password = $_POST['password'];
            $password2 = $_POST['password2'];
            $newPassword;
            var_dump($firstName);
            var_dump($lastName);
            var_dump($email);
            var_dump($_SESSION);

            if ($password || $password2 || $oldPassword) {
                // var_dump($password);
                // var_dump($password2);
                // var_dump($oldPassword);


                if (password_verify($oldPassword, $_SESSION['userPassword'])) {
                    if ($password == $password2) {
                        if (strlen(trim($password)) < 8) {
                            $_SESSION['errorMsg'] = "La password deve essere lunga almeno 8 caratteri!";
                            header("Location: http://localhost/editAdmin.php");
                            exit();
                        } else {
                            $newPassword = password_hash($password, PASSWORD_DEFAULT);
                        }
                    } else {
                        $_SESSION['errorMsg'] = "Controlla bene le due password";
                        header("Location: http://localhost/editAdmin.php");
                        exit();
                    }
                } else {
                    $_SESSION['errorMsg'] = "Password corrente errata!";
                    header("Location: http://localhost/editAdmin.php");
                    exit();
                }
            } else {
                $newPassword = $userData["password"];
            }

            $update = new Admin(
                $firstName,
                $lastName,
                $email,
                $newPassword,
                $userData["id"]
            );
            var_dump($update);
            if ($admin->updateAdmin($update)) {
                session_start();

                $_SESSION["successMsg"] = "Dati Admin aggiornati!";
                $_SESSION["userName"] = $update->firstname;
                $_SESSION["lastName"] =  $update->lastname;
                $_SESSION["userEmail"] = $update->email;
                $_SESSION["userPassword"] =  $newPassword;

                var_dump($_SESSION);
            }
            header("Location: http://localhost/admin.php");
            exit();
        }
    }
    if(isset($_POST['create'])){
        if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {
            $userDTO = new UserDTO($conn);
            $dbEmailList = [];
            foreach ($userDTO->getAllUsersEmail($config) as $mail) {
                $dbEmailList[] = $mail["email"];
            };


            $firstName = htmlspecialchars(trim($_POST['firstname']));
            $lastName = htmlspecialchars(trim($_POST['lastname']));
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            echo $firstName . " " . $lastName . " " . $email . " " . $remember . " " . $password;
    
            if (strlen($firstName) < 2) {
                $_SESSION["errorMsg"] = "First name <b> " . $firstName . " </b>troppo corto";
                header("Location: http://localhost/create.php");
                exit();
            } elseif (strlen($lastName) < 2) {
                $_SESSION["errorMsg"] = "Last name <b> " . $lastName . " </b>troppo corto";
                header("Location: http://localhost/create.php");
                exit();
            } elseif (strlen($_POST['password']) < 8) {
                $_SESSION["errorMsg"] = "La password deve essere di almeno 8 caratteri";
                header("Location: http://localhost/create.php");
                exit();
            } elseif (in_array($email, $dbEmailList)) {
                $_SESSION["errorMsg"] = "Indirizzo email già presente nel database! 
                                         Inserisci una nuova email o fai il login";
                header("Location: http://localhost/create.php");
                exit();
            } else {
                $admin = new Admin($_SESSION["userName"], $_SESSION["lastName"], $_SESSION["userEmail"], $_SESSION["userPassword"]);
                $admin->setPDO($conn);
    
                $user = new User($firstName, $lastName, $email, $password);
                $admin->createUser($user);
                $_SESSION["successMsg"] = "Nuovo utente registrato!";
                header("Location: http://localhost/admin.php");
                exit();
            }
        }
    }
}
