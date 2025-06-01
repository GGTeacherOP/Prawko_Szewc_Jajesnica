<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $imie = $_POST['imie'];
                $nazwisko = $_POST['nazwisko'];
                $email = $_POST['email'];
                $telefon = $_POST['telefon'];
                $kategorie = $_POST['kategorie'];
                
                $stmt = $conn->prepare("INSERT INTO instruktorzy (imie, nazwisko, email, telefon, kategorie) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $imie, $nazwisko, $email, $telefon, $kategorie);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $imie = $_POST['imie'];
                $nazwisko = $_POST['nazwisko'];
                $email = $_POST['email'];
                $telefon = $_POST['telefon'];
                $kategorie = $_POST['kategorie'];
                
                $stmt = $conn->prepare("UPDATE instruktorzy SET imie = ?, nazwisko = ?, email = ?, telefon = ?, kategorie = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $imie, $nazwisko, $email, $telefon, $kategorie, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM instruktorzy WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Get all instructors with their course information
$result = $conn->query("
    SELECT i.*, 
           COUNT(DISTINCT j.id) as liczba_jazd,
           COUNT(DISTINCT k.id) as liczba_kursantow
    FROM instruktorzy i
    LEFT JOIN jazdy j ON i.id = j.instruktor_id
    LEFT JOIN zapisy z ON j.kursant_id = z.uzytkownik_id
    LEFT JOIN kursy k ON z.kurs_id = k.id
    GROUP BY i.id
    ORDER BY i.nazwisko, i.imie
");
$instructors = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie instruktorami - Panel Administratora</title>
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

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
        }

        .admin-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .instructor-list {
            list-style: none;
            padding: 0;
        }

        .instructor-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .instructor-item:last-child {
            border-bottom: none;
        }

        .instructor-info {
            flex: 1;
        }

        .instructor-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .instructor-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .instructor-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .stat {
            background: var(--light-color);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Zarządzanie instruktorami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Lista instruktorów</h2>
                <ul class="instructor-list">
                    <?php foreach ($instructors as $instructor): ?>
                        <li class="instructor-item">
                            <div class="instructor-info">
                                <h3><?php echo htmlspecialchars($instructor['imie'] . ' ' . $instructor['nazwisko']); ?></h3>
                                <p>Email: <?php echo htmlspecialchars($instructor['email']); ?></p>
                                <p>Telefon: <?php echo htmlspecialchars($instructor['telefon']); ?></p>
                                <p>Kategorie: <?php echo htmlspecialchars($instructor['kategorie']); ?></p>
                                <div class="stats">
                                    <div class="stat">
                                        Jazdy: <?php echo $instructor['liczba_jazd']; ?>
                                    </div>
                                    <div class="stat">
                                        Kursanci: <?php echo $instructor['liczba_kursantow']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="instructor-actions">
                                <button class="btn-edit" onclick="editInstructor(<?php echo htmlspecialchars(json_encode($instructor)); ?>)">Edytuj</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tego instruktora?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $instructor['id']; ?>">
                                    <button type="submit" class="btn-delete">Usuń</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="admin-section">
                <h2>Dodaj/Edytuj instruktora</h2>
                <form method="POST" id="instructorForm">
                    <input type="hidden" name="action" value="add" id="formAction">
                    <input type="hidden" name="id" value="" id="instructorId">
                    
                    <div class="form-group">
                        <label for="imie">Imię</label>
                        <input type="text" id="imie" name="imie" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nazwisko">Nazwisko</label>
                        <input type="text" id="nazwisko" name="nazwisko" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefon">Telefon</label>
                        <input type="tel" id="telefon" name="telefon" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="kategorie">Kategorie</label>
                        <select id="kategorie" name="kategorie" multiple required>
                            <option value="A">Kategoria A</option>
                            <option value="B">Kategoria B</option>
                            <option value="C">Kategoria C</option>
                            <option value="D">Kategoria D</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-submit">Zapisz instruktora</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editInstructor(instructor) {
            document.getElementById('formAction').value = 'edit';
            document.getElementById('instructorId').value = instructor.id;
            document.getElementById('imie').value = instructor.imie;
            document.getElementById('nazwisko').value = instructor.nazwisko;
            document.getElementById('email').value = instructor.email;
            document.getElementById('telefon').value = instructor.telefon;
            
            // Handle multiple select
            const kategorie = instructor.kategorie.split(',');
            const select = document.getElementById('kategorie');
            Array.from(select.options).forEach(option => {
                option.selected = kategorie.includes(option.value);
            });
            
            // Scroll to form
            document.querySelector('.admin-section:last-child').scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form when clicking "Add new" button
        document.querySelector('.btn-submit').addEventListener('click', function(e) {
            if (document.getElementById('formAction').value === 'add') {
                document.getElementById('instructorForm').reset();
            }
        });
    </script>
</body>
</html> 