<?php
require_once 'config.php';

// Array of required courses
$required_courses = [
    [
        'nazwa' => 'Kurs Instruktorski Podstawowy',
        'kategoria' => 'Instruktorzy',
        'opis' => 'Podstawowy kurs dla przyszłych instruktorów',
        'cena' => 5000.00
    ],
    [
        'nazwa' => 'Kurs Kierowcy Zawodowego',
        'kategoria' => 'Kierowcy Zawodowi',
        'opis' => 'Kurs kwalifikacji wstępnej dla kierowców zawodowych',
        'cena' => 3800.00
    ],
    [
        'nazwa' => 'Kwalifikacja Okresowa',
        'kategoria' => 'Kierowcy Zawodowi',
        'opis' => 'Szkolenie okresowe dla kierowców zawodowych',
        'cena' => 1200.00
    ],
    [
        'nazwa' => 'Kurs ADR',
        'kategoria' => 'Kierowcy Zawodowi',
        'opis' => 'Przewóz materiałów niebezpiecznych',
        'cena' => 1800.00
    ],
    [
        'nazwa' => 'Kurs Operatora Koparki',
        'kategoria' => 'Operatorzy Maszyn',
        'opis' => 'Kurs na operatora koparki',
        'cena' => 3000.00
    ],
    [
        'nazwa' => 'Kurs Operatora - Rozszerzony',
        'kategoria' => 'Operatorzy Maszyn',
        'opis' => 'Kurs na operatora - dwie specjalności',
        'cena' => 3800.00
    ],
    [
        'nazwa' => 'Kurs Operatora HDS',
        'kategoria' => 'Operatorzy Maszyn',
        'opis' => 'Kurs na operatora dźwigu HDS',
        'cena' => 2500.00
    ]
];

// Check and insert each course if it doesn't exist
foreach ($required_courses as $course) {
    $stmt = $conn->prepare("SELECT id FROM kursy WHERE nazwa = ? AND kategoria = ?");
    $stmt->bind_param("ss", $course['nazwa'], $course['kategoria']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO kursy (nazwa, kategoria, opis, cena) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sssd", $course['nazwa'], $course['kategoria'], $course['opis'], $course['cena']);
        
        if ($insert->execute()) {
            echo "Dodano kurs: " . $course['nazwa'] . "<br>";
        } else {
            echo "Błąd podczas dodawania kursu: " . $course['nazwa'] . "<br>";
        }
        $insert->close();
    } else {
        echo "Kurs już istnieje: " . $course['nazwa'] . "<br>";
    }
    $stmt->close();
}

$conn->close();
echo "<br><a href='check_courses.php'>Sprawdź dostępne kursy</a>";
?> 