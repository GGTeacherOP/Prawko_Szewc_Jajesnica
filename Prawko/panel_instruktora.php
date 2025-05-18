<?php
session_start();
require_once 'config.php';

// Sprawdź czy użytkownik jest zalogowany i czy jest instruktorem
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

$instruktor_id = $_SESSION['user_id'];

// Pobierz zaplanowane jazdy dla instruktora
$stmt = $conn->prepare("
    SELECT j.*, 
           u.imie as kursant_imie, u.nazwisko as kursant_nazwisko,
           p.marka, p.model
    FROM jazdy j
    JOIN uzytkownicy u ON j.uzytkownik_id = u.id
    JOIN pojazdy p ON j.pojazd_id = p.id
    WHERE j.instruktor_id = ? AND j.status = 'Zaplanowana'
    ORDER BY j.data_jazdy, j.godzina_rozpoczecia
");
$stmt->bind_param("i", $instruktor_id);
$stmt->execute();
$jazdy = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Instruktora - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .instructor-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .lesson-card {
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .lesson-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
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
                <li><a href="panel_instruktora.php">Panel Instruktora</a></li>
                <li><a href="logout.php">Wyloguj</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="instructor-container">
            <h1>Panel Instruktora</h1>
            
            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?php 
                        echo htmlspecialchars($_SESSION['success_message']);
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div class="error-message">
                    <?php 
                        echo htmlspecialchars($_SESSION['error_message']);
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <h2>Zaplanowane Jazdy</h2>
            <?php if($jazdy->num_rows > 0): ?>
                <?php while($jazda = $jazdy->fetch_assoc()): ?>
                    <div class="lesson-card">
                        <h3>Jazda <?php echo date('d.m.Y', strtotime($jazda['data_jazdy'])); ?></h3>
                        <p><strong>Kursant:</strong> <?php echo htmlspecialchars($jazda['kursant_imie'] . ' ' . $jazda['kursant_nazwisko']); ?></p>
                        <p><strong>Godzina:</strong> <?php echo date('H:i', strtotime($jazda['godzina_rozpoczecia'])); ?></p>
                        <p><strong>Czas trwania:</strong> <?php echo $jazda['liczba_godzin']; ?> godz.</p>
                        <p><strong>Pojazd:</strong> <?php echo htmlspecialchars($jazda['marka'] . ' ' . $jazda['model']); ?></p>
                        
                        <div class="lesson-actions">
                            <form method="POST" action="anuluj_jazde_instruktor.php">
                                <input type="hidden" name="jazda_id" value="<?php echo $jazda['id']; ?>">
                                <button type="submit" class="btn danger">Anuluj jazdę</button>
                            </form>
                            <button type="button" class="btn primary" onclick="showRescheduleForm(<?php echo $jazda['id']; ?>)">Przesuń termin</button>
                        </div>

                        <div id="reschedule-form-<?php echo $jazda['id']; ?>" style="display: none; margin-top: 1rem;">
                            <form method="POST" action="przesun_jazde.php">
                                <input type="hidden" name="jazda_id" value="<?php echo $jazda['id']; ?>">
                                <div class="form-group">
                                    <label for="nowa_data_<?php echo $jazda['id']; ?>">Nowa data:</label>
                                    <input type="date" id="nowa_data_<?php echo $jazda['id']; ?>" name="nowa_data" 
                                           required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="nowa_godzina_<?php echo $jazda['id']; ?>">Nowa godzina:</label>
                                    <select id="nowa_godzina_<?php echo $jazda['id']; ?>" name="nowa_godzina" required>
                                        <?php
                                        for ($h = 8; $h <= 18; $h++) {
                                            for ($m = 0; $m < 60; $m += 30) {
                                                $time = sprintf("%02d:%02d", $h, $m);
                                                echo "<option value=\"$time\">$time</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn primary">Zatwierdź zmianę</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nie masz zaplanowanych jazd.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function showRescheduleForm(jazdaId) {
            const form = document.getElementById(`reschedule-form-${jazdaId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html> 