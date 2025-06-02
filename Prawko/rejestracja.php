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
            margin: 120px auto 50px;
            padding: 2.5rem;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .registration-container h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 600;
        }

        .registration-form {
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--primary-color);
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.875rem;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group input:hover,
        .form-group select:hover {
            border-color: var(--primary-color);
            background-color: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
        }

        .form-group input::placeholder {
            color: #adb5bd;
        }

        .error-message {
            background-color: #fff5f5;
            color: #e53e3e;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #feb2b2;
            font-size: 0.95rem;
        }

        .success-message {
            background-color: #f0fff4;
            color: #38a169;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #9ae6b4;
            font-size: 0.95rem;
        }

        .btn-register {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-register:hover {
            background-color: var(--primary-color-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--primary-color-rgb), 0.2);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-color-dark);
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .registration-container {
                margin: 100px 1rem 50px;
                padding: 1.5rem;
            }

            .registration-container h2 {
                font-size: 1.75rem;
            }

            .form-group input,
            .form-group select {
                padding: 0.75rem;
            }

            .btn-register {
                padding: 0.875rem;
                font-size: 1rem;
            }
        }

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            margin-top: 0.5rem;
            border-radius: 2px;
            background-color: #e1e1e1;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .strength-weak { background-color: #e53e3e; width: 33.33%; }
        .strength-medium { background-color: #ecc94b; width: 66.66%; }
        .strength-strong { background-color: #38a169; width: 100%; }
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
                <div class="password-strength">
                    <div class="password-strength-bar"></div>
                </div>
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

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('haslo');
        const strengthBar = document.querySelector('.password-strength-bar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 1;
            
            // Contains number
            if (/\d/.test(password)) strength += 1;
            
            // Contains special character
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 1;
            
            // Contains uppercase and lowercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
            
            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });

        // Form validation
        const form = document.querySelector('.registration-form');
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('haslo').value;
            const confirmPassword = document.getElementById('powtorz_haslo').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Hasła nie są identyczne!');
            }
        });
    </script>
</body>
</html> 