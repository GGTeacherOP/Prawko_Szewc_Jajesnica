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
                $data_badania = $_POST['data_badania'];
                $typ = $_POST['typ'];
                $wynik = $_POST['wynik'];
                $status = $_POST['status'];
                $waznosc_do = $_POST['waznosc_do'];
                
                $stmt = $conn->prepare("INSERT INTO badania (data_badania, typ, wynik, status, waznosc_do) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $data_badania, $typ, $wynik, $status, $waznosc_do);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $data_badania = $_POST['data_badania'];
                $typ = $_POST['typ'];
                $wynik = $_POST['wynik'];
                $status = $_POST['status'];
                $waznosc_do = $_POST['waznosc_do'];
                
                $stmt = $conn->prepare("UPDATE badania SET data_badania = ?, typ = ?, wynik = ?, status = ?, waznosc_do = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $data_badania, $typ, $wynik, $status, $waznosc_do, $id);
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
           u.imie,
           u.nazwisko
    FROM badania b
    LEFT JOIN uzytkownicy u ON b.uzytkownik_id = u.id
    ORDER BY b.data_badania DESC
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
                    <?php if (empty($examinations)): ?>
                        <li class="examination-item">
                            <p>Brak dostępnych badań.</p>
                        </li>
                    <?php else: ?>
                        <?php foreach ($examinations as $exam): ?>
                            <li class="examination-item">
                                <div class="examination-info">
                                    <h3>
                                        Badanie <?php echo htmlspecialchars($exam['typ']); ?>
                                        <?php if ($exam['imie'] && $exam['nazwisko']): ?>
                                            - <?php echo htmlspecialchars($exam['imie'] . ' ' . $exam['nazwisko']); ?>
                                        <?php endif; ?>
                                    </h3>
                                    <p>Data: <?php echo date('d.m.Y', strtotime($exam['data_badania'])); ?></p>
                                    <p>Typ: <?php echo htmlspecialchars($exam['typ']); ?></p>
                                    <p>Wynik: <?php echo htmlspecialchars($exam['wynik']); ?></p>
                                    <p>Status: <?php echo htmlspecialchars($exam['status']); ?></p>
                                    <p>Ważność do: <?php echo date('d.m.Y', strtotime($exam['waznosc_do'])); ?></p>
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
                    <?php endif; ?>
                </ul>
            </div>

            <div class="admin-section">
                <h2>Dodaj nowe badanie</h2>
                <form method="POST" id="addExaminationForm">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="data_badania">Data badania:</label>
                        <input type="date" id="data_badania" name="data_badania" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="typ">Typ badania:</label>
                        <select id="typ" name="typ" required>
                            <option value="Podstawowe">Podstawowe</option>
                            <option value="Rozszerzone">Rozszerzone</option>
                            <option value="Psychologiczne">Psychologiczne</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="wynik">Wynik:</label>
                        <select id="wynik" name="wynik" required>
                            <option value="Pozytywny">Pozytywny</option>
                            <option value="Negatywny">Negatywny</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="Oczekujący">Oczekujący</option>
                            <option value="Zatwierdzony">Zatwierdzony</option>
                            <option value="Odrzucony">Odrzucony</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="waznosc_do">Ważność do:</label>
                        <input type="date" id="waznosc_do" name="waznosc_do" required>
                    </div>

                    <button type="submit" class="btn-submit">Dodaj badanie</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function editExamination(exam) {
        // Wypełnij formularz danymi do edycji
        document.getElementById('data_badania').value = exam.data_badania;
        document.getElementById('typ').value = exam.typ;
        document.getElementById('wynik').value = exam.wynik;
        document.getElementById('status').value = exam.status;
        document.getElementById('waznosc_do').value = exam.waznosc_do;
        
        // Zmień akcję formularza na edycję
        const form = document.getElementById('addExaminationForm');
        form.querySelector('input[name="action"]').value = 'edit';
        
        // Dodaj ukryte pole z ID
        let idInput = form.querySelector('input[name="id"]');
        if (!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            form.appendChild(idInput);
        }
        idInput.value = exam.id;
        
        // Zmień tekst przycisku
        form.querySelector('.btn-submit').textContent = 'Zapisz zmiany';
        
        // Przewiń do formularza
        form.scrollIntoView({ behavior: 'smooth' });
    }

    // Reset formularza po dodaniu/edycji
    document.getElementById('addExaminationForm').addEventListener('submit', function() {
        setTimeout(() => {
            this.reset();
            this.querySelector('input[name="action"]').value = 'add';
            this.querySelector('.btn-submit').textContent = 'Dodaj badanie';
            const idInput = this.querySelector('input[name="id"]');
            if (idInput) idInput.remove();
        }, 1000);
    });
    </script>
</body>
</html> 