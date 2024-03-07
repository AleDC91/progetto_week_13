<?php

class UserDTO
{

    public function __construct(private PDO $conn)
    {
    }


    public function getAllUsers($config)
    {
        $logger = Logger::getInstance();

        try {
            $db = $config['database'];
            $sql = "SELECT * FROM  $db.users";
            $res = $this->conn->query($sql, PDO::FETCH_ASSOC);
            return $res ? $res : null;
        } catch (PDOException $e) {
            $logger->log("Errore durante il fetch degli utenti: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore durante il fetch degli utenti: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    public function getAllUsersEmail($config)
    {
        $logger = Logger::getInstance();

        $db = $config['database'];
        try {
            $sql = "SELECT email FROM  $db.users";
            // echo $sql;
            $res = $this->conn->query($sql, PDO::FETCH_ASSOC);
            return $res ? $res : null;
        } catch (PDOException $e) {
            $logger->log("Errore durante il fetch degli utenti: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore durante il fetch degli utenti: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }


    public function registerUser(array $config, User $user)
    {
        $logger = Logger::getInstance();
        try {
            $sql = "INSERT INTO " . $config['database'] . ".users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)";
            $stm = $this->conn->prepare($sql);
            $stm->execute(['firstname' => $user->firstname, 'lastname' => $user->lastname, 'email' => $user->email, 'password' => $user->password]);
            return $stm->rowCount();
        } catch (PDOException $e) {
            $logger->log("Errore nella registrazione dell'utente: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nella registrazione dell'utente: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    public function updateUserToken(int $userId, string $token)
    {
        $logger = Logger::getInstance();
        try {
            setcookie('auth_token', $token, time() + (86400 * 30));
            $stmt = $this->conn->prepare("UPDATE users SET auth_token = :token WHERE id = :userId");
            $stmt->execute(['token' => $token, 'userId' => $userId]);
        } catch (PDOException $e) {
            $logger->log("Errore nell'updateUserToken: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nellupdate del token";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }


    public function getUserByToken(string $token)
    {
        $logger = Logger::getInstance();

        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE auth_token = :token");
            $stmt->execute(['token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $logger->log("Errore nel getUserToken: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nel getUserToken";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    public function getUserById(string $id)
    {
        $logger = Logger::getInstance();

        try {

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $logger->log("Errore nel getUserById: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nel getUserByToken";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }


    public function getUserByEmail(string $email)
    {
        $logger = Logger::getInstance();

        try{
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            $logger->log("Errore nel getUserByEmail: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nel getUserByEmail";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

    }


    public function isUserAdmin($userId)
    {
        $logger = Logger::getInstance();

        try {
            $stmt = $this->conn->prepare("SELECT isAdmin FROM users WHERE id = :userId");
            $stmt->execute(['userId' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
            if ($result && $result['isAdmin']) {
                return true;
            } else {
                return false;
            }
        }catch(PDOException $e) {
            $logger->log("Errore nel getUserByEmail: " . $e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore nel getUserByEmail";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
    }
}
