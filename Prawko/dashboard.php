<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$get_user_query = "SELECT * FROM uzytkownicy WHERE id = ?";
$stmt = $conn->prepare($get_user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user's courses with more details
$courses_query = "SELECT k.*, z.status, z.id as zapis_id,
                  (SELECT COUNT(*) FROM badania b 
                   WHERE b.uzytkownik_id = z.uzytkownik_id 
                   AND b.wynik = 'Pozytywny' 
                   AND b.waznosc_do >= CURDATE()
                   AND b.typ = 'Podstawowe'
                   AND b.status = 'Zatwierdzony') as has_valid_medical,
                  (SELECT COUNT(*) FROM platnosci p 
                   WHERE p.kurs_id = k.id 
                   AND p.uzytkownik_id = z.uzytkownik_id 
                   AND p.status = 'Opłacony') as is_paid
                  FROM kursy k 
                  JOIN zapisy z ON k.id = z.kurs_id 
                  WHERE z.uzytkownik_id = ?
                  ORDER BY z.id DESC";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$stmt->close();

// Get user's medical examinations
$examinations_query = "SELECT * FROM badania WHERE uzytkownik_id = ? AND typ = 'Podstawowe' ORDER BY data_badania DESC LIMIT 1";
$stmt = $conn->prepare($examinations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$examination_result = $stmt->get_result();
$latest_examination = $examination_result->fetch_assoc();
$stmt->close();

// Get user's payments with course details
$payments_query = "SELECT p.*, k.nazwa as kurs_nazwa, k.kategoria as kurs_kategoria,
                         CASE 
                             WHEN p.opis IS NOT NULL AND p.opis LIKE '%badanie%' THEN 'badanie'
                             WHEN k.kategoria IS NOT NULL THEN k.kategoria
                             ELSE 'kurs'
                         END as payment_type
                  FROM platnosci p 
                  LEFT JOIN kursy k ON p.kurs_id = k.id 
                  WHERE p.uzytkownik_id = ? 
                  ORDER BY p.data_platnosci DESC";
$stmt = $conn->prepare($payments_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$payments_result = $stmt->get_result();
$stmt->close();

// Check if medical examination is needed
$needs_medical = false;
$has_valid_medical = false;

if ($latest_examination) {
    $has_valid_medical = ($latest_examination['wynik'] == 'Pozytywny' && 
                         strtotime($latest_examination['waznosc_do']) >= strtotime('today'));
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Użytkownika - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .user-info, .courses-section, .examinations-section, .payments-section {
            margin-bottom: 2rem;
        }

        .course-card, .examination-card, .payment-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--light-blue);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .status {
            font-weight: bold;
        }

        .status.oczekujacy {
            color: orange;
        }

        .status.zatwierdzony, .status.oplacony, .status.pozytywny {
            color: green;
        }

        .status.odrzucony, .status.anulowany, .status.negatywny {
            color: red;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .course-requirements {
            margin-top: 0.5rem;
            font-size: 0.9em;
            color: #666;
        }

        .course-requirements.warning {
            color: #856404;
        }

        .course-requirements.success {
            color: green;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn.small {
            padding: 0.25rem 0.5rem;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
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

        <div class="user-info">
            <h1>Witaj, <?php echo htmlspecialchars($user['imie'] . ' ' . $user['nazwisko']); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <div class="license-category">
                <p>Kategoria Prawa Jazdy: <?php echo isset($user['kategoria_prawa_jazdy']) ? htmlspecialchars($user['kategoria_prawa_jazdy']) : 'Nie wybrano'; ?></p>
                <form action="update_category.php" method="POST" class="update-category-form">
                    <select name="kategoria_prawa_jazdy" required>
                        <option value="">Wybierz kategorię</option>
                        <option value="A" <?php echo (isset($user['kategoria_prawa_jazdy']) && $user['kategoria_prawa_jazdy'] == 'A') ? 'selected' : ''; ?>>A - Motocykl</option>
                        <option value="B" <?php echo (isset($user['kategoria_prawa_jazdy']) && $user['kategoria_prawa_jazdy'] == 'B') ? 'selected' : ''; ?>>B - Samochód osobowy</option>
                        <option value="C" <?php echo (isset($user['kategoria_prawa_jazdy']) && $user['kategoria_prawa_jazdy'] == 'C') ? 'selected' : ''; ?>>C - Samochód ciężarowy</option>
                        <option value="D" <?php echo (isset($user['kategoria_prawa_jazdy']) && $user['kategoria_prawa_jazdy'] == 'D') ? 'selected' : ''; ?>>D - Autobus</option>
                    </select>
                    <button type="submit" class="btn primary">Aktualizuj kategorię</button>
                </form>
            </div>
        </div>

        <div class="courses-section">
            <h2>Twoje Kursy</h2>
            <?php if ($courses_result->num_rows > 0): ?>
                <?php while($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-card">
                        <div>
                            <h3><?php echo htmlspecialchars($course['nazwa']); ?></h3>
                            <p><?php echo htmlspecialchars($course['kategoria']); ?></p>
                            
                            <?php if ($course['kategoria'] === 'Prawo Jazdy'): ?>
                                <div class="course-requirements <?php echo (!$course['has_valid_medical']) ? 'warning' : ''; ?>">
                                    <?php if (!$course['has_valid_medical']): ?>
                                        <p>⚠️ Wymagane badanie lekarskie</p>
                                        <div class="action-buttons">
                                            <a href="umow_badanie.php?typ=podstawowe" class="btn small primary">Umów badanie</a>
                                        </div>
                                    <?php else: ?>
                                        <p>✓ Badanie lekarskie aktualne</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!$course['is_paid']): ?>
                                <div class="course-requirements warning">
                                    <p>⚠️ Wymagana płatność za kurs</p>
                                    <div class="action-buttons">
                                        <a href="oplaty.php" class="btn small primary">Przejdź do płatności</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="course-requirements success">
                                    <p>✓ Kurs opłacony</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($course['status'] === 'Zatwierdzony'): ?>
                                <div class="course-requirements success">
                                    <p>✓ Możesz rozpocząć kurs</p>
                                    <?php if ($course['kategoria'] === 'Prawo Jazdy'): ?>
                                        <div class="action-buttons">
                                            <a href="planowanie_jazd.php" class="btn small primary">Zaplanuj jazdę</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="status <?php echo strtolower($course['status']); ?>">
                                <?php echo htmlspecialchars($course['status']); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nie jesteś zapisany na żadne kursy.</p>
            <?php endif; ?>
        </div>

        <div class="examinations-section">
            <h2>Twoje Badania</h2>
            <?php if ($latest_examination): ?>
                <div class="examination-card">
                    <div>
                        <p>Data badania: <?php echo htmlspecialchars(date('Y-m-d', strtotime($latest_examination['data_badania']))); ?></p>
                        <p>Ważne do: <?php echo htmlspecialchars($latest_examination['waznosc_do']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($latest_examination['status']); ?></p>
                        <?php if ($latest_examination['status'] === 'Oczekujący'): ?>
                            <p class="warning">⚠️ Oczekuje na realizację</p>
                        <?php endif; ?>
                        <?php if ($latest_examination['wynik'] === 'Pozytywny'): ?>
                            <p class="success">✓ Badanie zaliczone</p>
                        <?php elseif ($latest_examination['wynik'] === 'Negatywny'): ?>
                            <p class="error">✗ Badanie niezaliczone</p>
                            <div class="action-buttons">
                                <a href="umow_badanie.php?typ=podstawowe" class="btn small primary">Umów nowe badanie</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="status <?php echo strtolower($latest_examination['wynik']); ?>">
                        <?php echo htmlspecialchars($latest_examination['wynik']); ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Nie masz jeszcze żadnych badań.</p>
                <?php if ($courses_result->num_rows > 0): ?>
                    <div class="action-buttons">
                        <a href="umow_badanie.php?typ=podstawowe" class="btn primary">Umów badanie</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="payments-section">
            <h2>Twoje Płatności</h2>
            <?php if ($payments_result->num_rows > 0): ?>
                <?php 
                $total_unpaid = 0;
                while($payment = $payments_result->fetch_assoc()): 
                    if ($payment['status'] === 'Oczekujący') {
                        $total_unpaid += $payment['kwota'];
                    }
                ?>
                    <div class="payment-card">
                        <div>
                            <h3>
                                <?php 
                                if ($payment['payment_type'] === 'badanie') {
                                    echo 'Badanie lekarskie';
                                } else {
                                    echo $payment['kurs_nazwa'] ? htmlspecialchars($payment['kurs_nazwa']) : 'Płatność';
                                }
                                ?>
                            </h3>
                            <p>Kwota: <?php echo number_format($payment['kwota'], 2); ?> PLN</p>
                            <p>Data: <?php echo htmlspecialchars($payment['data_platnosci']); ?></p>
                            <?php if ($payment['status'] === 'Oczekujący'): ?>
                                <p class="warning">⚠️ Wymagana płatność</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="status <?php echo strtolower($payment['status']); ?>">
                                <?php echo htmlspecialchars($payment['status']); ?>
                            </div>
                            <?php if ($payment['status'] === 'Oczekujący'): ?>
                                <a href="process_payment.php?payment_id=<?php echo $payment['id']; ?>" class="btn primary">Zapłać</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php if ($total_unpaid > 0): ?>
                    <div class="total-payment-card">
                        <div>
                            <h3>Suma oczekujących płatności</h3>
                            <p>Całkowita kwota: <?php echo number_format($total_unpaid, 2); ?> PLN</p>
                        </div>
                        <a href="process_payment.php?all=true" class="btn primary">Zapłać wszystko</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Nie masz żadnych płatności.</p>
            <?php endif; ?>
        </div>

        <div class="actions">
            <a href="index.php" class="btn">Strona główna</a>
            <a href="logout.php" class="btn">Wyloguj</a>
        </div>
    </div>
</body>
</html>
