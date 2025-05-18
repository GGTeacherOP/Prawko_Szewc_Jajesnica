<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako instruktor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST['data'];
    $godzina_od = $_POST['godzina_od'];
    $godzina_do = $_POST['godzina_do'];
    
    // Sprawdź, czy data nie jest z przeszłości
    if (strtotime($data) < strtotime(date('Y-m-d'))) {
        $message = "Nie można ustawić dostępności dla daty z przeszłości.";
        $message_type = "error";
    }
    // Sprawdź, czy godzina zakończenia jest późniejsza niż godzina rozpoczęcia
    elseif (strtotime($godzina_do) <= strtotime($godzina_od)) {
        $message = "Godzina zakończenia musi być późniejsza niż godzina rozpoczęcia.";
        $message_type = "error";
    }
    else {
        // Sprawdź, czy nie ma konfliktu z istniejącymi jazdami
        $check_query = "SELECT 1 FROM jazdy 
                       WHERE instruktor_id = ? 
                       AND data_jazdy BETWEEN ? AND ?
                       AND status = 'Zaplanowana'
                       LIMIT 1";
        $check_stmt = $conn->prepare($check_query);
        $data_od = $data . ' ' . $godzina_od;
        $data_do = $data . ' ' . $godzina_do;
        $check_stmt->bind_param("iss", $instruktor_id, $data_od, $data_do);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = "Nie można ustawić dostępności - masz zaplanowane jazdy w tym terminie.";
            $message_type = "error";
        } else {
            // Dodaj dostępność
            $insert_query = "INSERT INTO dostepnosc_instruktorow (instruktor_id, data, godzina_od, godzina_do) 
                           VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("isss", $instruktor_id, $data, $godzina_od, $godzina_do);
            
            if ($insert_stmt->execute()) {
                $message = "Dostępność została dodana pomyślnie.";
                $message_type = "success";
            } else {
                $message = "Wystąpił błąd podczas dodawania dostępności.";
                $message_type = "error";
            }
        }
    }
}

// Pobierz aktualną dostępność
$availability_query = "SELECT * FROM dostepnosc_instruktorow 
                      WHERE instruktor_id = ? 
                      AND data >= CURDATE()
                      ORDER BY data ASC, godzina_od ASC";
$stmt = $conn->prepare($availability_query);
$stmt->bind_param("i", $instruktor_id);
$stmt->execute();
$availability_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustaw Dostępność - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dostepnosc-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .availability-list {
            margin-top: 2rem;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
        }

        .availability-item {
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="dostepnosc-container">
        <h1>Ustaw Dostępność</h1>

        <?php if($message): ?>
            <div class="alert <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="godzina_od">Godzina rozpoczęcia:</label>
                <input type="time" id="godzina_od" name="godzina_od" required>
            </div>

            <div class="form-group">
                <label for="godzina_do">Godzina zakończenia:</label>
                <input type="time" id="godzina_do" name="godzina_do" required>
            </div>

            <button type="submit" class="btn">Dodaj Dostępność</button>
        </form>

        <div class="availability-list">
            <h2>Twoja Dostępność</h2>
            <?php if($availability_result->num_rows > 0): ?>
                <?php while($availability = $availability_result->fetch_assoc()): ?>
                    <div class="availability-item">
                        <div>
                            <strong><?php echo date('d.m.Y', strtotime($availability['data'])); ?></strong>
                            <?php echo substr($availability['godzina_od'], 0, 5); ?> - <?php echo substr($availability['godzina_do'], 0, 5); ?>
                        </div>
                        <a href="usun_dostepnosc.php?id=<?php echo $availability['id']; ?>" 
                           class="btn-delete"
                           onclick="return confirm('Czy na pewno chcesz usunąć tę dostępność?')">
                            Usuń
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nie masz jeszcze ustawionej dostępności.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 