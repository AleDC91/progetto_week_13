<?php

class Admin extends User
{
    private PDO $conn;

    function __construct(
        private string $firstname,
        private string $lastname,
        private string $email,
        private string $password,
        private bool $isAdmin = true,

    ) {
        parent::__construct(
            $firstname,
            $lastname,
            $email,
            $password
        );
        $this->isAdmin = $isAdmin;
    }

    public function setPDO(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function deleteUser(User $user)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute(['id' => $user->id]);
    }

    public function updateUser(User $user)
    {
        $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email, password = :password WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute([
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'password' => $user->password,
            'id' => $user->id
        ]);
        return $stm->rowCount();
    }

    public function upgradeUserToAdmin(User $user)
    {
        $sql = "UPDATE users SET isAdmin = :isAdmin WHERE id = :id";
        $stm = $this->conn->prepare($sql);
        $stm->execute(["isAdmin" => true, "id" => $user->id]);
    }

    
}
