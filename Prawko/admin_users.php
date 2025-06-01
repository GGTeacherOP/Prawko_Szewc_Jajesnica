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
        $user_id = $_POST['user_id'];
        
        switch ($_POST['action']) {
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM uzytkownicy WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                break;
                
            case 'update_role':
                $new_role = $_POST['new_role'];
                $stmt = $conn->prepare("UPDATE uzytkownicy SET rola = ? WHERE id = ?");
                $stmt->bind_param("si", $new_role, $user_id);
                $stmt->execute();
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
$query = "SELECT * FROM uzytkownicy ORDER BY id DESC LIMIT ? OFFSET ?";
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
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Zarządzanie Użytkownikami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
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
                                        <option value="admin">Admin</option>
                                        <option value="instruktor">Instruktor</option>
                                        <option value="kursant">Kursant</option>
                                        <option value="ksiegowy">Księgowy</option>
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