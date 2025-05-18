<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if examination type is specified
if (!isset($_GET['typ'])) {
    header("Location: badania.php");
    exit();
}

$typ = $_GET['typ'];
$kwota = 200.00; // Default price for basic examination

// Set examination price based on type
switch ($typ) {
    case 'podstawowe':
        $kwota = 200.00;
        $typ_badania = 'Podstawowe';
        break;
    case 'zawodowe':
        $kwota = 350.00;
        $typ_badania = 'Zawodowe';
        break;
    case 'instruktorskie':
        $kwota = 450.00;
        $typ_badania = 'Instruktorskie';
        break;
    default:
        header("Location: badania.php");
        exit();
}

// Get available dates (next 30 days, excluding weekends)
$available_dates = array();
$start_date = new DateTime();
$end_date = new DateTime('+30 days');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($start_date, $interval, $end_date);

foreach ($date_range as $date) {
    // Skip weekends
    if ($date->format('N') >= 6) continue;
    $available_dates[] = $date->format('Y-m-d');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_date = $_POST['selected_date'];
    $selected_time = $_POST['selected_time'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Add examination record
        $stmt = $conn->prepare("INSERT INTO badania (uzytkownik_id, typ, data_badania, status, kwota) VALUES (?, ?, ?, 'Zaplanowane', ?)");
        $data_badania = $selected_date . ' ' . $selected_time;
        $stmt->bind_param("issd", $user_id, $typ_badania, $data_badania, $kwota);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas zapisywania badania: " . $stmt->error);
        }
        $badanie_id = $stmt->insert_id;
        
        // Add payment record
        $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, badanie_id, kwota, status, opis) VALUES (?, ?, ?, 'Oczekujący', ?)");
        $opis = "Opłata za badanie " . strtolower($typ_badania);
        $stmt->bind_param("iids", $user_id, $badanie_id, $kwota, $opis);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas tworzenia płatności: " . $stmt->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Badanie zostało zaplanowane. Przejdź do sekcji 'Opłaty' aby uregulować należność.";
        header("Location: dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umów Badanie - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="calendar">
            <h2>Umów Badanie Lekarskie</h2>
            
            <div class="examination-info">
                <h3>Szczegóły badania:</h3>
                <p>Typ badania: <?php echo $typ_badania; ?></p>
                <p>Koszt: <?php echo number_format($kwota, 2); ?> PLN</p>
            </div>

            <p>Wybierz dogodny termin badania:</p>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="date">Data:</label>
                    <select name="selected_date" id="date" required>
                        <?php foreach ($available_dates as $date): ?>
                            <option value="<?php echo $date; ?>">
                                <?php echo date('d.m.Y', strtotime($date)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="time">Godzina:</label>
                    <select name="selected_time" id="time" required>
                        <?php
                        for ($hour = 8; $hour <= 16; $hour++) {
                            for ($minute = 0; $minute < 60; $minute += 30) {
                                $time = sprintf("%02d:%02d:00", $hour, $minute);
                                echo "<option value=\"$time\">$time</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn">Zarezerwuj termin</button>
            </form>
        </div>
    </main>
</body>
</html> 