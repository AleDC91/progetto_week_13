<?php

require_once('User.php');
require_once('Logger.php');


$logger = Logger::getInstance();
class Admin extends User
{
    private PDO $conn;

    function __construct(
        private string $firstname,
        private string $lastname,
        private string $email,
        private string $password,
        private ?int $id = null,
        private bool $isAdmin = true,

    ) {
        parent::__construct(
            $firstname,
            $lastname,
            $email,
            $password,
            $id
        );
        $this->isAdmin = $isAdmin;
    }

    public function setPDO(PDO $conn)
    {
        $this->conn = $conn;
    }


    public function createUser(User $user)
    {
    $logger = Logger::getInstance();

        try {
            $sql = "INSERT INTO users (firstname,lastname,email,password) VALUES (:firstname,:lastname,:email,:password)";
            $stm = $this->conn->prepare($sql);
            $stm->execute([
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'password' => $user->password,
        ]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            $logger->log($e->getMessage(), "ERROR");
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteUser(User $user)
    {
        $logger = Logger::getInstance();

        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute(['id' => $user->id]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            $logger->log($e->getMessage(), "ERROR");
            $_SESSION['errorMsg'] = "Errore durante l'eliminazione dell'utente: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit(); 

            // echo "Error: " . $e->getMessage();

        }
    }


    public function updateUser(User $user)
    {
        $logger = Logger::getInstance();

        try {
            $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute([
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'id' => $user->id
            ]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            echo "Error: " . $e->getMessage();
            $logger->log($e->getMessage(), "ERROR");

        }
    }

    public function upgradeUserToAdmin(User $user)
    {
        $logger = Logger::getInstance();

        try {
            $sql = "UPDATE users SET isAdmin = true WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute(["id" => $user->id]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            $logger->log($e->getMessage(), "ERROR");

            echo "Error: " . $e->getMessage();

        }
    }



    public function downgradeAdminToUser(Admin $admin)
    {
        $logger = Logger::getInstance();

        try {
            $sql = "UPDATE users SET isAdmin = false WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute(["id" => $admin->id]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            $logger->log($e->getMessage(), "ERROR");

            echo "Error: " . $e->getMessage();
        }
    }

    public function updateAdmin(Admin $admin)
    {
        $logger = Logger::getInstance();

        try {
            $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, password = :password WHERE id = :id";
            $stm = $this->conn->prepare($sql);
            $stm->execute([
                'firstname' => $admin->firstname,
                'lastname' => $admin->lastname,
                'email' => $admin->email,
                'password' => $admin->password,
                'id' => $admin->id
            ]);
            if ($stm->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            $logger->log($e->getMessage(), "ERROR");
            echo "Error: " . $e->getMessage();
        }
    }


}
