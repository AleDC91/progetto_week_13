<?php

class UserDTO
{

    public function __construct(private PDO $conn)
    {
    }


    public function getAllUsers($config)
    {
        $db = $config['database'];
        $sql = "SELECT * FROM  $db.users";
        $res = $this->conn->query($sql, PDO::FETCH_ASSOC);
        return $res ? $res : null;
    }

    public function getAllUsersEmail($config)
    {
        $db = $config['database'];
        // echo $db;
        $sql = "SELECT email FROM  $db.users";
        // echo $sql;
        $res = $this->conn->query($sql, PDO::FETCH_ASSOC);
        return $res ? $res : null;
    }


    public function registerUser(array $config, User $user)
    {
        $sql = "INSERT INTO " . $config['database'] . ".users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['firstname' => $user->firstname, 'lastname' => $user->lastname, 'email' => $user->email, 'password' => $user->password]);
        return $stm->rowCount();
    }

    public function updateUserToken(int $userId, string $token)
    {
        setcookie('auth_token', $token, time() + (86400 * 30));
        $stmt = $this->conn->prepare("UPDATE users SET auth_token = :token WHERE id = :userId");
        $stmt->execute(['token' => $token, 'userId' => $userId]);
    }

    public function getUserByToken(string $token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE auth_token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isUserAdmin($userId)
    {
        $stmt = $this->conn->prepare("SELECT isAdmin FROM users WHERE id = :userId");
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se l'utente esiste e se è un amministratore
        if ($result && $result['isAdmin']) {
            return true; // L'utente è un amministratore
        } else {
            return false; // L'utente non è un amministratore
        }
    }
}
