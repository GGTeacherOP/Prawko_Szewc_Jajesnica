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
        .registration-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .registration-form {
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

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid var(--light-blue);
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
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

        .btn-register {
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

        .btn-register:hover {
            background-color: var(--dark-blue);
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="registration-container">
        <h2>Rejestracja</h2>
        
        <?php if(isset($_SESSION['error_messages'])): ?>
            <div class="error-message">
                <?php 
                    foreach ($_SESSION['error_messages'] as $error) {
                        echo htmlspecialchars($error) . "<br>";
                    }
                    unset($_SESSION['error_messages']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                    echo htmlspecialchars($_SESSION['success_message']);
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="register_handler.php" method="POST" class="registration-form">
            <div class="form-group">
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" required>
            </div>

            <div class="form-group">
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefon">Telefon:</label>
                <input type="tel" id="telefon" name="telefon" pattern="[0-9]{9}" placeholder="123456789" required>
            </div>

            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>
            </div>

            <div class="form-group">
                <label for="haslo">Hasło:</label>
                <input type="password" id="haslo" name="haslo" required>
            </div>

            <div class="form-group">
                <label for="powtorz_haslo">Powtórz hasło:</label>
                <input type="password" id="powtorz_haslo" name="powtorz_haslo" required>
            </div>

            <div class="form-group">
                <label for="data_urodzenia">Data urodzenia:</label>
                <input type="date" id="data_urodzenia" name="data_urodzenia" required>
            </div>

            <div class="form-group">
                <label for="kategoria_prawa_jazdy">Kategoria prawa jazdy:</label>
                <select id="kategoria_prawa_jazdy" name="kategoria_prawa_jazdy" required>
                    <option value="">Wybierz kategorię</option>
                    <option value="A">A - Motocykl</option>
                    <option value="B">B - Samochód osobowy</option>
                    <option value="C">C - Samochód ciężarowy</option>
                    <option value="D">D - Autobus</option>
                </select>
            </div>

            <button type="submit" class="btn-register">Zarejestruj się</button>
        </form>

        <div class="login-link">
            <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 