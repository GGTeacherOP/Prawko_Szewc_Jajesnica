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

        // Create the examination record
        $stmt = $conn->prepare("INSERT INTO badania (uzytkownik_id, data_badania, typ, status, wynik, waznosc_do) 
                               VALUES (?, ?, ?, 'Oczekujący', 'Oczekujący', DATE_ADD(?, INTERVAL 2 YEAR))");
        $examination_date = $selected_date . ' ' . $selected_time;
        $stmt->bind_param("isss", $user_id, $examination_date, $typ_badania, $selected_date);
        $stmt->execute();
        
        // Create payment record for examination
        $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kwota, status, opis) VALUES (?, ?, 'Oczekujący', ?)");
        $opis = "Badanie lekarskie - " . $typ_badania;
        $stmt->bind_param("ids", $user_id, $kwota, $opis);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Badanie zostało umówione. Przejdź do sekcji płatności, aby uregulować należność.";
        header("Location: oplaty.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Wystąpił błąd podczas umawiania badania: " . $e->getMessage();
        header("Location: badania.php");
        exit();
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
    <style>
        .calendar {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dates-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin: 2rem 0;
        }

        .date-cell {
            padding: 1rem;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .date-cell:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .date-cell.selected {
            background-color: var(--primary-color);
            color: white;
        }

        .date-cell.disabled {
            background-color: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 2rem 0;
        }

        .time-slot {
            padding: 1rem;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .time-slot:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .time-slot.selected {
            background-color: var(--primary-color);
            color: white;
        }

        .examination-info {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header class="scroll-up">
        <nav>
            <div class="logo">
                <img src="logo.png" alt="Linia Nauka Jazdy Logo">
            </div>
            <ul>
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="kurs_prawa_jazdy.php">Kurs Prawa Jazdy</a></li>
                <li><a href="kurs_instruktorow.php">Kursy dla Instruktorów</a></li>
                <li><a href="kurs_kierowcow.php">Kursy Kierowców Zawodowych</a></li>
                <li><a href="kurs_operatorow.php">Kursy Operatorów Maszyn</a></li>
                <li><a href="badania.php" class="active">Badania</a></li>
                <li><a href="oplaty.php">Opłaty</a></li>
                <li><a href="kontakt.php">Kontakt</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Panel</a></li>
                    <li><a href="logout.php">Wyloguj</a></li>
                <?php else: ?>
                    <li><a href="login.php">Zaloguj</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="calendar">
            <h2>Umów Badanie Lekarskie</h2>
            
            <div class="examination-info">
                <h3>Szczegóły badania:</h3>
                <p>Typ badania: <?php echo $typ_badania; ?></p>
                <p>Koszt: <?php echo number_format($kwota, 2); ?> PLN</p>
            </div>

            <p>Wybierz dogodny termin badania:</p>

            <form method="POST" id="examination-form">
                <div class="dates-grid">
                    <?php
                    foreach ($available_dates as $date) {
                        $date_obj = new DateTime($date);
                        $formatted_date = $date_obj->format('d.m.Y');
                        echo "<div class='date-cell' data-date='$date'>$formatted_date</div>";
                    }
                    ?>
                </div>

                <h3>Dostępne godziny:</h3>
                <div class="time-slots">
                    <?php
                    $time_slots = array('09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00');
                    foreach ($time_slots as $time) {
                        echo "<div class='time-slot' data-time='$time'>$time</div>";
                    }
                    ?>
                </div>

                <input type="hidden" name="selected_date" id="selected_date">
                <input type="hidden" name="selected_time" id="selected_time">
                <button type="submit" class="btn primary" disabled id="submit-btn">Umów badanie</button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateCells = document.querySelectorAll('.date-cell');
            const timeSlots = document.querySelectorAll('.time-slot');
            const form = document.getElementById('examination-form');
            const submitBtn = document.getElementById('submit-btn');
            const selectedDateInput = document.getElementById('selected_date');
            const selectedTimeInput = document.getElementById('selected_time');

            let selectedDate = null;
            let selectedTime = null;

            dateCells.forEach(cell => {
                cell.addEventListener('click', function() {
                    dateCells.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedDate = this.dataset.date;
                    selectedDateInput.value = selectedDate;
                    checkFormValidity();
                });
            });

            timeSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    timeSlots.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedTime = this.dataset.time;
                    selectedTimeInput.value = selectedTime;
                    checkFormValidity();
                });
            });

            function checkFormValidity() {
                if (selectedDate && selectedTime) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
        });
    </script>
</body>
</html> 