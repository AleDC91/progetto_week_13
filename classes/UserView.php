<?php


class UserView
{

    public function __construct(private ?PDOStatement $users)
    {
    }

    public function displayUsers()
    {
        if (!$this->users) {
            echo "<h2 class='mt-5 text-center'>No Users Found</h2>";
        } else {
            // Inizializza una tabella HTML per mostrare gli utenti
            echo "<table class='table table-striped table-hover'>";
            echo "<thead><tr><th scope='col'>First Name</th><th scope='col'>Last Name</th><th scope='col'>Email</th></tr></thead>";
            echo "<tbody>";

            // Mostra gli utenti all'interno della tabella
            while ($user = $this->users->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['firstname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['lastname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        }
    }
}
