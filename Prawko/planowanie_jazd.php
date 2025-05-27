<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's course category and ID
$stmt = $conn->prepare("
    SELECT k.id as kurs_id, k.kategoria, k.nazwa, z.status, 
           COUNT(j.id) as liczba_jazd
    FROM zapisy z 
    JOIN kursy k ON z.kurs_id = k.id 
    LEFT JOIN jazdy j ON j.kursant_id = ? AND j.status != 'Anulowana'
    WHERE z.uzytkownik_id = ? AND z.status = 'Zatwierdzony'
    GROUP BY k.id, k.kategoria, k.nazwa, z.status
");

if ($stmt === false) {
    die("Błąd przygotowania zapytania: " . $conn->error);
}

$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$kurs = $result->fetch_assoc();

if (!$kurs) {
    $_SESSION['error_message'] = "Nie masz opłaconego kursu. Aby zaplanować jazdę, musisz najpierw opłacić kurs.";
    header("Location: dashboard.php");
    exit();
}

$kurs_id = $kurs['kurs_id'];
if (!$kurs_id) {
    $_SESSION['error_message'] = "Nie znaleziono kursu. Proszę skontaktować się z administracją.";
    header("Location: dashboard.php");
    exit();
}

$liczba_jazd = $kurs['liczba_jazd'] ?? 0;
$pozostale_godziny = 30 - $liczba_jazd;

// Get course category (for filtering instructors and vehicles)
$kategoria = 'C'; // Domyślna kategoria dla kursu zawodowego
if (strpos($kurs['nazwa'], 'Kat.') !== false) {
    // Dla kursów prawa jazdy
    $kategoria = str_replace('Kat. ', '', $kurs['nazwa']);
    $kategoria = trim(explode(' ', $kategoria)[0]);
}

// Debug information
echo "<!-- Debug info: -->\n";
echo "<!-- Course name: " . htmlspecialchars($kurs['nazwa']) . " -->\n";
echo "<!-- Course category: " . htmlspecialchars($kurs['kategoria']) . " -->\n";
echo "<!-- Extracted category: " . htmlspecialchars($kategoria) . " -->\n";
echo "<!-- Raw course data: " . print_r($kurs, true) . " -->\n";

// Handle lesson cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['anuluj_jazde'])) {
    $jazda_id = $_POST['jazda_id'];
    
    // Debug information
    error_log("Attempting to cancel lesson ID: " . $jazda_id);
    error_log("User ID: " . $user_id);
    
    // Verify that the lesson belongs to the current user
    $stmt = $conn->prepare("
        SELECT id, status 
        FROM jazdy 
        WHERE id = ? AND kursant_id = ? AND status = 'Zaplanowana'
    ");
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $_SESSION['error_message'] = "Błąd podczas przygotowania zapytania.";
        header("Location: planowanie_jazd.php");
        exit();
    }
    
    $stmt->bind_param("ii", $jazda_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $jazda = $result->fetch_assoc();
    
    error_log("Found lesson: " . print_r($jazda, true));
    
    if ($jazda) {
        // Update lesson status to 'Anulowana'
        $update_stmt = $conn->prepare("
            UPDATE jazdy 
            SET status = 'Anulowana' 
            WHERE id = ? AND kursant_id = ? AND status = 'Zaplanowana'
        ");
        
        if ($update_stmt === false) {
            error_log("Update prepare failed: " . $conn->error);
            $_SESSION['error_message'] = "Błąd podczas przygotowania zapytania aktualizacji.";
            header("Location: planowanie_jazd.php");
            exit();
        }
        
        $update_stmt->bind_param("ii", $jazda_id, $user_id);
        $update_result = $update_stmt->execute();
        
        error_log("Update result: " . ($update_result ? "success" : "failed"));
        error_log("Update affected rows: " . $update_stmt->affected_rows);
        
        if ($update_result && $update_stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Jazda została anulowana pomyślnie.";
        } else {
            $_SESSION['error_message'] = "Wystąpił błąd podczas anulowania jazdy.";
        }
    } else {
        $_SESSION['error_message'] = "Nie można anulować tej jazdy.";
    }
    
    header("Location: planowanie_jazd.php");
    exit();
}

// Handle form submission (planowanie jazdy)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['anuluj_jazde'])) {
    try {
        $instruktor_id = $_POST['instruktor'];
        $pojazd_id = $_POST['pojazd'];
        $data = $_POST['data'];
        $godzina = $_POST['godzina'];
        $liczba_godzin = $_POST['liczba_godzin'];

        // Validate remaining hours
        if ($liczba_godzin > $pozostale_godziny) {
            throw new Exception("Przekroczono maksymalną liczbę godzin jazd.");
        }

        // Check if instructor and vehicle are available
        $stmt = $conn->prepare("
            SELECT COUNT(*) as konflikt
            FROM jazdy 
            WHERE instruktor_id = ? 
            AND DATE(data_jazdy) = ? 
            AND TIME(data_jazdy) BETWEEN ? AND ADDTIME(?, SEC_TO_TIME(? * 3600))
            AND status = 'Zaplanowana'
        ");
        $stmt->bind_param("issss", $instruktor_id, $data, $godzina, $godzina, $liczba_godzin);
        $stmt->execute();
        $result = $stmt->get_result();
        $konflikt = $result->fetch_assoc()['konflikt'];

        if ($konflikt > 0) {
            throw new Exception("Wybrany termin jest już zajęty.");
        }

        // Insert new driving lesson
        $data_jazdy = $data . ' ' . $godzina . ':00';
        $stmt = $conn->prepare("
            INSERT INTO jazdy (kursant_id, instruktor_id, kurs_id, data_jazdy)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $user_id, $instruktor_id, $kurs_id, $data_jazdy);
        $stmt->execute();

        $_SESSION['success_message'] = "Jazda została zaplanowana pomyślnie.";
        header("Location: planowanie_jazd.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

// Get instructors for the course category
$stmt = $conn->prepare("SELECT * FROM pracownicy WHERE rola = 'instruktor' AND kategorie_uprawnien LIKE ?");
$kategoria_pattern = '%' . $kategoria . '%';
echo "<!-- Debug: Szukana kategoria: " . htmlspecialchars($kategoria) . " -->\n";
echo "<!-- Debug: Wzorzec wyszukiwania: " . htmlspecialchars($kategoria_pattern) . " -->\n";
$stmt->bind_param("s", $kategoria_pattern);
$stmt->execute();
$instruktorzy = $stmt->get_result();
echo "<!-- Debug: Liczba znalezionych instruktorów: " . $instruktorzy->num_rows . " -->\n";

// Pokaż wszystkich instruktorów dla debugowania
$all_instructors = $conn->query("SELECT * FROM pracownicy WHERE rola = 'instruktor'");
echo "<!-- Debug: Całkowita liczba instruktorów w bazie: " . $all_instructors->num_rows . " -->\n";
while ($inst = $all_instructors->fetch_assoc()) {
    echo "<!-- Debug: Instruktor ID: " . $inst['id'] . ", Kategorie: " . $inst['kategorie_uprawnien'] . " -->\n";
}

// Get vehicles for the course category
$stmt = $conn->prepare("SELECT * FROM pojazdy WHERE kategoria_prawa_jazdy = ? AND stan_techniczny = 'Dobry'");
$stmt->bind_param("s", $kategoria);
$stmt->execute();
$pojazdy = $stmt->get_result();

// Get user's scheduled lessons
$stmt = $conn->prepare("
    SELECT j.*, i.imie as instruktor_imie, i.nazwisko as instruktor_nazwisko
    FROM jazdy j
    JOIN pracownicy i ON j.instruktor_id = i.id
    WHERE j.kursant_id = ? AND j.status = 'Zaplanowana'
    ORDER BY j.data_jazdy
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$zaplanowane_jazdy = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planowanie Jazd - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .planning-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .scheduled-lessons {
            margin-top: 3rem;
        }

        .lesson-card {
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        <div class="planning-container">
            <h1>Planowanie Jazd</h1>
            
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

            <div class="course-info">
                <h2>Informacje o kursie</h2>
                <p>Kurs: <?php echo htmlspecialchars($kurs['nazwa']); ?></p>
                <p>Wykorzystane godziny: <?php echo $liczba_jazd; ?> z 30</p>
                <p>Pozostałe godziny: <?php echo $pozostale_godziny; ?></p>
            </div>

            <?php if($pozostale_godziny > 0): ?>
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="instruktor_select">Instruktor:</label>
                            <select name="instruktor" id="instruktor_select" required>
                                <option value="">Wybierz instruktora</option>
                                <?php while($instruktor = $instruktorzy->fetch_assoc()): ?>
                                    <option value="<?php echo $instruktor['id']; ?>">
                                        <?php echo htmlspecialchars($instruktor['imie'] . ' ' . $instruktor['nazwisko']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pojazd_select">Pojazd:</label>
                            <select name="pojazd" id="pojazd_select" required>
                                <option value="">Wybierz pojazd</option>
                                <?php while($pojazd = $pojazdy->fetch_assoc()): ?>
                                    <option value="<?php echo $pojazd['id']; ?>">
                                        <?php echo htmlspecialchars($pojazd['marka'] . ' ' . $pojazd['model']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="data_jazdy">Data:</label>
                            <input type="date" name="data" id="data_jazdy" required 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                        </div>

                        <div class="form-group">
                            <label for="godzina_jazdy">Godzina rozpoczęcia:</label>
                            <select name="godzina" id="godzina_jazdy" required>
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

                        <div class="form-group">
                            <label for="liczba_godzin_select">Liczba godzin:</label>
                            <select name="liczba_godzin" id="liczba_godzin_select" required>
                                <?php
                                for ($i = 1; $i <= min(4, $pozostale_godziny); $i++) {
                                    echo "<option value=\"$i\">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn primary">Zaplanuj jazdę</button>
                </form>
            <?php else: ?>
                <p>Wykorzystałeś już wszystkie godziny jazd.</p>
            <?php endif; ?>

            <div class="scheduled-lessons">
                <h2>Zaplanowane jazdy</h2>
                <?php if($zaplanowane_jazdy->num_rows > 0): ?>
                    <?php while($jazda = $zaplanowane_jazdy->fetch_assoc()): ?>
                        <div class="lesson-card">
                            <div>
                                <p><strong>Data i godzina:</strong> <?php echo date('d.m.Y H:i', strtotime($jazda['data_jazdy'])); ?></p>
                                <p><strong>Instruktor:</strong> <?php echo htmlspecialchars($jazda['instruktor_imie'] . ' ' . $jazda['instruktor_nazwisko']); ?></p>
                            </div>
                            <div>
                                <form method="POST" action="">
                                    <input type="hidden" name="jazda_id" value="<?php echo $jazda['id']; ?>">
                                    <button type="submit" name="anuluj_jazde" class="btn danger" onclick="return confirm('Czy na pewno chcesz anulować tę jazdę?');">Anuluj</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nie masz zaplanowanych jazd.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 