<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is owner
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'wlasciciel') {
    header("Location: login.php");
    exit();
}

// Get total income
$result = $conn->query("
    SELECT 
        SUM(CASE WHEN status = 'Opłacony' THEN kwota ELSE 0 END) as total_income,
        COUNT(CASE WHEN status = 'Opłacony' THEN 1 END) as total_payments,
        COUNT(CASE WHEN status = 'Oczekujący' THEN 1 END) as pending_payments,
        COUNT(CASE WHEN status = 'Anulowany' THEN 1 END) as cancelled_payments
    FROM platnosci
");
$finances = $result->fetch_assoc();

// Get income by course
$result = $conn->query("
    SELECT 
        k.nazwa,
        COUNT(p.id) as liczba_platnosci,
        SUM(CASE WHEN p.status = 'Opłacony' THEN p.kwota ELSE 0 END) as przychod
    FROM kursy k
    LEFT JOIN platnosci p ON k.id = p.kurs_id
    GROUP BY k.id
    ORDER BY przychod DESC
");
$course_finances = $result->fetch_all(MYSQLI_ASSOC);

// Get monthly income
$result = $conn->query("
    SELECT 
        DATE_FORMAT(data_platnosci, '%Y-%m') as miesiac,
        SUM(CASE WHEN status = 'Opłacony' THEN kwota ELSE 0 END) as przychod
    FROM platnosci
    GROUP BY DATE_FORMAT(data_platnosci, '%Y-%m')
    ORDER BY miesiac DESC
    LIMIT 12
");
$monthly_finances = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Właściciela - Finanse</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .owner-header {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .owner-header h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
        }

        .owner-container {
            max-width: 1200px;
            margin: 80px auto 50px;
            padding: 2rem;
        }

        .finance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .finance-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .finance-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .finance-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .finance-label {
            color: #666;
            font-size: 0.9rem;
        }

        .table-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .logout-btn {
            background-color: white;
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="owner-header">
        <h1>Panel Finansowy</h1>
        <a href="logout.php" class="logout-btn">Wyloguj</a>
    </div>

    <div class="owner-container">
        <div class="finance-grid">
            <div class="finance-card">
                <h3>Całkowity Przychód</h3>
                <div class="finance-value positive"><?php echo number_format($finances['total_income'], 2); ?> zł</div>
                <div class="finance-label">Od początku działalności</div>
            </div>
            <div class="finance-card">
                <h3>Liczba Płatności</h3>
                <div class="finance-value"><?php echo $finances['total_payments']; ?></div>
                <div class="finance-label">Zrealizowane płatności</div>
            </div>
            <div class="finance-card">
                <h3>Oczekujące Płatności</h3>
                <div class="finance-value"><?php echo $finances['pending_payments']; ?></div>
                <div class="finance-label">Do realizacji</div>
            </div>
            <div class="finance-card">
                <h3>Anulowane Płatności</h3>
                <div class="finance-value negative"><?php echo $finances['cancelled_payments']; ?></div>
                <div class="finance-label">Łącznie</div>
            </div>
        </div>

        <div class="table-container">
            <h2>Przychody według kursów</h2>
            <table>
                <thead>
                    <tr>
                        <th>Kurs</th>
                        <th>Liczba płatności</th>
                        <th>Przychód</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($course_finances as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['nazwa']); ?></td>
                        <td><?php echo $course['liczba_platnosci']; ?></td>
                        <td class="positive"><?php echo number_format($course['przychod'], 2); ?> zł</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h2>Przychody miesięczne</h2>
            <table>
                <thead>
                    <tr>
                        <th>Miesiąc</th>
                        <th>Przychód</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_finances as $month): ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($month['miesiac'] . '-01')); ?></td>
                        <td class="positive"><?php echo number_format($month['przychod'], 2); ?> zł</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 