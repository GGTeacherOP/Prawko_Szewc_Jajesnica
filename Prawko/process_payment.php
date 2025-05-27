<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payments = array();
$total_amount = 0;

try {
    if (isset($_GET['all'])) {
        // Get all unpaid payments for the user
        $stmt = $conn->prepare("SELECT p.*, k.nazwa as kurs_nazwa 
                               FROM platnosci p 
                               LEFT JOIN kursy k ON p.kurs_id = k.id 
                               WHERE p.uzytkownik_id = ? AND p.status = 'Oczekujący'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($payment = $result->fetch_assoc()) {
            $payments[] = $payment;
            $total_amount += $payment['kwota'];
        }
        $stmt->close();
    } elseif (isset($_GET['payment_id'])) {
        // Get single payment with detailed status check
        $payment_id = $_GET['payment_id'];
        
        // First check if there's any payment in progress
        $stmt = $conn->prepare("SELECT COUNT(*) as in_progress 
                               FROM platnosci 
                               WHERE uzytkownik_id = ? 
                               AND status = 'Oczekujący' 
                               AND id != ?");
        $stmt->bind_param("ii", $user_id, $payment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $in_progress = $result->fetch_assoc()['in_progress'];
        $stmt->close();

        if ($in_progress > 0) {
            throw new Exception("Masz już inną płatność w trakcie realizacji. Zakończ ją lub anuluj przed rozpoczęciem nowej.");
        }

        // Now get the specific payment
        $stmt = $conn->prepare("SELECT p.*, k.nazwa as kurs_nazwa 
                               FROM platnosci p 
                               LEFT JOIN kursy k ON p.kurs_id = k.id 
                               WHERE p.id = ? AND p.uzytkownik_id = ?");
        $stmt->bind_param("ii", $payment_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($payment = $result->fetch_assoc()) {
            // Check if payment is already in progress or completed
            if ($payment['status'] === 'Opłacony') {
                throw new Exception("Ta płatność została już zrealizowana.");
            } elseif ($payment['status'] === 'Anulowany') {
                throw new Exception("Ta płatność została anulowana.");
            }
            $payments[] = $payment;
            $total_amount = $payment['kwota'];
        }
        $stmt->close();
    } else {
        throw new Exception("Nieprawidłowe żądanie płatności.");
    }

    if (empty($payments)) {
        throw new Exception("Nie znaleziono płatności do realizacji.");
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();
        
        foreach ($payments as $payment) {
            // Double check payment status before processing
            $stmt = $conn->prepare("SELECT status FROM platnosci WHERE id = ? AND uzytkownik_id = ? FOR UPDATE");
            $stmt->bind_param("ii", $payment['id'], $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_status = $result->fetch_assoc();
            $stmt->close();

            if ($current_status['status'] !== 'Oczekujący') {
                throw new Exception("Status płatności został zmieniony. Odśwież stronę i spróbuj ponownie.");
            }

            // Update payment status
            $stmt = $conn->prepare("UPDATE platnosci SET status = 'Opłacony', data_platnosci = NOW() WHERE id = ? AND uzytkownik_id = ?");
            $stmt->bind_param("ii", $payment['id'], $user_id);
            $stmt->execute();
            $stmt->close();

            // If this is a course payment, update course status
            if ($payment['kurs_id']) {
                // Check if user has valid medical examination (for driving license courses)
                $requires_medical = false;
                $has_valid_medical = false;

                // Get course type
                $stmt = $conn->prepare("SELECT kategoria FROM kursy WHERE id = ?");
                $stmt->bind_param("i", $payment['kurs_id']);
                $stmt->execute();
                $course_result = $stmt->get_result();
                $course = $course_result->fetch_assoc();
                $stmt->close();

                if ($course && $course['kategoria'] === 'Prawo Jazdy') {
                    $requires_medical = true;
                    
                    // Check for valid medical examination
                    $stmt = $conn->prepare("SELECT id FROM badania 
                                          WHERE uzytkownik_id = ? 
                                          AND wynik = 'Pozytywny' 
                                          AND waznosc_do >= CURDATE()
                                          AND typ = 'Podstawowe'
                                          AND status = 'Zatwierdzony'");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $medical_result = $stmt->get_result();
                    $has_valid_medical = $medical_result->num_rows > 0;
                    $stmt->close();
                }

                // Update course enrollment status
                $new_status = ($requires_medical && !$has_valid_medical) ? 'Oczekujący' : 'Zatwierdzony';
                $stmt = $conn->prepare("UPDATE zapisy SET status = ? WHERE uzytkownik_id = ? AND kurs_id = ?");
                $stmt->bind_param("sii", $new_status, $user_id, $payment['kurs_id']);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        $conn->commit();
        $_SESSION['success_message'] = "Płatność została zrealizowana pomyślnie.";
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "Wystąpił błąd podczas realizacji płatności: " . $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizacja Płatności - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .payment-summary {
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .payment-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .payment-total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #dee2e6;
            font-weight: bold;
        }

        .payment-methods {
            margin: 2rem 0;
        }

        .payment-method {
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
        }

        .payment-method:hover {
            background-color: #f8f9fa;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #e8f0fe;
        }

        .card-details {
            margin-top: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #dee2e6;
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
                <li><a href="dashboard.php">Panel</a></li>
                <li><a href="logout.php">Wyloguj</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="payment-container">
            <h1>Realizacja Płatności</h1>

            <div class="payment-summary">
                <h2>Podsumowanie płatności</h2>
                <?php foreach ($payments as $payment): ?>
                    <div class="payment-item">
                        <div>
                            <?php 
                            $description = $payment['kurs_nazwa'] 
                                ? htmlspecialchars($payment['kurs_nazwa']) 
                                : ($payment['opis'] ? htmlspecialchars($payment['opis']) : 'Płatność');
                            echo $description;
                            ?>
                        </div>
                        <div><?php echo number_format($payment['kwota'], 2); ?> PLN</div>
                    </div>
                <?php endforeach; ?>
                
                <div class="payment-total">
                    <div class="payment-item">
                        <div>Suma do zapłaty:</div>
                        <div><?php echo number_format($total_amount, 2); ?> PLN</div>
                    </div>
                </div>
            </div>

            <form method="POST" id="payment-form">
                <div class="payment-methods">
                    <h2>Wybierz metodę płatności</h2>
                    
                    <div class="payment-method" onclick="selectPaymentMethod('card')">
                        <input type="radio" name="payment_method" value="card" id="card" required>
                        <label for="card">Karta płatnicza</label>
                        
                        <div class="card-details" id="card-details" style="display: none;">
                            <div class="form-group">
                                <label for="card_number">Numer karty</label>
                                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="form-group">
                                <label for="expiry">Data ważności</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/RR" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" onclick="selectPaymentMethod('blik')">
                        <input type="radio" name="payment_method" value="blik" id="blik" required>
                        <label for="blik">BLIK</label>
                        
                        <div class="card-details" id="blik-details" style="display: none;">
                            <div class="form-group">
                                <label for="blik_code">Kod BLIK</label>
                                <input type="text" id="blik_code" name="blik_code" placeholder="123456" maxlength="6">
                            </div>
                        </div>
                    </div>

                    <div class="payment-method" onclick="selectPaymentMethod('transfer')">
                        <input type="radio" name="payment_method" value="transfer" id="transfer" required>
                        <label for="transfer">Przelew bankowy</label>
                    </div>
                </div>

                <button type="submit" class="btn primary">Zapłać <?php echo number_format($total_amount, 2); ?> PLN</button>
                <a href="dashboard.php" class="btn">Anuluj</a>
            </form>
        </div>
    </main>

    <script>
        function selectPaymentMethod(method) {
            // Hide all details sections
            document.querySelectorAll('.card-details').forEach(el => el.style.display = 'none');
            
            // Remove selected class from all methods
            document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
            
            // Select the radio button
            document.getElementById(method).checked = true;
            
            // Show the details section for the selected method
            const detailsSection = document.getElementById(method + '-details');
            if (detailsSection) {
                detailsSection.style.display = 'block';
            }
            
            // Add selected class to the parent div
            document.getElementById(method).closest('.payment-method').classList.add('selected');
        }

        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = formattedValue;
        });

        // Format expiry date input
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Allow only numbers in CVV and BLIK code
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
        
        document.getElementById('blik_code').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html> 