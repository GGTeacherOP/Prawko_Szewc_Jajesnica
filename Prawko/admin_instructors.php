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
        $errors = [];
        
        // Validate required fields
        if (empty($_POST['imie'])) {
            $errors[] = "Imię jest wymagane.";
        }
        if (empty($_POST['nazwisko'])) {
            $errors[] = "Nazwisko jest wymagane.";
        }
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Podaj prawidłowy adres email.";
        }
        if (empty($_POST['telefon'])) {
            $errors[] = "Telefon jest wymagany.";
        }
        if (empty($_POST['haslo'])) {
            $errors[] = "Hasło jest wymagane.";
        }
        if (empty($_POST['kategorie_uprawnien'])) {
            $errors[] = "Wybierz przynajmniej jedną kategorię.";
        }
        
        if (empty($errors)) {
            switch ($_POST['action']) {
                case 'add':
                    $imie = trim($_POST['imie']);
                    $nazwisko = trim($_POST['nazwisko']);
                    $email = trim($_POST['email']);
                    $telefon = trim($_POST['telefon']);
                    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
                    $kategorie_uprawnien = implode(',', $_POST['kategorie_uprawnien']);
                    
                    try {
                        // Check if email already exists
                        $check_stmt = $conn->prepare("SELECT id FROM pracownicy WHERE email = ?");
                        $check_stmt->bind_param("s", $email);
                        $check_stmt->execute();
                        $check_result = $check_stmt->get_result();
                        
                        if ($check_result->num_rows > 0) {
                            $errors[] = "Ten email jest już zajęty.";
                        } else {
                            // Insert into pracownicy
                            $stmt = $conn->prepare("INSERT INTO pracownicy (imie, nazwisko, email, telefon, haslo, kategorie_uprawnien, rola) VALUES (?, ?, ?, ?, ?, ?, 'instruktor')");
                            $stmt->bind_param("ssssss", $imie, $nazwisko, $email, $telefon, $haslo, $kategorie_uprawnien);
                            $stmt->execute();
                            
                            header("Location: admin_instructors.php?success=1");
                            exit();
                        }
                    } catch (Exception $e) {
                        $errors[] = "Wystąpił błąd podczas dodawania instruktora: " . $e->getMessage();
                    }
                    break;
                    
                case 'edit':
                    $id = $_POST['id'];
                    $imie = trim($_POST['imie']);
                    $nazwisko = trim($_POST['nazwisko']);
                    $email = trim($_POST['email']);
                    $telefon = trim($_POST['telefon']);
                    $kategorie_uprawnien = implode(',', $_POST['kategorie_uprawnien']);
                    
                    try {
                        // Update pracownicy
                        $stmt = $conn->prepare("UPDATE pracownicy SET imie = ?, nazwisko = ?, email = ?, telefon = ?, kategorie_uprawnien = ? WHERE id = ?");
                        $stmt->bind_param("sssssi", $imie, $nazwisko, $email, $telefon, $kategorie_uprawnien, $id);
                        $stmt->execute();
                        
                        header("Location: admin_instructors.php?success=2");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Wystąpił błąd podczas aktualizacji instruktora: " . $e->getMessage();
                    }
                    break;
                    
                case 'delete':
                    $id = $_POST['id'];
                    try {
                        // Delete from pracownicy
                        $stmt = $conn->prepare("DELETE FROM pracownicy WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        
                        header("Location: admin_instructors.php?success=3");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Wystąpił błąd podczas usuwania instruktora: " . $e->getMessage();
                    }
                    break;
            }
        }
    }
}

// Get all instructors with their course information
$result = $conn->query("
    SELECT p.*, 
           COUNT(DISTINCT j.id) as liczba_jazd,
           COUNT(DISTINCT k.id) as liczba_kursantow
    FROM pracownicy p
    LEFT JOIN jazdy j ON p.id = j.instruktor_id
    LEFT JOIN zapisy z ON j.kursant_id = z.uzytkownik_id
    LEFT JOIN kursy k ON z.kurs_id = k.id
    WHERE p.rola = 'instruktor'
    GROUP BY p.id
    ORDER BY p.nazwisko, p.imie
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

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
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
            <h1>Zarządzanie instruktorami</h1>
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
                <?php
                switch ($_GET['success']) {
                    case 1:
                        echo "Instruktor został pomyślnie dodany.";
                        break;
                    case 2:
                        echo "Instruktor został pomyślnie zaktualizowany.";
                        break;
                    case 3:
                        echo "Instruktor został pomyślnie usunięty.";
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Lista instruktorów</h2>
                <?php if (empty($instructors)): ?>
                    <p>Brak dostępnych instruktorów.</p>
                <?php else: ?>
                    <ul class="instructor-list">
                        <?php foreach ($instructors as $instructor): ?>
                            <li class="instructor-item">
                                <div class="instructor-info">
                                    <h3><?php echo htmlspecialchars($instructor['imie'] . ' ' . $instructor['nazwisko']); ?></h3>
                                    <p>Email: <?php echo htmlspecialchars($instructor['email']); ?></p>
                                    <p>Telefon: <?php echo htmlspecialchars($instructor['telefon']); ?></p>
                                    <p>Kategorie: <?php echo htmlspecialchars($instructor['kategorie_uprawnien']); ?></p>
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
                <?php endif; ?>
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
                        <label for="haslo">Hasło</label>
                        <input type="password" id="haslo" name="haslo" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategorie</label>
                        <div class="checkbox-group">
                            <label>
                                <input type="checkbox" name="kategorie_uprawnien[]" value="A"> Kategoria A - Motocykle
                            </label>
                            <label>
                                <input type="checkbox" name="kategorie_uprawnien[]" value="B"> Kategoria B - Samochody osobowe
                            </label>
                            <label>
                                <input type="checkbox" name="kategorie_uprawnien[]" value="C"> Kategoria C - Samochody ciężarowe
                            </label>
                            <label>
                                <input type="checkbox" name="kategorie_uprawnien[]" value="D"> Kategoria D - Autobusy
                            </label>
                            <label>
                                <input type="checkbox" name="kategorie_uprawnien[]" value="inne"> Inne
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submitBtn">Dodaj instruktora</button>
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
            
            // Clear all checkboxes
            document.querySelectorAll('input[name="kategorie_uprawnien[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check appropriate categories
            const categories = instructor.kategorie_uprawnien.split(',');
            categories.forEach(category => {
                const checkbox = document.querySelector(`input[name="kategorie_uprawnien[]"][value="${category}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            
            // Hide login and password fields when editing
            document.getElementById('haslo').parentElement.style.display = 'none';
            
            document.getElementById('submitBtn').textContent = 'Zapisz zmiany';
            
            // Scroll to form
            document.getElementById('instructorForm').scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form after submission
        document.getElementById('instructorForm').addEventListener('submit', function() {
            setTimeout(function() {
                document.getElementById('formAction').value = 'add';
                document.getElementById('instructorId').value = '';
                document.getElementById('instructorForm').reset();
                document.getElementById('submitBtn').textContent = 'Dodaj instruktora';
                
                // Show login and password fields
                document.getElementById('haslo').parentElement.style.display = 'block';
            }, 100);
        });
    </script>
</body>
</html> 