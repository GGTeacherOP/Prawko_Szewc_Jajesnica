<?php
session_start();
require_once 'config.php';

// Check if token is provided
if (!isset($_GET['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_GET['token'];

// Validate token and check expiry
$stmt = $conn->prepare("
    SELECT rt.user_id, rt.expiry, u.imie 
    FROM reset_tokens rt 
    JOIN uzytkownicy u ON u.id = rt.user_id 
    WHERE rt.token = ? AND rt.used = 0 AND rt.expiry > NOW()
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Link do resetowania hasła jest nieprawidłowy lub wygasł.";
    header("Location: reset_hasla.php");
    exit();
}

$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowe Hasło - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .reset-form {
            display: grid;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: bold;
            color: var(--dark-blue);
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success-message {
            color: green;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn-reset {
            background-color: var(--primary-blue);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 1rem;
        }

        .btn-reset:hover {
            background-color: var(--dark-blue);
        }

        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .password-requirements ul {
            margin: 0.5rem 0 0 1.5rem;
            padding: 0;
        }

        .password-requirements li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="reset-container">
        <h2>Ustaw Nowe Hasło</h2>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                    echo htmlspecialchars($_SESSION['error_message']);
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="password-requirements">
            <strong>Wymagania dotyczące hasła:</strong>
            <ul>
                <li>Minimum 8 znaków</li>
                <li>Co najmniej jedna wielka litera</li>
                <li>Co najmniej jedna mała litera</li>
                <li>Co najmniej jedna cyfra</li>
                <li>Co najmniej jeden znak specjalny</li>
            </ul>
        </div>

        <form action="update_haslo.php" method="POST" class="reset-form">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="form-group">
                <label for="haslo">Nowe hasło:</label>
                <input type="password" id="haslo" name="haslo" required>
            </div>

            <div class="form-group">
                <label for="powtorz_haslo">Powtórz nowe hasło:</label>
                <input type="password" id="powtorz_haslo" name="powtorz_haslo" required>
            </div>

            <button type="submit" class="btn-reset">Zmień Hasło</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html> 