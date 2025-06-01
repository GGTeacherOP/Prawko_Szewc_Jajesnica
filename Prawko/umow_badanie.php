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
        $stmt = $conn->prepare("INSERT INTO badania (uzytkownik_id, data_badania, wynik, waznosc_do) VALUES (?, ?, 'Pozytywny', DATE_ADD(?, INTERVAL 1 YEAR))");
        if ($stmt === false) {
            throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
        }
        $data_badania = $selected_date;
        $stmt->bind_param("iss", $user_id, $data_badania, $data_badania);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas zapisywania badania: " . $stmt->error);
        }
        $badanie_id = $stmt->insert_id;
        
        // Add payment record
        $stmt = $conn->prepare("INSERT INTO platnosci (uzytkownik_id, kwota, status) VALUES (?, ?, 'Oczekujący')");
        if ($stmt === false) {
            throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
        }
        $stmt->bind_param("id", $user_id, $kwota);
        
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
    <style>
        .calendar {
            max-width: 800px;
            margin: 120px auto 50px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .calendar h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .examination-info {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .examination-info h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .examination-info p {
            margin: 0.5rem 0;
            color: #666;
        }

        .date-time-selection {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .date-picker, .time-picker {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 8px;
        }

        .date-picker h3, .time-picker h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.2em;
        }

        .date-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .date-grid .weekday {
            text-align: center;
            font-weight: bold;
            color: var(--primary-color);
            padding: 0.5rem;
        }

        .date-grid .date {
            text-align: center;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .date-grid .date:hover {
            background: var(--primary-color);
            color: white;
        }

        .date-grid .date.selected {
            background: var(--primary-color);
            color: white;
        }

        .date-grid .date.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.75rem;
            text-align: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .time-slot:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .submit-section {
            text-align: center;
            margin-top: 2rem;
        }

        .submit-section .btn {
            padding: 1rem 2rem;
            font-size: 1.1em;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-section .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .calendar {
                margin: 100px 1rem 2rem;
                padding: 1rem;
            }

            .date-time-selection {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .time-slots {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
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

            <form method="POST" action="" id="bookingForm">
                <div class="date-time-selection">
                    <div class="date-picker">
                        <h3>Wybierz datę</h3>
                        <div class="date-grid">
                            <div class="weekday">Pn</div>
                            <div class="weekday">Wt</div>
                            <div class="weekday">Śr</div>
                            <div class="weekday">Cz</div>
                            <div class="weekday">Pt</div>
                            <div class="weekday">So</div>
                            <div class="weekday">Nd</div>
                            <?php
                            $current_month = date('m');
                            $current_year = date('Y');
                            $first_day = new DateTime("$current_year-$current_month-01");
                            $last_day = new DateTime("$current_year-$current_month-" . $first_day->format('t'));
                            
                            // Add empty cells for days before the first day of the month
                            $first_day_of_week = $first_day->format('N');
                            for ($i = 1; $i < $first_day_of_week; $i++) {
                                echo '<div class="date disabled"></div>';
                            }
                            
                            // Add days of the month
                            $current_date = new DateTime();
                            $end_date = new DateTime('+30 days');
                            
                            for ($day = 1; $day <= $last_day->format('d'); $day++) {
                                $date = new DateTime("$current_year-$current_month-$day");
                                $is_available = in_array($date->format('Y-m-d'), $available_dates);
                                $is_selected = isset($_POST['selected_date']) && $_POST['selected_date'] === $date->format('Y-m-d');
                                
                                if ($is_available) {
                                    echo '<div class="date' . ($is_selected ? ' selected' : '') . '" data-date="' . $date->format('Y-m-d') . '">' . $day . '</div>';
                                } else {
                                    echo '<div class="date disabled">' . $day . '</div>';
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" name="selected_date" id="selected_date" required>
                    </div>

                    <div class="time-picker">
                        <h3>Wybierz godzinę</h3>
                        <div class="time-slots">
                            <?php
                            for ($hour = 8; $hour <= 16; $hour++) {
                                for ($minute = 0; $minute < 60; $minute += 30) {
                                    $time = sprintf("%02d:%02d", $hour, $minute);
                                    $is_selected = isset($_POST['selected_time']) && $_POST['selected_time'] === $time . ':00';
                                    echo '<div class="time-slot' . ($is_selected ? ' selected' : '') . '" data-time="' . $time . ':00">' . $time . '</div>';
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" name="selected_time" id="selected_time" required>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="btn">Zarezerwuj termin</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateGrid = document.querySelector('.date-grid');
            const timeSlots = document.querySelector('.time-slots');
            const selectedDateInput = document.getElementById('selected_date');
            const selectedTimeInput = document.getElementById('selected_time');
            const form = document.getElementById('bookingForm');

            // Handle date selection
            dateGrid.addEventListener('click', function(e) {
                if (e.target.classList.contains('date') && !e.target.classList.contains('disabled')) {
                    // Remove selected class from all dates
                    document.querySelectorAll('.date').forEach(date => date.classList.remove('selected'));
                    // Add selected class to clicked date
                    e.target.classList.add('selected');
                    // Update hidden input
                    selectedDateInput.value = e.target.dataset.date;
                }
            });

            // Handle time selection
            timeSlots.addEventListener('click', function(e) {
                if (e.target.classList.contains('time-slot')) {
                    // Remove selected class from all time slots
                    document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));
                    // Add selected class to clicked time slot
                    e.target.classList.add('selected');
                    // Update hidden input
                    selectedTimeInput.value = e.target.dataset.time;
                }
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                if (!selectedDateInput.value || !selectedTimeInput.value) {
                    e.preventDefault();
                    alert('Proszę wybrać datę i godzinę badania.');
                }
            });
        });
    </script>
</body>
</html> 