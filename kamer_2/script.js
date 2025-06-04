// Игровые переменные
let gameState = {
    sleutelGevonden: false,
    notitieGevonden: false,
    deurCode: "4219",
    huidigeVraag: null,
    spelActief: true
};

// Вопросы для второй комнаты
const vragen = [
    {
id: 1,
roomId: 2,
object: "kast",
tekst: "Wat is de output van console.log(typeof []) in JavaScript?",
antwoord: "object",
beloning: "sleutel"
    },
    {
id: 2,
roomId: 2,
object: "kluis", 
tekst: "Hoe declareer je een variabele in JavaScript die niet kan worden gewijzigd?",
antwoord: "const",
beloning: "notitie"
    },
    {
id: 3,
roomId: 2,
object: "deur",
tekst: "Voer de deurcode in om te ontsnappen:",
antwoord: "4219",
beloning: "ontsnapping"
    }
];

// DOM elementen
const elementen = {
    kast: document.getElementById('kast'),
    kluis: document.getElementById('kluis'),
    deur: document.getElementById('deur'),
    vraagModal: document.getElementById('vraag-modal'),
    vraagTekst: document.getElementById('vraag-tekst'),
    antwoordInput: document.getElementById('antwoord-input'),
    bevestigBtn: document.getElementById('bevestig-btn'),
    annuleerBtn: document.getElementById('annuleer-btn'),
    notitieModal: document.getElementById('notitie-modal'),
    sluitNotitieBtn: document.getElementById('sluit-notitie-btn'),
    sleutelIcon: document.getElementById('sleutel-icon'),
    notitieIcon: document.getElementById('notitie-icon'),
    sleutelSlot: document.getElementById('sleutel-slot'),
    notitieSlot: document.getElementById('notitie-slot'),
    timer: document.getElementById('timer')
};

// Таймер
let tijdOver = 300; // 5 минут
const timerInterval = setInterval(() => {
    if (!gameState.spelActief) return;
    
    tijdOver--;
    const minuten = Math.floor(tijdOver / 60);
    const seconden = tijdOver % 60;
    elementen.timer.textContent = `${minuten}:${seconden.toString().padStart(2, '0')}`;
    
    // Предупреждение в последнюю минуту
    if (tijdOver <= 60) {
elementen.timer.classList.add('timer-warning');
    }
    
    if (tijdOver <= 0) {
gameOver();
    }
}, 1000);

// Функция показа вопроса
function toonVraag(vraagId) {
    const vraag = vragen.find(v => v.id === vraagId);
    if (!vraag) return;
    
    gameState.huidigeVraag = vraag;
    elementen.vraagTekst.textContent = vraag.tekst;
    elementen.antwoordInput.value = '';
    elementen.vraagModal.classList.remove('verborgen');
    elementen.antwoordInput.focus();
}

// Функция проверки ответа
function controleerAntwoord() {
    if (!gameState.huidigeVraag) return;
    
    const gegevenAntwoord = elementen.antwoordInput.value.trim().toLowerCase();
    const juisteAntwoord = gameState.huidigeVraag.antwoord.toLowerCase();
    
    if (gegevenAntwoord === juisteAntwoord) {
// Правильный ответ
document.body.classList.add('success-flash');
setTimeout(() => document.body.classList.remove('success-flash'), 500);

verwerkJuistAntwoord(gameState.huidigeVraag);
sluitVraagModal();
    } else {
// Неправильный ответ
document.body.classList.add('error-flash');
setTimeout(() => document.body.classList.remove('error-flash'), 500);

alert('❌ Fout antwoord! Probeer het opnieuw.');
elementen.antwoordInput.value = '';
elementen.antwoordInput.focus();
    }
}

// Обработка правильного ответа
function verwerkJuistAntwoord(vraag) {
    switch(vraag.beloning) {
case 'sleutel':
    gameState.sleutelGevonden = true;
    elementen.sleutelIcon.classList.remove('verborgen');
    elementen.sleutelSlot.classList.add('heeft-item');
    alert('Geweldig! Je hebt een sleutel gevonden in de medicijnkast!');
    break;
    
case 'notitie':
    gameState.notitieGevonden = true;
    elementen.notitieIcon.classList.remove('verborgen');
    elementen.notitieSlot.classList.add('heeft-item');
    alert('Perfect! Je hebt een belangrijke notitie gevonden in de kluis!');
    break;
    
case 'ontsnapping':
    winSpel();
    break;
    }
}

// Закрытие модального окна вопроса
function sluitVraagModal() {
    elementen.vraagModal.classList.add('verborgen');
    gameState.huidigeVraag = null;
}

// Обработчики событий для объектов
elementen.kast.addEventListener('click', () => {
    if (gameState.sleutelGevonden) {
alert('Je hebt de sleutel al uit deze kast gehaald.');
return;
    }
    toonVraag(1);
});

elementen.kluis.addEventListener('click', () => {
    if (gameState.notitieGevonden) {
alert('Je hebt de notitie al uit deze kluis gehaald.');
return;
    }
    toonVraag(2);
});

elementen.deur.addEventListener('click', () => {
    if (!gameState.sleutelGevonden) {
alert('De deur is op slot! Je hebt een sleutel nodig.');
return;
    }
    
    if (!gameState.notitieGevonden) {
alert('Je hebt de sleutel, maar er is nog een code nodig. Zoek naar aanwijzingen!');
return;
    }
    
    toonVraag(3);
});

// Обработчики для модального окна вопроса
elementen.bevestigBtn.addEventListener('click', controleerAntwoord);
elementen.annuleerBtn.addEventListener('click', sluitVraagModal);

// Enter для подтверждения ответа
elementen.antwoordInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
controleerAntwoord();
    }
});

// Обработчики для инвентаря
elementen.notitieSlot.addEventListener('click', () => {
    if (gameState.notitieGevonden) {
elementen.notitieModal.classList.remove('verborgen');
    } else {
alert('Je hebt nog geen notitie gevonden.');
    }
});

elementen.sleutelSlot.addEventListener('click', () => {
    if (gameState.sleutelGevonden) {
alert('Je hebt de sleutel! Gebruik hem om de deur te openen.');
    } else {
alert('Je hebt nog geen sleutel gevonden.');
    }
});

// Закрытие модального окна записки
elementen.sluitNotitieBtn.addEventListener('click', () => {
    elementen.notitieModal.classList.add('verborgen');
});

// Функция поражения
function gameOver() {
    gameState.spelActief = false;
    clearInterval(timerInterval);
    
    setTimeout(() => {
        // Переход на страницу поражения
        window.location.href = '../win-verlies/losevd.html';
    }, 1000);
}

// Функция победы
function gameWin() {
    gameState.spelActief = false;
    clearInterval(timerInterval);
    
    setTimeout(() => {
        // Переход на страницу победы
        window.location.href = '../win-verlies/winvd.html';
    }, 1000);
}

// Инициализация игры с проверкой изображений
function initializeGame() {
    console.log('Escape Room - Kamer 2 geladen');
    console.log('Doel: Vind de sleutel, ontdek de code, en ontsnap!');
    
    // Проверяем загрузку фонового изображения
    const testImg = new Image();
    testImg.onload = function() {
        console.log('Achtergrond afbeelding geladen');
    };
    testImg.onerror = function() {
        console.log('Achtergrond afbeelding niet gevonden, gebruikt fallback');
    };
    testImg.src = 'img/hospital-corridor.jpg';
}

// Функция перезапуска игры (для использования на страницах победы/поражения)
function restartGame() {
    // Возврат к основной игре
    window.location.href = '../homepagina/indexvit.html';
}

// Запуск игры
initializeGame();