<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako kursant
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'kursant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pobierz zaplanowane jazdy
$query = "SELECT j.*, 
          p.imie as instruktor_imie, 
          p.nazwisko as instruktor_nazwisko,
          k.nazwa as kurs_nazwa,
          k.kategoria as kurs_kategoria
          FROM jazdy j
          LEFT JOIN pracownicy p ON j.instruktor_id = p.id
          LEFT JOIN kursy k ON j.kurs_id = k.id
          WHERE j.kursant_id = ?
          ORDER BY j.data_jazdy ASC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Błąd SQL: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje Jazdy - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .jazdy-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .jazda-card {
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .jazda-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .jazda-details {
            margin-top: 0.5rem;
            font-size: 0.9em;
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }

        .no-jazdy {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="jazdy-container">
        <h1>Moje Jazdy</h1>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <?php if($result->num_rows > 0): ?>
            <?php while($jazda = $result->fetch_assoc()): ?>
                <div class="jazda-card">
                    <div class="jazda-header">
                        <h3><?php echo htmlspecialchars($jazda['kurs_nazwa']); ?> - <?php echo htmlspecialchars($jazda['kurs_kategoria']); ?></h3>
                        <div class="action-buttons">
                            <?php if(strtotime($jazda['data_jazdy']) > time() && $jazda['status'] !== 'Anulowana'): ?>
                                <a href="anuluj_jazde.php?id=<?php echo $jazda['id']; ?>" class="btn-cancel" 
                                   onclick="return confirm('Czy na pewno chcesz anulować tę jazdę?')">
                                    Anuluj Jazdę
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="jazda-details">
                        <p><strong>Instruktor:</strong> <?php echo htmlspecialchars($jazda['instruktor_imie'] . ' ' . $jazda['instruktor_nazwisko']); ?></p>
                        <p><strong>Data i godzina:</strong> <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($jazda['status']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-jazdy">
                <p>Nie masz jeszcze zaplanowanych jazd.</p>
                <a href="planowanie_jazd.php" class="btn">Zaplanuj Jazdę</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html> 