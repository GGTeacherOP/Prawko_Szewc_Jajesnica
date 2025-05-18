<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako instruktor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];

// Sprawdź, czy podano ID kursanta
if (!isset($_GET['kursant_id'])) {
    header("Location: moi_kursanci.php");
    exit();
}

$kursant_id = $_GET['kursant_id'];

// Pobierz dane kursanta
$kursant_query = "SELECT * FROM uzytkownicy WHERE id = ?";
$stmt = $conn->prepare($kursant_query);
$stmt->bind_param("i", $kursant_id);
$stmt->execute();
$kursant_result = $stmt->get_result();
$kursant = $kursant_result->fetch_assoc();

if (!$kursant) {
    header("Location: moi_kursanci.php");
    exit();
}

// Pobierz historię jazd
$jazdy_query = "SELECT j.*, k.nazwa as kurs_nazwa, k.kategoria as kurs_kategoria
                FROM jazdy j
                LEFT JOIN kursy k ON j.kurs_id = k.id
                WHERE j.kursant_id = ? AND j.instruktor_id = ?
                ORDER BY j.data_jazdy DESC";
$stmt = $conn->prepare($jazdy_query);
$stmt->bind_param("ii", $kursant_id, $instruktor_id);
$stmt->execute();
$jazdy_result = $stmt->get_result();

// Pobierz statystyki
$stats_query = "SELECT 
                COUNT(*) as total_jazdy,
                SUM(CASE WHEN status = 'Zakończona' THEN 1 ELSE 0 END) as completed_jazdy,
                SUM(CASE WHEN status = 'Anulowana' THEN 1 ELSE 0 END) as cancelled_jazdy,
                AVG(CASE WHEN status = 'Zakończona' AND ocena IS NOT NULL THEN ocena ELSE NULL END) as avg_ocena
                FROM jazdy
                WHERE kursant_id = ? AND instruktor_id = ?";
$stmt = $conn->prepare($stats_query);
$stmt->bind_param("ii", $kursant_id, $instruktor_id);
$stmt->execute();
$stats_result = $stmt->get_result();
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Jazd - <?php echo htmlspecialchars($kursant['imie'] . ' ' . $kursant['nazwisko']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .historia-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .kursant-info {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 0.5rem;
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

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .status-badge.zaplanowana {
            background-color: #ffc107;
            color: #000;
        }

        .status-badge.zakonczona {
            background-color: #28a745;
            color: white;
        }

        .status-badge.anulowana {
            background-color: #dc3545;
            color: white;
        }

        .ocena {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .star {
            color: #ffc107;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="historia-container">
        <h1>Historia Jazd - <?php echo htmlspecialchars($kursant['imie'] . ' ' . $kursant['nazwisko']); ?></h1>

        <div class="kursant-info">
            <h2>Dane Kursanta</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($kursant['email']); ?></p>
            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($kursant['telefon']); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['total_jazdy']; ?></div>
                <div>Wszystkie Jazdy</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['completed_jazdy']; ?></div>
                <div>Ukończone Jazdy</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['cancelled_jazdy']; ?></div>
                <div>Anulowane Jazdy</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($stats['avg_ocena'], 1); ?></div>
                <div>Średnia Ocena</div>
            </div>
        </div>

        <h2>Lista Jazd</h2>
        <?php if($jazdy_result->num_rows > 0): ?>
            <?php while($jazda = $jazdy_result->fetch_assoc()): ?>
                <div class="jazda-card">
                    <div class="jazda-header">
                        <h3><?php echo htmlspecialchars($jazda['kurs_nazwa']); ?> - <?php echo htmlspecialchars($jazda['kurs_kategoria']); ?></h3>
                        <span class="status-badge <?php echo strtolower($jazda['status']); ?>">
                            <?php echo htmlspecialchars($jazda['status']); ?>
                        </span>
                    </div>
                    <div class="jazda-details">
                        <p><strong>Data i godzina:</strong> <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></p>
                        <?php if($jazda['status'] === 'Zakończona' && $jazda['ocena']): ?>
                            <div class="ocena">
                                <strong>Ocena:</strong>
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="star"><?php echo $i <= $jazda['ocena'] ? '★' : '☆'; ?></span>
                                <?php endfor; ?>
                            </div>
                            <?php if($jazda['komentarz']): ?>
                                <p><strong>Komentarz:</strong> <?php echo htmlspecialchars($jazda['komentarz']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Brak historii jazd dla tego kursanta.</p>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html> 