<?php
session_start();

// Check if user is logged in and is an instructor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'instruktor') {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Debug
error_log("Instructor panel accessed by: " . print_r($_SESSION, true));

// Get instructor details
$instructor_id = $_SESSION['user_id'];
$get_instructor_query = "SELECT * FROM pracownicy WHERE id = ? AND rola = 'instruktor'";
$stmt = $conn->prepare($get_instructor_query);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();
$stmt->close();

// Pobierz zaplanowane jazdy dla instruktora
$stmt = $conn->prepare("
    SELECT j.*, 
           u.imie as kursant_imie, u.nazwisko as kursant_nazwisko
    FROM jazdy j
    JOIN uzytkownicy u ON j.kursant_id = u.id
    WHERE j.instruktor_id = ? AND j.status = 'Zaplanowana'
    ORDER BY j.data_jazdy
");
$stmt->bind_param("i", $instructor_id);
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
            margin: 20px auto;
            padding: 20px;
        }

        .welcome-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    <?php include 'header.php'; ?>

    <main>
        <div class="instructor-container">
            <div class="welcome-section">
                <h1>Witaj, <?php echo htmlspecialchars($instructor['imie'] . ' ' . $instructor['nazwisko']); ?>!</h1>
                <p>Panel instruktora - zarządzaj swoimi kursami i kursantami</p>
            </div>

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

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Twoje uprawnienia</h3>
                    <p><?php echo htmlspecialchars($instructor['kategorie_uprawnien']); ?></p>
                </div>
                <!-- Dodaj więcej kart statystyk według potrzeb -->
            </div>

            <h2>Zaplanowane Jazdy</h2>
            <?php if($jazdy->num_rows > 0): ?>
                <?php while($jazda = $jazdy->fetch_assoc()): ?>
                    <div class="lesson-card">
                        <h3>Jazda <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></h3>
                        <p><strong>Kursant:</strong> <?php echo htmlspecialchars($jazda['kursant_imie'] . ' ' . $jazda['kursant_nazwisko']); ?></p>
                        <p><strong>Data i godzina:</strong> <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></p>
                        
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
                                    <label for="nowa_data_<?php echo $jazda['id']; ?>">Nowa data i godzina:</label>
                                    <input type="datetime-local" id="nowa_data_<?php echo $jazda['id']; ?>" name="nowa_data" 
                                           required min="<?php echo date('Y-m-d\TH:i'); ?>">
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