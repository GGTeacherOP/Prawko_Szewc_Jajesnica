<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if course ID is provided
if (!isset($_GET['kurs_id'])) {
    $_SESSION['error_message'] = "Nie określono kursu.";
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$kurs_id = $_GET['kurs_id'];

try {
    $conn->begin_transaction();

    // Get course details and check enrollment
    $stmt = $conn->prepare("SELECT k.*, z.id as zapis_id, z.status as zapis_status
                           FROM kursy k 
                           JOIN zapisy z ON k.id = z.kurs_id 
                           WHERE k.id = ? AND z.uzytkownik_id = ?");
    if ($stmt === false) {
        throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $kurs_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Nie jesteś zapisany na ten kurs.");
    }
    
    $kurs = $result->fetch_assoc();
    $stmt->close();

    // Check for existing payments
    $stmt = $conn->prepare("SELECT id, status FROM platnosci 
                           WHERE kurs_id = ? AND uzytkownik_id = ? 
                           ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("ii", $kurs_id, $user_id);
    $stmt->execute();
    $payment_result = $stmt->get_result();
    $existing_payment = $payment_result->fetch_assoc();
    $stmt->close();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($existing_payment) {
            if ($existing_payment['status'] === 'Opłacony') {
                throw new Exception("Ten kurs został już opłacony.");
            } elseif ($existing_payment['status'] === 'Oczekujący') {
                // Update existing payment
                $stmt = $conn->prepare("UPDATE platnosci SET 
                                      status = 'Opłacony',
                                      data_platnosci = NOW()
                                      WHERE id = ?");
                $stmt->bind_param("i", $existing_payment['id']);
            }
        } else {
            // Create new payment
            $stmt = $conn->prepare("INSERT INTO platnosci 
                                  (uzytkownik_id, kurs_id, kwota, status, data_platnosci) 
                                  VALUES (?, ?, ?, 'Opłacony', NOW())");
            $stmt->bind_param("iid", $user_id, $kurs_id, $kurs['cena']);
        }

        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas przetwarzania płatności: " . $stmt->error);
        }
        $stmt->close();

        // Update enrollment status
        $stmt = $conn->prepare("UPDATE zapisy SET status = 'Zatwierdzony' 
                              WHERE kurs_id = ? AND uzytkownik_id = ?");
        $stmt->bind_param("ii", $kurs_id, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas aktualizacji statusu zapisu: " . $stmt->error);
        }
        $stmt->close();

        $conn->commit();
        $_SESSION['success_message'] = "Płatność została zrealizowana pomyślnie.";
        header("Location: dashboard.php");
        exit();
    }

    // If we get here, we're displaying the payment form
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Płatność za kurs - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 120px auto 50px;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .payment-details {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .payment-form {
            margin-top: 2rem;
        }

        .payment-method {
            margin-bottom: 1rem;
            padding: 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #f0f7ff;
        }

        .btn-pay {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .payment-info {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #e9ecef;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="payment-container">
        <h1>Płatność za kurs</h1>
        
        <div class="payment-details">
            <h2><?php echo htmlspecialchars($kurs['nazwa']); ?></h2>
            <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($kurs['kategoria']); ?></p>
            <p><strong>Kwota do zapłaty:</strong> <span style="font-size: 1.2em; color: var(--primary-color);"><?php echo number_format($kurs['cena'], 2); ?> PLN</span></p>
        </div>

        <form method="POST" class="payment-form">
            <div class="payment-method selected">
                <input type="radio" name="payment_method" value="transfer" id="transfer" checked>
                <label for="transfer"><strong>Przelew bankowy</strong></label>
                <div class="payment-info">
                    <p><strong>Bank:</strong> Example Bank</p>
                    <p><strong>Nr konta:</strong> 12 3456 7890 1234 5678 9012 3456</p>
                    <p><strong>Tytuł przelewu:</strong> Kurs <?php echo htmlspecialchars($kurs['nazwa']); ?> - <?php echo $user_id; ?></p>
                </div>
            </div>

            <div class="payment-method">
                <input type="radio" name="payment_method" value="card" id="card">
                <label for="card"><strong>Karta płatnicza</strong></label>
                <div class="payment-info">
                    <p>Płatność online przez system płatności</p>
                </div>
            </div>

            <button type="submit" class="btn-pay">Potwierdź płatność</button>
        </form>
    </div>

    <script>
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html> 