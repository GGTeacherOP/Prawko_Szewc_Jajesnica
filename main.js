function showAvailableSlots() {
    const instructor = document.getElementById('instructor').value;
    const duration = parseInt(document.getElementById('duration').value);
    const date = document.getElementById('date').value;
    const slotsListDiv = document.getElementById('slots-list');
    const availableSlotsContainer = document.getElementById('available-slots');

    if (!instructor || !date) {
      alert('Proszę wybrać instruktora i datę.');
      return;
    }

    slotsListDiv.innerHTML = ''; // Czyszczenie poprzednich wyników

    // --- Definicja WSZYSTKICH możliwych godzin rozpoczęcia --- 
    const allPossibleStartTimes1h = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
    const allPossibleStartTimes2h = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00']; // Ostatnia jazda 2h zaczyna się o 16:00

    const allPossibleStartTimes = duration === 1 ? allPossibleStartTimes1h : allPossibleStartTimes2h;

    // --- Symulacja NIEDOSTĘPNYCH godzin (dla przykładu) --- 
    // W realnej aplikacji te dane pochodziłyby z serwera
    // Przykładowe niedostępne sloty dla instruktora "Robert Lewandowski" w dniu "2025-05-20"
    let unavailableTimes = [];
    if (instructor === 'Robert Lewandowski' && date === '2025-05-20') {
        if (duration === 1) unavailableTimes = ['10:00', '14:00'];
        if (duration === 2) unavailableTimes = ['09:00']; 
    } else if (instructor === 'Muhamed Kebsik' && date === '2025-05-17') {
        if (duration === 1) unavailableTimes = ['08:00', '09:00', '13:00'];
        if (duration === 2) unavailableTimes = ['12:00', '16:00'];
    }
    // Można dodać więcej logiki symulacji...

    // --- Wyświetlanie WSZYSTKICH terminów (dostępnych i niedostępnych) --- 
    if (allPossibleStartTimes.length > 0) {
      allPossibleStartTimes.forEach(startTime => {
        const isUnavailable = unavailableTimes.includes(startTime);

        const startHour = parseInt(startTime.split(':')[0]);
        const startMinute = parseInt(startTime.split(':')[1]);
        let endHour = startHour + duration;
        let endMinute = startMinute;
        
        const formattedStartTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
        const formattedEndTime = `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;
        const timeSlotString = `${formattedStartTime} - ${formattedEndTime}`;

        let slotElement;
        if (isUnavailable) {
          // Tworzenie nieklikalnego elementu dla niedostępnego terminu
          slotElement = document.createElement('span');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-time', 'unavailable-slot'); // Dodaj klasy dla stylizacji
        } else {
          // Tworzenie przycisku dla dostępnego terminu
          slotElement = document.createElement('button');
          slotElement.textContent = timeSlotString;
          slotElement.classList.add('slot-button'); // Użyj tej samej klasy co wcześniej lub nowej
          slotElement.onclick = function() { bookSlot(instructor, date, timeSlotString); };
        }
        slotsListDiv.appendChild(slotElement);
      });
      availableSlotsContainer.style.display = 'block';
    } else {
      // Ten warunek raczej nie zajdzie przy obecnej logice, ale zostawiam
      slotsListDiv.innerHTML = '<p>Brak zdefiniowanych godzin pracy.</p>'; 
      availableSlotsContainer.style.display = 'block'; 
    }
  }

  function bookSlot(instructor, date, timeSlot) {
    // Tutaj docelowo logika rezerwacji terminu (np. wysłanie zapytania do serwera)
    alert(`Zarezerwowano jazdę:
Instruktor: ${instructor}
Data: ${date}
Godzina: ${timeSlot}

(To jest tylko symulacja, termin nie został zapisany.)`);
    
    // Opcjonalnie: wyczyść formularz lub ukryj terminy po rezerwacji
    // document.getElementById('planning-form').reset();
    // document.getElementById('available-slots').style.display = 'none';
  }

  // Dodaj walidację daty - nie można wybrać daty z przeszłości
  const dateInput = document.getElementById('date');
  const today = new Date().toISOString().split('T')[0];
  dateInput.setAttribute('min', today);