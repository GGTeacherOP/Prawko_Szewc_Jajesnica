<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Walidacja wymaganych pól
                $required_fields = ['imie', 'nazwisko', 'email', 'telefon', 'haslo', 'rola', 'login'];
                $errors = [];
                
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        $errors[] = "Pole " . ucfirst($field) . " jest wymagane.";
                    }
                }
                
                if (empty($errors)) {
                    $imie = trim($_POST['imie']);
                    $nazwisko = trim($_POST['nazwisko']);
                    $email = trim($_POST['email']);
                    $telefon = trim($_POST['telefon']);
                    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
                    $rola = $_POST['rola'];
                    $login = trim($_POST['login']);
                    
                    // Sprawdź czy login już istnieje
                    $check_stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE login = ?");
                    $check_stmt->bind_param("s", $login);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    
                    if ($check_result->num_rows > 0) {
                        $errors[] = "Ten login jest już zajęty.";
                    } else {
                        try {
                            $stmt = $conn->prepare("INSERT INTO uzytkownicy (imie, nazwisko, email, telefon, haslo, rola, login) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("sssssss", $imie, $nazwisko, $email, $telefon, $haslo, $rola, $login);
                            $stmt->execute();
                            
                            // Przekieruj po pomyślnym dodaniu
                            header("Location: admin_users.php?success=1");
                            exit();
                        } catch (Exception $e) {
                            $errors[] = "Wystąpił błąd podczas dodawania użytkownika: " . $e->getMessage();
                        }
                    }
                }
                break;
                
            case 'delete':
                $user_id = $_POST['user_id'];
                $stmt = $conn->prepare("DELETE FROM uzytkownicy WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                break;
                
            case 'update_role':
                $new_role = $_POST['new_role'];
                $user_id = $_POST['user_id'];
                if (!empty($new_role)) {
                    try {
                        // Sprawdź czy użytkownik jest w tabeli pracownicy
                        $check_stmt = $conn->prepare("SELECT id FROM pracownicy WHERE id = ?");
                        $check_stmt->bind_param("i", $user_id);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();
                        
                        if ($check_result->num_rows > 0) {
                            // Aktualizuj rolę w tabeli pracownicy
                            $stmt = $conn->prepare("UPDATE pracownicy SET rola = ? WHERE id = ?");
                        } else {
                            // Aktualizuj rolę w tabeli uzytkownicy
                            $stmt = $conn->prepare("UPDATE uzytkownicy SET rola = ? WHERE id = ?");
                        }
                        
                        $stmt->bind_param("si", $new_role, $user_id);
                        $stmt->execute();
                        
                        if ($stmt->affected_rows > 0) {
                            header("Location: admin_users.php?role_updated=1");
                            exit();
                        } else {
                            $errors[] = "Nie udało się zaktualizować roli użytkownika.";
                        }
                    } catch (Exception $e) {
                        $errors[] = "Błąd podczas aktualizacji roli: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total users count
$result = $conn->query("SELECT COUNT(*) as total FROM uzytkownicy");
$total_users = $result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users for current page
$query = "SELECT u.id, u.imie, u.nazwisko, u.email, u.telefon, u.haslo, u.rola, 'uzytkownicy' as source 
          FROM uzytkownicy u 
          UNION ALL 
          SELECT p.id, p.imie, p.nazwisko, p.email, p.telefon, p.haslo, p.rola, 'pracownicy' as source 
          FROM pracownicy p 
          ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$users = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie Użytkownikami - Panel Administratora</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .users-table th {
            background: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover {
            background: var(--light-color);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.9em;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .search-box {
            margin-bottom: 2rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .role-admin {
            background: #dc3545;
            color: white;
        }

        .role-instructor {
            background: #28a745;
            color: white;
        }

        .role-student {
            background: #17a2b8;
            color: white;
        }

        .role-accountant {
            background: #6c757d;
            color: white;
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 100px 1rem 2rem;
                padding: 1rem;
            }

            .users-table {
                display: block;
                overflow-x: auto;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        .add-user-form {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Zarządzanie Użytkownikami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <p>Użytkownik został pomyślnie dodany.</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['role_updated'])): ?>
            <div class="alert alert-success">
                <p>Rola użytkownika została pomyślnie zaktualizowana.</p>
            </div>
        <?php endif; ?>

        <div class="admin-section">
            <h2>Dodaj nowego użytkownika</h2>
            <form method="POST" class="add-user-form">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="imie">Imię:</label>
                    <input type="text" id="imie" name="imie" required>
                </div>

                <div class="form-group">
                    <label for="nazwisko">Nazwisko:</label>
                    <input type="text" id="nazwisko" name="nazwisko" required>
                </div>

                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telefon">Telefon:</label>
                    <input type="tel" id="telefon" name="telefon" required>
                </div>

                <div class="form-group">
                    <label for="haslo">Hasło:</label>
                    <input type="password" id="haslo" name="haslo" required>
                </div>

                <div class="form-group">
                    <label for="rola">Rola:</label>
                    <select id="rola" name="rola" required>
                        <option value="kursant">Kursant</option>
                        <option value="instruktor">Instruktor</option>
                        <option value="ksiegowy">Księgowy</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Dodaj użytkownika</button>
            </form>
        </div>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Szukaj użytkowników..." onkeyup="searchUsers()">
        </div>

        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imię i Nazwisko</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>Rola</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['imie'] . ' ' . $user['nazwisko']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['telefon']); ?></td>
                        <td>
                            <span class="role-badge role-<?php echo strtolower($user['rola']); ?>">
                                <?php echo ucfirst($user['rola']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="update_role">
                                    <select name="new_role" onchange="this.form.submit()" class="btn-small">
                                        <option value="">Zmień rolę</option>
                                        <?php if ($user['source'] === 'pracownicy'): ?>
                                            <option value="admin">Admin</option>
                                            <option value="instruktor">Instruktor</option>
                                            <option value="ksiegowy">Księgowy</option>
                                        <?php else: ?>
                                            <option value="admin">Admin</option>
                                            <option value="instruktor">Instruktor</option>
                                            <option value="kursant">Kursant</option>
                                        <?php endif; ?>
                                    </select>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-small btn-danger">Usuń</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        function searchUsers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.querySelector('.users-table');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html> 