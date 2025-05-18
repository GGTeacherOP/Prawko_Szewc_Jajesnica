<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany jako instruktor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];

// Pobierz zaplanowane jazdy
$query = "SELECT j.*, 
          u.imie as kursant_imie, 
          u.nazwisko as kursant_nazwisko,
          u.telefon as kursant_telefon,
          k.nazwa as kurs_nazwa,
          k.kategoria as kurs_kategoria
          FROM jazdy j
          LEFT JOIN uzytkownicy u ON j.kursant_id = u.id
          LEFT JOIN kursy k ON j.kurs_id = k.id
          WHERE j.instruktor_id = ?
          ORDER BY j.data_jazdy ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instruktor_id);
$stmt->execute();
$result = $stmt->get_result();

// Pobierz dostępność instruktora
$availability_query = "SELECT * FROM dostepnosc_instruktorow 
                      WHERE instruktor_id = ? 
                      AND data >= CURDATE()
                      ORDER BY data ASC, godzina_od ASC";
$stmt_availability = $conn->prepare($availability_query);
$stmt_availability->bind_param("i", $instruktor_id);
$stmt_availability->execute();
$availability_result = $stmt_availability->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmonogram Jazd - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .harmonogram-container {
            max-width: 1200px;
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

        .availability-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .availability-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .availability-card {
            background-color: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .no-jazdy {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab {
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .tab.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="harmonogram-container">
        <h1>Harmonogram Jazd</h1>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert <?php echo $_SESSION['message_type']; ?>">
                <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab active" onclick="showTab('jazdy')">Zaplanowane Jazdy</button>
            <button class="tab" onclick="showTab('dostepnosc')">Moja Dostępność</button>
        </div>

        <div id="jazdy" class="tab-content">
            <?php if($result->num_rows > 0): ?>
                <?php while($jazda = $result->fetch_assoc()): ?>
                    <div class="jazda-card">
                        <div class="jazda-header">
                            <h3><?php echo htmlspecialchars($jazda['kurs_nazwa']); ?> - <?php echo htmlspecialchars($jazda['kurs_kategoria']); ?></h3>
                            <div class="action-buttons">
                                <?php if(strtotime($jazda['data_jazdy']) > time()): ?>
                                    <a href="anuluj_jazde_instruktor.php?id=<?php echo $jazda['id']; ?>" class="btn-cancel" 
                                       onclick="return confirm('Czy na pewno chcesz anulować tę jazdę?')">
                                        Anuluj Jazdę
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="jazda-details">
                            <p><strong>Kursant:</strong> <?php echo htmlspecialchars($jazda['kursant_imie'] . ' ' . $jazda['kursant_nazwisko']); ?></p>
                            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($jazda['kursant_telefon']); ?></p>
                            <p><strong>Data i godzina:</strong> <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($jazda['status']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-jazdy">
                    <p>Nie masz jeszcze zaplanowanych jazd.</p>
                </div>
            <?php endif; ?>
        </div>

        <div id="dostepnosc" class="tab-content" style="display: none;">
            <h2>Moja Dostępność</h2>
            <div class="availability-grid">
                <?php if($availability_result->num_rows > 0): ?>
                    <?php while($availability = $availability_result->fetch_assoc()): ?>
                        <div class="availability-card">
                            <p><strong><?php echo date('d.m.Y', strtotime($availability['data'])); ?></strong></p>
                            <p><?php echo substr($availability['godzina_od'], 0, 5); ?> - <?php echo substr($availability['godzina_do'], 0, 5); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nie masz jeszcze ustawionej dostępności.</p>
                <?php endif; ?>
            </div>
            <div style="text-align: center; margin-top: 2rem;">
                <a href="ustaw_dostepnosc.php" class="btn">Ustaw Dostępność</a>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ukryj wszystkie zakładki
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Pokaż wybraną zakładkę
            document.getElementById(tabName).style.display = 'block';
            
            // Aktualizuj style przycisków
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>

    <script src="script.js"></script>
</body>
</html> 