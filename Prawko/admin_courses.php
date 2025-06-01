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
                $nazwa = $_POST['nazwa'];
                $cena = $_POST['cena'];
                $opis = $_POST['opis'];
                $kategoria = $_POST['kategoria'];
                
                $stmt = $conn->prepare("INSERT INTO kursy (nazwa, cena, opis, kategoria) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdss", $nazwa, $cena, $opis, $kategoria);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $nazwa = $_POST['nazwa'];
                $cena = $_POST['cena'];
                $opis = $_POST['opis'];
                $kategoria = $_POST['kategoria'];
                
                $stmt = $conn->prepare("UPDATE kursy SET nazwa = ?, cena = ?, opis = ?, kategoria = ? WHERE id = ?");
                $stmt->bind_param("sdssi", $nazwa, $cena, $opis, $kategoria, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM kursy WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Get all courses
$result = $conn->query("SELECT * FROM kursy ORDER BY nazwa");
$courses = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie kursami - Panel Administratora</title>
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

        .course-list {
            list-style: none;
            padding: 0;
        }

        .course-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-info {
            flex: 1;
        }

        .course-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .course-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .course-actions {
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

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
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
            <h1>Zarządzanie kursami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Lista kursów</h2>
                <ul class="course-list">
                    <?php foreach ($courses as $course): ?>
                        <li class="course-item">
                            <div class="course-info">
                                <h3><?php echo htmlspecialchars($course['nazwa']); ?></h3>
                                <p>Kategoria: <?php echo htmlspecialchars($course['kategoria']); ?></p>
                                <p>Cena: <?php echo number_format($course['cena'], 2); ?> PLN</p>
                            </div>
                            <div class="course-actions">
                                <button class="btn-edit" onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)">Edytuj</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten kurs?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" class="btn-delete">Usuń</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="admin-section">
                <h2>Dodaj/Edytuj kurs</h2>
                <form method="POST" id="courseForm">
                    <input type="hidden" name="action" value="add" id="formAction">
                    <input type="hidden" name="id" value="" id="courseId">
                    
                    <div class="form-group">
                        <label for="nazwa">Nazwa kursu</label>
                        <input type="text" id="nazwa" name="nazwa" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="kategoria">Kategoria</label>
                        <select id="kategoria" name="kategoria" required>
                            <option value="A">Kategoria A</option>
                            <option value="B">Kategoria B</option>
                            <option value="C">Kategoria C</option>
                            <option value="D">Kategoria D</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cena">Cena (PLN)</label>
                        <input type="number" id="cena" name="cena" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <textarea id="opis" name="opis" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Zapisz kurs</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editCourse(course) {
            document.getElementById('formAction').value = 'edit';
            document.getElementById('courseId').value = course.id;
            document.getElementById('nazwa').value = course.nazwa;
            document.getElementById('kategoria').value = course.kategoria;
            document.getElementById('cena').value = course.cena;
            document.getElementById('opis').value = course.opis;
            
            // Scroll to form
            document.querySelector('.admin-section:last-child').scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form when clicking "Add new" button
        document.querySelector('.btn-submit').addEventListener('click', function(e) {
            if (document.getElementById('formAction').value === 'add') {
                document.getElementById('courseForm').reset();
            }
        });
    </script>
</body>
</html> 