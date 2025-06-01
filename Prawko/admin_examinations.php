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
                $data = $_POST['data'];
                $godzina = $_POST['godzina'];
                $miejsce = $_POST['miejsce'];
                $max_osob = $_POST['max_osob'];
                
                $stmt = $conn->prepare("INSERT INTO badania (data, godzina, miejsce, max_osob) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $data, $godzina, $miejsce, $max_osob);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $data = $_POST['data'];
                $godzina = $_POST['godzina'];
                $miejsce = $_POST['miejsce'];
                $max_osob = $_POST['max_osob'];
                
                $stmt = $conn->prepare("UPDATE badania SET data = ?, godzina = ?, miejsce = ?, max_osob = ? WHERE id = ?");
                $stmt->bind_param("sssii", $data, $godzina, $miejsce, $max_osob, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM badania WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Get all examinations with user information
$result = $conn->query("
    SELECT b.*, 
           COALESCE(COUNT(z.id), 0) as zapisane_osoby,
           GROUP_CONCAT(CONCAT(u.imie, ' ', u.nazwisko) SEPARATOR ', ') as uczestnicy
    FROM badania b
    LEFT JOIN zapisy_badan z ON b.id = z.badanie_id
    LEFT JOIN uzytkownicy u ON z.uzytkownik_id = u.id
    GROUP BY b.id
    ORDER BY b.data DESC, b.godzina DESC
");
$examinations = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie badaniami - Panel Administratora</title>
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

        .examination-list {
            list-style: none;
            padding: 0;
        }

        .examination-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .examination-item:last-child {
            border-bottom: none;
        }

        .examination-info {
            flex: 1;
        }

        .examination-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .examination-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .examination-actions {
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

        .status-full {
            color: #dc3545;
            font-weight: bold;
        }

        .status-available {
            color: #28a745;
            font-weight: bold;
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
            <h1>Zarządzanie badaniami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Lista badań</h2>
                <ul class="examination-list">
                    <?php foreach ($examinations as $exam): ?>
                        <li class="examination-item">
                            <div class="examination-info">
                                <h3>
                                    Badanie lekarskie
                                    <span class="<?php echo $exam['zapisane_osoby'] >= $exam['max_osob'] ? 'status-full' : 'status-available'; ?>">
                                        (<?php echo $exam['zapisane_osoby']; ?>/<?php echo $exam['max_osob']; ?> osób)
                                    </span>
                                </h3>
                                <p>Data: <?php echo date('d.m.Y', strtotime($exam['data'])); ?></p>
                                <p>Godzina: <?php echo $exam['godzina']; ?></p>
                                <p>Miejsce: <?php echo htmlspecialchars($exam['miejsce']); ?></p>
                                <?php if ($exam['uczestnicy']): ?>
                                    <p>Zapisani: <?php echo htmlspecialchars($exam['uczestnicy']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="examination-actions">
                                <button class="btn-edit" onclick="editExamination(<?php echo htmlspecialchars(json_encode($exam)); ?>)">Edytuj</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć to badanie?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
                                    <button type="submit" class="btn-delete">Usuń</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="admin-section">
                <h2>Dodaj/Edytuj badanie</h2>
                <form method="POST" id="examinationForm">
                    <input type="hidden" name="action" value="add" id="formAction">
                    <input type="hidden" name="id" value="" id="examinationId">
                    
                    <div class="form-group">
                        <label for="data">Data</label>
                        <input type="date" id="data" name="data" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="godzina">Godzina</label>
                        <input type="time" id="godzina" name="godzina" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="miejsce">Miejsce</label>
                        <input type="text" id="miejsce" name="miejsce" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_osob">Maksymalna liczba osób</label>
                        <input type="number" id="max_osob" name="max_osob" min="1" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Zapisz badanie</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editExamination(exam) {
            document.getElementById('formAction').value = 'edit';
            document.getElementById('examinationId').value = exam.id;
            document.getElementById('data').value = exam.data;
            document.getElementById('godzina').value = exam.godzina;
            document.getElementById('miejsce').value = exam.miejsce;
            document.getElementById('max_osob').value = exam.max_osob;
            
            // Scroll to form
            document.querySelector('.admin-section:last-child').scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form when clicking "Add new" button
        document.querySelector('.btn-submit').addEventListener('click', function(e) {
            if (document.getElementById('formAction').value === 'add') {
                document.getElementById('examinationForm').reset();
            }
        });
    </script>
</body>
</html> 