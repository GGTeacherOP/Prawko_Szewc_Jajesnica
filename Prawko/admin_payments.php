<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $id = $_POST['id'];
                $status = $_POST['status'];
                
                $stmt = $conn->prepare("UPDATE platnosci SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM platnosci WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Get all payments with user and course information
$result = $conn->query("
    SELECT p.*, u.imie, u.nazwisko, k.nazwa as kurs_nazwa 
    FROM platnosci p 
    LEFT JOIN uzytkownicy u ON p.uzytkownik_id = u.id 
    LEFT JOIN kursy k ON p.kurs_id = k.id 
    ORDER BY p.data_platnosci DESC
");
$payments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie płatnościami - Panel Administratora</title>
    <link rel="stylesheet" href="styles.css">
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

        .payment-list {
            list-style: none;
            padding: 0;
        }

        .payment-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-item:last-child {
            border-bottom: none;
        }

        .payment-info {
            flex: 1;
        }

        .payment-info h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .payment-info p {
            margin: 0.5rem 0 0;
            color: #666;
        }

        .payment-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .status-select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 0.5rem;
        }

        .status-paid {
            color: #28a745;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }

        .filters {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .filter-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 100px 1rem 2rem;
                padding: 1rem;
            }

            .payment-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .payment-actions {
                margin-top: 1rem;
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Zarządzanie płatnościami</h1>
            <a href="admin_panel.php" class="btn">Powrót do panelu</a>
        </div>

        <div class="admin-section">
            <h2>Lista płatności</h2>
            
            <div class="filters">
                <div class="filter-group">
                    <label for="status-filter">Status</label>
                    <select id="status-filter" onchange="filterPayments()">
                        <option value="all">Wszystkie</option>
                        <option value="Opłacony">Opłacone</option>
                        <option value="Oczekujący">Oczekujące</option>
                        <option value="Anulowany">Anulowane</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date-filter">Sortuj po dacie</label>
                    <select id="date-filter" onchange="filterPayments()">
                        <option value="newest">Najnowsze</option>
                        <option value="oldest">Najstarsze</option>
                    </select>
                </div>
            </div>

            <ul class="payment-list">
                <?php foreach ($payments as $payment): ?>
                    <li class="payment-item" data-status="<?php echo $payment['status']; ?>">
                        <div class="payment-info">
                            <h3>
                                <?php echo htmlspecialchars($payment['imie'] . ' ' . $payment['nazwisko']); ?>
                                <span class="status-<?php echo strtolower($payment['status']); ?>">
                                    (<?php echo $payment['status']; ?>)
                                </span>
                            </h3>
                            <p>
                                <?php if ($payment['kurs_id']): ?>
                                    Kurs: <?php echo htmlspecialchars($payment['kurs_nazwa']); ?>
                                <?php else: ?>
                                    Badanie lekarskie
                                <?php endif; ?>
                            </p>
                            <p>Kwota: <?php echo number_format($payment['kwota'], 2); ?> PLN</p>
                            <p>Data: <?php echo date('d.m.Y H:i', strtotime($payment['data_platnosci'])); ?></p>
                        </div>
                        <div class="payment-actions">
                            <select class="status-select" onchange="updateStatus(<?php echo $payment['id']; ?>, this.value)">
                                <option value="Opłacony" <?php echo $payment['status'] === 'Opłacony' ? 'selected' : ''; ?>>Opłacony</option>
                                <option value="Oczekujący" <?php echo $payment['status'] === 'Oczekujący' ? 'selected' : ''; ?>>Oczekujący</option>
                                <option value="Anulowany" <?php echo $payment['status'] === 'Anulowany' ? 'selected' : ''; ?>>Anulowany</option>
                            </select>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tę płatność?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $payment['id']; ?>">
                                <button type="submit" class="btn-delete">Usuń</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="status" value="${status}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function filterPayments() {
            const statusFilter = document.getElementById('status-filter').value;
            const dateFilter = document.getElementById('date-filter').value;
            const payments = document.querySelectorAll('.payment-item');
            
            payments.forEach(payment => {
                const status = payment.dataset.status;
                if (statusFilter === 'all' || status === statusFilter) {
                    payment.style.display = '';
                } else {
                    payment.style.display = 'none';
                }
            });

            // Sort by date
            const paymentList = document.querySelector('.payment-list');
            const paymentArray = Array.from(payments);
            
            paymentArray.sort((a, b) => {
                const dateA = new Date(a.querySelector('p:last-child').textContent.split(': ')[1]);
                const dateB = new Date(b.querySelector('p:last-child').textContent.split(': ')[1]);
                return dateFilter === 'newest' ? dateB - dateA : dateA - dateB;
            });

            paymentArray.forEach(payment => paymentList.appendChild(payment));
        }
    </script>
</body>
</html> 