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

        .user-info, .courses-section {
            margin-bottom: 2rem;
        }

        .course-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--light-blue);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .course-status {
            font-weight: bold;
        }

        .course-status.oczekujacy {
            color: orange;
        }

        .course-status.zatwierdzony {
            color: green;
        }

        .course-status.odrzucony {
            color: red;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
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
                        <div class="course-status <?php echo strtolower($course['status']); ?>">
                            <?php echo htmlspecialchars($course['status']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nie jesteś zapisany na żadne kursy.</p>
            <?php endif; ?>
        </div>

        <div class="actions">
            <a href="kurs_prawa_jazdy.html" class="btn">Zapisz się na kurs</a>
            <a href="logout.php" class="btn">Wyloguj</a>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
