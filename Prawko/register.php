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
        .form-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            padding: 2.5rem 2.2rem;
        }
        .registration-form {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 0.4rem;
        }
        .form-group {
            margin-bottom: 1.3rem;
        }
        .form-control {
            width: 100%;
            padding: 1.1rem;
            border: 2px solid #2366b8 !important;
            border-radius: 7px;
            font-size: 1.08rem;
            background: #eaf0fb !important;
            transition: border-color 0.2s, box-shadow 0.2s;
            color: #22304a !important;
            box-shadow: 0 2px 10px rgba(44,62,80,0.08) !important;
        }
        .form-control:focus {
            border-color: #2366b8 !important;
            outline: none;
            background: #fff !important;
            box-shadow: 0 0 0 3px #b5d6ff, 0 2px 10px rgba(44,62,80,0.10) !important;
        }
        .form-control::placeholder {
            color: #6a7ba2 !important;
            opacity: 1;
        }
        .btn.primary {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 6px;
        }
    </style>
</head>
<body style="background: #eaf0fb;">
    <?php include 'header.php'; ?>

    <main>
        <div class="form-container">
            <h1>Rejestracja</h1>

            <?php
            if (isset($_SESSION['error_messages'])) {
                echo '<div class="error-messages">';
                foreach ($_SESSION['error_messages'] as $error) {
                    echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
                }
                echo '</div>';
                unset($_SESSION['error_messages']);
            }

            if (isset($_SESSION['success_message'])) {
                echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                unset($_SESSION['success_message']);
            }

            $form_data = $_SESSION['form_data'] ?? [];
            unset($_SESSION['form_data']);
            ?>

            <form action="register_handler.php" method="POST" class="registration-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="haslo">Hasło:</label>
                    <input type="password" id="haslo" name="haslo" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="powtorz_haslo">Powtórz hasło:</label>
                    <input type="password" id="powtorz_haslo" name="powtorz_haslo" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="imie">Imię:</label>
                    <input type="text" id="imie" name="imie" value="<?php echo htmlspecialchars($form_data['imie'] ?? ''); ?>" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="nazwisko">Nazwisko:</label>
                    <input type="text" id="nazwisko" name="nazwisko" value="<?php echo htmlspecialchars($form_data['nazwisko'] ?? ''); ?>" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($form_data['login'] ?? ''); ?>" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="data_urodzenia">Data urodzenia:</label>
                    <input type="date" id="data_urodzenia" name="data_urodzenia" value="<?php echo htmlspecialchars($form_data['data_urodzenia'] ?? ''); ?>" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <div class="form-group">
                    <label for="kategoria_prawa_jazdy">Kategoria prawa jazdy:</label>
                    <select id="kategoria_prawa_jazdy" name="kategoria_prawa_jazdy" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                        <option value="">Wybierz kategorię</option>
                        <option value="A" <?php echo (isset($form_data['kategoria_prawa_jazdy']) && $form_data['kategoria_prawa_jazdy'] == 'A') ? 'selected' : ''; ?>>A - Motocykl</option>
                        <option value="B" <?php echo (isset($form_data['kategoria_prawa_jazdy']) && $form_data['kategoria_prawa_jazdy'] == 'B') ? 'selected' : ''; ?>>B - Samochód osobowy</option>
                        <option value="C" <?php echo (isset($form_data['kategoria_prawa_jazdy']) && $form_data['kategoria_prawa_jazdy'] == 'C') ? 'selected' : ''; ?>>C - Samochód ciężarowy</option>
                        <option value="D" <?php echo (isset($form_data['kategoria_prawa_jazdy']) && $form_data['kategoria_prawa_jazdy'] == 'D') ? 'selected' : ''; ?>>D - Autobus</option>
                    </select>
                </div>

                <div class="form-group instructor-section">
                    <label>
                        <input type="checkbox" name="is_instructor" id="is_instructor" value="1"> Chcę założyć konto instruktora
                    </label>
                    <div id="instructor_fields" style="display: none;">
                        <label for="instructor_password_input">Hasło weryfikacyjne instruktora:</label>
                        <input type="password" id="instructor_password_input" name="instructor_password" class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                        <label for="instructor_categories_group">Kategorie uprawnień instruktora:</label>
                        <div id="instructor_categories_group" class="checkbox-group">
                            <label><input type="checkbox" name="instructor_categories[]" id="cat_a" value="A"> Kategoria A</label>
                            <label><input type="checkbox" name="instructor_categories[]" id="cat_b" value="B"> Kategoria B</label>
                            <label><input type="checkbox" name="instructor_categories[]" id="cat_c" value="C"> Kategoria C</label>
                            <label><input type="checkbox" name="instructor_categories[]" id="cat_d" value="D"> Kategoria D</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefon">Telefon:</label>
                    <input type="tel" id="telefon" name="telefon" value="<?php echo htmlspecialchars($form_data['telefon'] ?? ''); ?>" pattern="[0-9]{9}" placeholder="123456789" required class="form-control" style="background:#eaf0fb;border:2px solid #2366b8;color:#22304a;border-radius:7px;padding:1.1rem;font-size:1.08rem;box-shadow:0 2px 10px rgba(44,62,80,0.08);">
                </div>

                <button type="submit" class="btn primary" style="background:#2366b8;color:#fff;font-size:1.15rem;font-weight:700;border-radius:7px;border:none;width:100%;padding:1.1rem;margin-top:1.2rem;box-shadow:0 2px 10px rgba(44,62,80,0.10);">Zarejestruj się</button>
            </form>

            <p class="text-center">
                Masz już konto? <a href="login.php">Zaloguj się</a>
            </p>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="contact-info">
                <h4>Kontakt</h4>
                <p>Telefon: +48 123 456 789</p>
                <p>Email: biuro@linianauka.pl</p>
                <p>Adres: ul. Przykładowa 123, Jaśienica</p>
            </div>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="copyright">
                <p>&copy; 2024 Linia Nauka Jazdy. Wszystkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('is_instructor').addEventListener('change', function() {
            document.getElementById('instructor_fields').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</body>
</html>
