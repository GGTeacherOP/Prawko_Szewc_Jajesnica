<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja - Linia Nauka Jazdy</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .register-form input, .register-form select {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
        }

        .register-form input.error {
            border-color: red;
        }

        .register-form .btn {
            width: 100%;
            margin-top: 1rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }

        .checkbox-group {
            margin: 1rem 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Zarejestruj się</h2>
        <?php if(isset($_GET['errors'])): ?>
            <div class="error-message">
                <?php echo str_replace('|', '<br>', htmlspecialchars(urldecode($_GET['errors']))); ?>
            </div>
        <?php endif; ?>
        <form action="register_handler.php" method="POST" class="register-form">
            <input type="text" name="login" placeholder="Login" required pattern="[a-zA-Z0-9_]{3,50}" title="Login może zawierać tylko litery, cyfry i podkreślenia, długość od 3 do 50 znaków">
            <input type="text" name="imie" placeholder="Imię" required>
            <input type="text" name="nazwisko" placeholder="Nazwisko" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="telefon" placeholder="Numer telefonu" required pattern="[0-9]{9,15}">
            <input type="date" name="data_urodzenia" required>
            <select name="kategoria_prawa_jazdy" required>
                <option value="">Wybierz kategorię prawa jazdy</option>
                <option value="B">Kategoria B</option>
                <option value="A">Kategoria A</option>
                <option value="C">Kategoria C</option>
                <option value="D">Kategoria D</option>
            </select>
            <input type="password" name="haslo" placeholder="Hasło" required minlength="8">
            <input type="password" name="potwierdz_haslo" placeholder="Potwierdź hasło" required minlength="8">
            
            <div class="checkbox-group">
                <input type="checkbox" id="regulamin" name="regulamin" required>
                <label for="regulamin">Akceptuję regulamin i politykę prywatności</label>
            </div>

            <button type="submit" class="btn">Zarejestruj się</button>
        </form>
        <div class="login-link">
            <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
        </div>
    </div>
</body>
</html>
