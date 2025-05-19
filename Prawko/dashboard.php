<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'kursant') {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Debug
error_log("Dashboard accessed by user: " . print_r($_SESSION, true));

// Get user details
$user_id = $_SESSION['user_id'];
$get_user_query = "SELECT * FROM uzytkownicy WHERE id = ?";
$stmt = $conn->prepare($get_user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get user's courses with payment status
$courses_query = "SELECT 
    k.*,
    z.status as zapis_status,
    z.id as zapis_id,
    p.id as payment_id,
    p.status as payment_status,
    p.kwota as payment_amount,
    p.data_platnosci as payment_date
FROM kursy k 
JOIN zapisy z ON k.id = z.kurs_id 
LEFT JOIN platnosci p ON k.id = p.kurs_id AND p.uzytkownik_id = z.uzytkownik_id
WHERE z.uzytkownik_id = ?
ORDER BY z.id DESC";
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

// Get user's payments with course details
$payments_query = "SELECT p.*, k.nazwa as kurs_nazwa, k.kategoria as kurs_kategoria
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
    $has_valid_medical = true;
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Użytkownika - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 120px auto 50px;
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
            background-color: var(--light-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .status {
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .status.oczekujacy {
            background-color: #fff3cd;
            color: #856404;
        }

        .status.zatwierdzony, .status.oplacony, .status.pozytywny {
            background-color: #d4edda;
            color: #155724;
        }

        .status.odrzucony, .status.anulowany, .status.negatywny {
            background-color: #f8d7da;
            color: #721c24;
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
            color: #155724;
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

        .section-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .info-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .info-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                margin: 100px 1rem 2rem;
                padding: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
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
            <h2 class="section-title">Informacje o użytkowniku</h2>
            <div class="info-card">
                <div class="info-grid">
                    <div>
                        <h3>Dane osobowe</h3>
                        <p><strong>Imię i nazwisko:</strong> <?php echo htmlspecialchars($user['imie'] . ' ' . $user['nazwisko']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Telefon:</strong> <?php echo htmlspecialchars($user['telefon']); ?></p>
                    </div>
                    <div>
                        <h3>Uprawnienia</h3>
                        <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($user['kategoria_prawa_jazdy']); ?></p>
                        <p><strong>Status badań:</strong> 
                            <?php if ($has_valid_medical): ?>
                                <span class="status pozytywny">Aktualne</span>
                            <?php else: ?>
                                <span class="status negatywny">Nieaktualne</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="courses-section">
            <h2 class="section-title">Moje kursy</h2>
            <?php if ($courses_result->num_rows > 0): ?>
                <?php while ($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-card">
                        <div>
                            <h3><?php echo htmlspecialchars($course['nazwa']); ?></h3>
                            <p>Kategoria: <?php echo htmlspecialchars($course['kategoria']); ?></p>
                            <p>Status: <span class="status <?php echo strtolower($course['payment_status']); ?>">
                                <?php 
                                if ($course['payment_status'] === 'Opłacony') {
                                    echo 'Opłacony';
                                } elseif ($course['payment_status'] === 'Anulowany') {
                                    echo 'Anulowany';
                                } else {
                                    echo 'Oczekujący na płatność';
                                }
                                ?>
                            </span></p>
                            <div class="course-requirements <?php echo ($course['payment_status'] === 'Opłacony') ? 'success' : 'warning'; ?>">
                                <?php if ($course['payment_status'] !== 'Opłacony'): ?>
                                    <p>❗ Wymagana płatność za kurs</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <?php if ($course['payment_status'] !== 'Opłacony'): ?>
                                <a href="platnosci.php?kurs_id=<?php echo $course['id']; ?>" class="btn btn-primary small">Opłać kurs</a>
                            <?php endif; ?>
                            <?php if ($course['payment_status'] === 'Opłacony'): ?>
                                <a href="moje_jazdy.php" class="btn btn-primary small">Moje jazdy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="info-card">
                    <p>Nie jesteś zapisany na żaden kurs.</p>
                    <a href="kursy.php" class="btn btn-primary">Przeglądaj kursy</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="examinations-section">
            <h2 class="section-title">Badania lekarskie</h2>
            <?php if (!$latest_examination): ?>
                <div class="examination-card">
                    <div>
                        <p>Badanie dostępne</p>
                    </div>
                    <a href="umow_badanie.php" class="btn btn-primary">Umów badanie</a>
                </div>
            <?php else: ?>
                <div class="info-card">
                    <p>Brak historii badań lekarskich.</p>
                    <a href="umow_badanie.php" class="btn btn-primary">Umów badanie</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="payments-section">
            <h2 class="section-title">Płatności</h2>
            <?php if ($payments_result->num_rows > 0): ?>
                <?php while ($payment = $payments_result->fetch_assoc()): ?>
                    <div class="payment-card">
                        <div>
                            <h3><?php echo $payment['kurs_nazwa'] ? htmlspecialchars($payment['kurs_nazwa']) : 'Opłata za badanie'; ?></h3>
                            <p>Kwota: <?php echo number_format($payment['kwota'], 2); ?> PLN</p>
                            <p>Data: <?php echo date('d.m.Y', strtotime($payment['data_platnosci'])); ?></p>
                        </div>
                        <span class="status <?php echo strtolower($payment['status']); ?>">
                            <?php echo htmlspecialchars($payment['status']); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="info-card">
                    <p>Brak historii płatności.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Inicjalizacja tooltipów i innych elementów UI
        document.addEventListener('DOMContentLoaded', function() {
            // Dodaj animacje przy przewijaniu
            const cards = document.querySelectorAll('.course-card, .examination-card, .payment-card');
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
            });

            function checkScroll() {
                cards.forEach(card => {
                    const cardTop = card.getBoundingClientRect().top;
                    if (cardTop < window.innerHeight - 100) {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                        card.style.transition = 'all 0.5s ease-out';
                    }
                });
            }

            window.addEventListener('scroll', checkScroll);
            checkScroll(); // Sprawdź widoczne elementy przy załadowaniu strony
        });
    </script>
</body>
</html>
