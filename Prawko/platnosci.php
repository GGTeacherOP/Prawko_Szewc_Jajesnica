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
    // Get course details
    $stmt = $conn->prepare("SELECT k.*, p.id as platnosc_id, p.status as platnosc_status 
                           FROM kursy k 
                           LEFT JOIN platnosci p ON k.id = p.kurs_id AND p.uzytkownik_id = ?
                           WHERE k.id = ?");
    if ($stmt === false) {
        throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $user_id, $kurs_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Nie znaleziono kursu.");
    }
    
    $kurs = $result->fetch_assoc();
    $stmt->close();

    // Check if payment already exists and is pending
    if ($kurs['platnosc_id'] && $kurs['platnosc_status'] === 'Oczekujący') {
        $_SESSION['error_message'] = "Płatność za ten kurs jest już w trakcie realizacji.";
        header("Location: dashboard.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Here you would typically integrate with a payment gateway
        // For now, we'll just mark the payment as completed
        
        $stmt = $conn->prepare("UPDATE platnosci SET status = 'Opłacony' WHERE kurs_id = ? AND uzytkownik_id = ?");
        if ($stmt === false) {
            throw new Exception("Błąd przygotowania zapytania: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $kurs_id, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Błąd podczas aktualizacji płatności: " . $stmt->error);
        }
        
        $_SESSION['success_message'] = "Płatność została zrealizowana pomyślnie.";
        header("Location: dashboard.php");
        exit();
    }
} catch (Exception $e) {
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
    <link rel="stylesheet" href="css/style.css">
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
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .payment-form {
            margin-top: 2rem;
        }

        .payment-method {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #f0f7ff;
        }

        .btn-pay {
            background-color: #28a745;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-pay:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="payment-container">
        <h1>Płatność za kurs</h1>
        
        <div class="payment-details">
            <h2><?php echo htmlspecialchars($kurs['nazwa']); ?></h2>
            <p>Kategoria: <?php echo htmlspecialchars($kurs['kategoria']); ?></p>
            <p>Kwota do zapłaty: <strong><?php echo number_format($kurs['cena'], 2); ?> PLN</strong></p>
        </div>

        <form method="POST" class="payment-form">
            <div class="payment-method selected">
                <input type="radio" name="payment_method" value="transfer" id="transfer" checked>
                <label for="transfer">Przelew bankowy</label>
                <p>Dane do przelewu:</p>
                <p>Bank: Example Bank</p>
                <p>Nr konta: 12 3456 7890 1234 5678 9012 3456</p>
                <p>Tytuł przelewu: Kurs <?php echo htmlspecialchars($kurs['nazwa']); ?> - <?php echo $user_id; ?></p>
            </div>

            <div class="payment-method">
                <input type="radio" name="payment_method" value="card" id="card">
                <label for="card">Karta płatnicza</label>
                <p>Płatność online przez system płatności</p>
            </div>

            <button type="submit" class="btn-pay">Potwierdź płatność</button>
        </form>
    </div>

    <script>
        // Handle payment method selection
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