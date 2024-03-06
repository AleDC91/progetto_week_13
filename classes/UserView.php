<?php


class UserView
{

    public function __construct(private ?PDOStatement $users, private ?Admin $admin = null)
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


    public function displayUsersAdmin()
    {
        if (!$this->admin) {
            echo "<h2 class='mt-5 text-center'>Accesso negato</h2>";
            return;
        }

        if (!$this->users) {
            echo "<h2 class='mt-5 text-center'>Nessun utente trovato</h2>";
        } else {
            echo "<table class='table table-striped table-hover'>";
            echo "<thead><tr><th scope='col'>First Name</th><th scope='col'>Last Name</th><th scope='col'>Email</th><th class='text-center'>Actions</th></tr></thead>";
            echo "<tbody>";

            while ($user = $this->users->fetch(PDO::FETCH_ASSOC)) {
                if ($this->admin->email === $user["email"]) {
                    continue;
                }
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['firstname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['lastname']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . $user["id"] . "'>
                            <button class='btn btn-warning'>
                                <i class='bi bi-pencil-square'></i>
                            </button>
                        </a>                     
                    </td>
                    
                    <td>
                    <form action='controller.php' method='POST'>
                        <input type='hidden' name='userId' value='" . $user["id"] . "'>
                        <button class='btn btn-danger' name ='delete-user'>
                            <i class='bi bi-trash'></i>
                        </button>
                    </form> 
                    </td>";

                if (!$user['isAdmin']) {
                    echo "<td>
                            <form action='controller.php' method='POST'>
                            <input type='hidden' name='userId' value='" . $user["id"] . "'>
                                <button class='btn btn-info' name='upgrade-user'>
                                   <i class='bi bi-person-up'></i>
                                </button>
                            </form>
                        </td>";
                } else {
                    echo "<td>
                            <form action='controller.php' method='POST'>
                                <input type='hidden' name='userId' value='" . $user["id"] . "'>
                                <button class='btn btn-primary' name ='downgrade-user'>
                                    <i class='bi bi-person-x'></i>
                                </button>
                            </form>
                        </td>";
                }

                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        }
    }
}
