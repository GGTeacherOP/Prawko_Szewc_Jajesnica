<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako instruktor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];

// Pobierz kursantów przypisanych do instruktora
$query = "SELECT DISTINCT 
          u.id as kursant_id,
          u.imie,
          u.nazwisko,
          u.telefon,
          u.email,
          k.nazwa as kurs_nazwa,
          k.kategoria as kurs_kategoria,
          (SELECT COUNT(*) FROM jazdy j2 
           WHERE j2.kursant_id = u.id 
           AND j2.instruktor_id = ? 
           AND j2.status = 'Zakończona') as completed_lessons,
          (SELECT COUNT(*) FROM jazdy j3 
           WHERE j3.kursant_id = u.id 
           AND j3.instruktor_id = ? 
           AND j3.status = 'Zaplanowana') as planned_lessons
          FROM jazdy j
          JOIN uzytkownicy u ON j.kursant_id = u.id
          JOIN kursy k ON j.kurs_id = k.id
          WHERE j.instruktor_id = ?
          GROUP BY u.id, k.id
          ORDER BY u.nazwisko, u.imie";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $instruktor_id, $instruktor_id, $instruktor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moi Kursanci - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .kursanci-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .kursant-card {
            background-color: #f8f9fa;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }

        .kursant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .kursant-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .progress-section {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }

        .progress-bar {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background-color: #28a745;
            width: 0;
            transition: width 0.3s ease;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }

        .stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            padding: 1rem;
            background-color: #e9ecef;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
        }

        .no-kursanci {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="kursanci-container">
        <h1>Moi Kursanci</h1>

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
            <?php while($kursant = $result->fetch_assoc()): ?>
                <div class="kursant-card">
                    <div class="kursant-header">
                        <h3><?php echo htmlspecialchars($kursant['imie'] . ' ' . $kursant['nazwisko']); ?></h3>
                        <div class="action-buttons">
                            <a href="historia_jazd.php?kursant_id=<?php echo $kursant['kursant_id']; ?>" class="btn-view">
                                Historia Jazd
                            </a>
                        </div>
                    </div>
                    <div class="kursant-details">
                        <div>
                            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($kursant['telefon']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($kursant['email']); ?></p>
                        </div>
                        <div>
                            <p><strong>Kurs:</strong> <?php echo htmlspecialchars($kursant['kurs_nazwa']); ?></p>
                            <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($kursant['kurs_kategoria']); ?></p>
                        </div>
                    </div>
                    <div class="stats">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $kursant['completed_lessons']; ?></div>
                            <div>Ukończone Jazdy</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $kursant['planned_lessons']; ?></div>
                            <div>Zaplanowane Jazdy</div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-kursanci">
                <p>Nie masz jeszcze przypisanych kursantów.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html> 