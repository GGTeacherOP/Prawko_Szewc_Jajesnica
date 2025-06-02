<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle payment approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    $payment_id = $_POST['id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Update payment status
        $stmt = $conn->prepare("UPDATE platnosci SET status = 'Opłacony' WHERE id = ?");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        
        // If this is a course payment, update the course enrollment status
        $stmt = $conn->prepare("
            SELECT kurs_id, uzytkownik_id 
            FROM platnosci 
            WHERE id = ? AND kurs_id IS NOT NULL
        ");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $payment_data = $result->fetch_assoc();
            $stmt = $conn->prepare("
                UPDATE zapisy 
                SET status = 'Aktywny' 
                WHERE kurs_id = ? AND uzytkownik_id = ?
            ");
            $stmt->bind_param("ii", $payment_data['kurs_id'], $payment_data['uzytkownik_id']);
            $stmt->execute();
        }
        
        $conn->commit();
        header("Location: admin_finances.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_finances.php?error=1");
        exit();
    }
}

// Get financial statistics
$stats = array();

// Total revenue
$result = $conn->query("SELECT SUM(kwota) as total FROM platnosci WHERE status = 'Opłacony'");
$stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Revenue by month
$result = $conn->query("
    SELECT DATE_FORMAT(data_platnosci, '%Y-%m') as month, SUM(kwota) as total
    FROM platnosci
    WHERE status = 'Opłacony'
    GROUP BY DATE_FORMAT(data_platnosci, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
$monthly_revenue = $result->fetch_all(MYSQLI_ASSOC);

// Revenue by course
$result = $conn->query("
    SELECT k.nazwa, COUNT(p.id) as liczba_platnosci, SUM(p.kwota) as total
    FROM platnosci p
    JOIN kursy k ON p.kurs_id = k.id
    WHERE p.status = 'Opłacony'
    GROUP BY k.id
    ORDER BY total DESC
");
$course_revenue = $result->fetch_all(MYSQLI_ASSOC);

// Pending payments
$result = $conn->query("
    SELECT p.*, u.imie, u.nazwisko, k.nazwa as kurs_nazwa
    FROM platnosci p
    LEFT JOIN uzytkownicy u ON p.uzytkownik_id = u.id
    LEFT JOIN kursy k ON p.kurs_id = k.id
    WHERE p.status = 'Oczekujący'
    ORDER BY p.data_platnosci DESC
");
$pending_payments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie finansami - Panel Administratora</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 120px auto 50px;
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .admin-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 1.5em;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }

        .revenue-list {
            list-style: none;
            padding: 0;
        }

        .revenue-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .revenue-item:last-child {
            border-bottom: none;
        }

        .revenue-info {
            flex: 1;
        }

        .revenue-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .revenue-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .pending-list {
            list-style: none;
            padding: 0;
        }

        .pending-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .pending-item:last-child {
            border-bottom: none;
        }

        .pending-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .pending-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .btn-approve {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Zarządzanie finansami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Płatność została pomyślnie zatwierdzona.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                Wystąpił błąd podczas zatwierdzania płatności. Spróbuj ponownie.
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Całkowity przychód</h3>
                <div class="value"><?php echo number_format($stats['total_revenue'], 2); ?> PLN</div>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Przychód miesięczny</h2>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="admin-section">
                <h2>Przychód z kursów</h2>
                <div class="chart-container">
                    <canvas id="courseChart"></canvas>
                </div>
            </div>

            <div class="admin-section">
                <h2>Oczekujące płatności</h2>
                <ul class="pending-list">
                    <?php foreach ($pending_payments as $payment): ?>
                        <li class="pending-item">
                            <div class="pending-info">
                                <h3>
                                    <?php echo htmlspecialchars($payment['imie'] . ' ' . $payment['nazwisko']); ?>
                                    <span class="amount"><?php echo number_format($payment['kwota'], 2); ?> PLN</span>
                                </h3>
                                <p>
                                    <?php if ($payment['kurs_id']): ?>
                                        Kurs: <?php echo htmlspecialchars($payment['kurs_nazwa']); ?>
                                    <?php else: ?>
                                        Badanie lekarskie
                                    <?php endif; ?>
                                </p>
                                <p>Data: <?php echo date('d.m.Y H:i', strtotime($payment['data_platnosci'])); ?></p>
                            </div>
                            <form method="POST" style="margin-top: 1rem;">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="id" value="<?php echo $payment['id']; ?>">
                                <button type="submit" class="btn-approve">Zatwierdź płatność</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Monthly revenue chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($item) {
                    return date('m.Y', strtotime($item['month'] . '-01'));
                }, $monthly_revenue)); ?>,
                datasets: [{
                    label: 'Przychód miesięczny',
                    data: <?php echo json_encode(array_map(function($item) {
                        return $item['total'];
                    }, $monthly_revenue)); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' PLN';
                            }
                        }
                    }
                }
            }
        });

        // Course revenue chart
        const courseCtx = document.getElementById('courseChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_map(function($item) {
                    return $item['nazwa'];
                }, $course_revenue)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_map(function($item) {
                        return $item['total'];
                    }, $course_revenue)); ?>,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw.toFixed(2) + ' PLN';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 