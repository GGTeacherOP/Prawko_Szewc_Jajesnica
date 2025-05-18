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

// Get user's courses
$courses_query = "SELECT k.*, z.status 
                  FROM kursy k 
                  JOIN zapisy z ON k.id = z.kurs_id 
                  WHERE z.uzytkownik_id = ?";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$stmt->close();

// Get user's medical examinations
$examinations_query = "SELECT * FROM badania WHERE uzytkownik_id = ? ORDER BY data_badania DESC LIMIT 1";
$stmt = $conn->prepare($examinations_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$examination_result = $stmt->get_result();
$latest_examination = $examination_result->fetch_assoc();
$stmt->close();

// Get user's payments
$payments_query = "SELECT p.*, k.nazwa as kurs_nazwa 
                  FROM platnosci p 
                  LEFT JOIN kursy k ON p.kurs_id = k.id 
                  WHERE p.uzytkownik_id = ? 
                  ORDER BY p.data_platnosci DESC";
$stmt = $conn->prepare($payments_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$payments_result = $stmt->get_result();
$stmt->close();
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
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php if(isset($_GET['enrollment']) && $_GET['enrollment'] === 'success'): ?>
            <div class="success-message">
                Zostałeś pomyślnie zapisany na kurs. Sprawdź szczegóły płatności poniżej.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['examination']) && $_GET['examination'] === 'success'): ?>
            <div class="success-message">
                Badanie zostało umówione. Sprawdź szczegóły płatności poniżej.
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
            </div>
        <?php endif; ?>

        <div class="user-info">
            <h1>Witaj, <?php echo htmlspecialchars($user['imie'] . ' ' . $user['nazwisko']); ?>!</h1>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Kategoria Prawa Jazdy: <?php echo htmlspecialchars($user['kategoria_prawa_jazdy']); ?></p>
        </div>

        <div class="courses-section">
            <h2>Twoje Kursy</h2>
            <?php if ($courses_result->num_rows > 0): ?>
                <?php while($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-card">
                        <div>
                            <h3><?php echo htmlspecialchars($course['nazwa']); ?></h3>
                            <p><?php echo htmlspecialchars($course['kategoria']); ?></p>
                        </div>
                        <div class="status <?php echo strtolower($course['status']); ?>">
                            <?php echo htmlspecialchars($course['status']); ?>
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
                        <p>Data badania: <?php echo htmlspecialchars($latest_examination['data_badania']); ?></p>
                        <p>Ważne do: <?php echo htmlspecialchars($latest_examination['waznosc_do']); ?></p>
                    </div>
                    <div class="status <?php echo strtolower($latest_examination['wynik']); ?>">
                        <?php echo htmlspecialchars($latest_examination['wynik']); ?>
                    </div>
                </div>
            <?php else: ?>
                <p>Nie masz jeszcze żadnych badań.</p>
            <?php endif; ?>
        </div>

        <div class="payments-section">
            <h2>Twoje Płatności</h2>
            <?php if ($payments_result->num_rows > 0): ?>
                <?php while($payment = $payments_result->fetch_assoc()): ?>
                    <div class="payment-card">
                        <div>
                            <h3><?php echo $payment['kurs_nazwa'] ? htmlspecialchars($payment['kurs_nazwa']) : 'Badanie lekarskie'; ?></h3>
                            <p>Kwota: <?php echo number_format($payment['kwota'], 2); ?> PLN</p>
                            <p>Data: <?php echo htmlspecialchars($payment['data_platnosci']); ?></p>
                        </div>
                        <div class="status <?php echo strtolower($payment['status']); ?>">
                            <?php echo htmlspecialchars($payment['status']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
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
