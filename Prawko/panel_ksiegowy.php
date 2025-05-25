<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['rola']) || $_SESSION['rola'] !== 'ksiegowy') { header('Location: login.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Księgowej</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(44,62,80,0.08);
            padding: 32px 40px 40px 40px;
        }
        h2 {
            text-align: center;
            color: #1565c0;
            margin-bottom: 32px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 14px 10px;
            text-align: left;
        }
        th {
            background: #e3f0ff;
            color: #1565c0;
            font-weight: 600;
            border-bottom: 2px solid #b3d1f7;
        }
        tr:nth-child(even) {
            background: #f7fbff;
        }
        tr:hover {
            background: #e3f0ff;
        }
        .status {
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 0.98em;
            font-weight: 500;
            display: inline-block;
        }
        .status.Opłacony {
            background: #e0f7e9;
            color: #2ecc71;
        }
        .status.Oczekujący {
            background: #fffbe0;
            color: #f1c40f;
        }
        .status.Anulowany {
            background: #ffe0e0;
            color: #e74c3c;
        }
    </style>
</head>
<body>
<div class="container">
<?php
$result = $conn->query("SELECT p.*, u.imie, u.nazwisko FROM platnosci p JOIN uzytkownicy u ON p.uzytkownik_id = u.id ORDER BY p.data_platnosci DESC");
echo "<h2>Wszystkie wpłaty</h2>";
echo "<table><tr><th>Imię</th><th>Nazwisko</th><th>Kwota</th><th>Status</th><th>Data</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['imie']}</td><td>{$row['nazwisko']}</td><td>{$row['kwota']} zł</td><td><span class='status {$row['status']}'>".htmlspecialchars($row['status'])."</span></td><td>{$row['data_platnosci']}</td></tr>";
}
echo "</table>";
?>
</div>
</body>
</html> 