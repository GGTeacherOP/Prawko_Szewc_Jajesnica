<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .login-form input {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
        }

        .login-form input.error {
            border-color: red;
        }

        .login-form .btn {
            width: 100%;
            margin-top: 1rem;
        }

        .login-links {
            text-align: center;
            margin-top: 1rem;
        }

        .login-links a {
            color: var(--primary-blue);
            text-decoration: none;
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Zaloguj się</h2>
        <?php if(isset($_GET['errors'])): ?>
            <div class="error-message">
                <?php echo str_replace('|', '<br>', htmlspecialchars(urldecode($_GET['errors']))); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
            <div class="success-message">
                Rejestracja zakończona pomyślnie. Możesz się teraz zalogować.
            </div>
        <?php endif; ?>
        <form action="login_handler.php" method="POST" class="login-form">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="haslo" placeholder="Hasło" required>
            <button type="submit" class="btn">Zaloguj</button>
        </form>
        <div class="login-links">
            <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
            <p><a href="#">Zapomniałeś hasła?</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
