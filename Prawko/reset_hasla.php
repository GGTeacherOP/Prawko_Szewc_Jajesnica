<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Hasła - Linia Nauka Jazdy</title>
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

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .reset-info {
            font-size: 0.9em;
            color: #666;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="reset-container">
        <h2>Reset Hasła</h2>
        
        <div class="reset-info">
            Podaj swój adres email, a wyślemy Ci link do zresetowania hasła.
        </div>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php 
                    echo htmlspecialchars($_SESSION['error_message']);
                    unset($_SESSION['error_message']);
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

        <form action="reset_handler.php" method="POST" class="reset-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <button type="submit" class="btn-reset">Wyślij Link do Resetu</button>
        </form>

        <div class="login-link">
            <p>Pamiętasz hasło? <a href="login.php">Zaloguj się</a></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 