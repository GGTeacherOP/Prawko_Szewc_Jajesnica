<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rola']) || $_SESSION['rola'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get statistics
$stats = array();

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM uzytkownicy");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Active courses (all courses since we don't have status column)
$result = $conn->query("SELECT COUNT(*) as total FROM kursy");
$stats['active_courses'] = $result->fetch_assoc()['total'];

// Total payments
$result = $conn->query("SELECT COUNT(*) as total, SUM(kwota) as sum FROM platnosci WHERE status = 'Opłacony'");
$row = $result->fetch_assoc();
$stats['total_payments'] = $row['total'];
$stats['total_revenue'] = $row['sum'] ?? 0;

// Recent activities
$recent_activities = array();

// Recent payments
$result = $conn->query("
    SELECT p.*, u.imie, u.nazwisko, k.nazwa as kurs_nazwa 
    FROM platnosci p 
    LEFT JOIN uzytkownicy u ON p.uzytkownik_id = u.id 
    LEFT JOIN kursy k ON p.kurs_id = k.id 
    ORDER BY p.data_platnosci DESC 
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $recent_activities[] = array(
        'type' => 'payment',
        'data' => $row
    );
}

// Recent course enrollments
$result = $conn->query("
    SELECT z.*, u.imie, u.nazwisko, k.nazwa as kurs_nazwa 
    FROM zapisy z 
    JOIN uzytkownicy u ON z.uzytkownik_id = u.id 
    JOIN kursy k ON z.kurs_id = k.id 
    ORDER BY z.data_zapisu DESC 
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $recent_activities[] = array(
        'type' => 'enrollment',
        'data' => $row
    );
}

// Sort activities by date
usort($recent_activities, function($a, $b) {
    $dateA = $a['type'] === 'payment' ? $a['data']['data_platnosci'] : $a['data']['data_zapisu'];
    $dateB = $b['type'] === 'payment' ? $b['data']['data_platnosci'] : $b['data']['data_zapisu'];
    return strtotime($dateB) - strtotime($dateA);
});

// Limit to 5 most recent activities
$recent_activities = array_slice($recent_activities, 0, 5);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Linia Nauka Jazdy</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
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

        .activity-list {
            list-style: none;
            padding: 0;
        }

        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-info {
            flex: 1;
        }

        .activity-info h4 {
            margin: 0;
            color: var(--primary-color);
        }

        .activity-info p {
            margin: 0.5rem 0 0;
            color: #666;
            font-size: 0.9em;
        }

        .activity-time {
            color: #999;
            font-size: 0.8em;
        }

        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .menu-item {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .menu-item i {
            font-size: 1.5em;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .admin-container {
                margin: 100px 1rem 2rem;
                padding: 1rem;
            }

            .admin-grid {
                grid-template-columns: 1fr;
            }

            .admin-menu {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Panel Administratora</h1>
            <div class="admin-actions">
                <a href="admin_users.php" class="btn">Zarządzaj użytkownikami</a>
                <a href="admin_courses.php" class="btn">Zarządzaj kursami</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Użytkownicy</h3>
                <div class="value"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Kursy</h3>
                <div class="value"><?php echo $stats['active_courses']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Płatności</h3>
                <div class="value"><?php echo $stats['total_payments']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Przychód</h3>
                <div class="value"><?php echo number_format($stats['total_revenue'], 2); ?> PLN</div>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-section">
                <h2>Ostatnie aktywności</h2>
                <ul class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                        <li class="activity-item">
                            <div class="activity-info">
                                <h4>
                                    <?php if ($activity['type'] === 'payment'): ?>
                                        Nowa płatność
                                    <?php else: ?>
                                        Nowe zapisanie na kurs
                                    <?php endif; ?>
                                </h4>
                                <p>
                                    <?php if ($activity['type'] === 'payment'): ?>
                                        <?php echo htmlspecialchars($activity['data']['imie'] . ' ' . $activity['data']['nazwisko']); ?> - 
                                        <?php echo htmlspecialchars($activity['data']['kurs_nazwa'] ?? 'Badanie lekarskie'); ?> - 
                                        <?php echo number_format($activity['data']['kwota'], 2); ?> PLN
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($activity['data']['imie'] . ' ' . $activity['data']['nazwisko']); ?> - 
                                        <?php echo htmlspecialchars($activity['data']['kurs_nazwa']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="activity-time">
                                <?php 
                                    $date = $activity['type'] === 'payment' ? 
                                        $activity['data']['data_platnosci'] : 
                                        $activity['data']['data_zapisu'];
                                    echo date('d.m.Y H:i', strtotime($date));
                                ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="admin-section">
                <h2>Menu administracyjne</h2>
                <div class="admin-menu">
                    <a href="admin_users.php" class="menu-item">
                        <div>Zarządzanie użytkownikami</div>
                        <small>Dodawanie, edycja i usuwanie użytkowników</small>
                    </a>
                    <a href="admin_courses.php" class="menu-item">
                        <div>Zarządzanie kursami</div>
                        <small>Tworzenie i edycja kursów, zarządzanie zapisami</small>
                    </a>
                    <a href="admin_payments.php" class="menu-item">
                        <div>Zarządzanie płatnościami</div>
                        <small>Przegląd i zarządzanie płatnościami</small>
                    </a>
                    <a href="admin_examinations.php" class="menu-item">
                        <div>Zarządzanie badaniami</div>
                        <small>Harmonogram badań, zarządzanie terminami</small>
                    </a>
                    <a href="admin_instructors.php" class="menu-item">
                        <div>Zarządzanie instruktorami</div>
                        <small>Przypisywanie instruktorów, zarządzanie harmonogramem</small>
                    </a>
                    <a href="admin_finances.php" class="menu-item">
                        <div>Zarządzanie finansami</div>
                        <small>Raporty finansowe, zarządzanie opłatami</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .admin-section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease-out';
                observer.observe(card);
            });
        });
    </script>
</body>
</html> 