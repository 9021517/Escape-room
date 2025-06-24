// Игровые переменные
let gameState = {
    sleutelGevonden: false,
    notitieGevonden: false,
    deurCode: "4219",
    huidigeVraag: null,
    spelActief: true
};

// Конфигурация комнаты
const ROOM_ID = 2;
const VRAAG_IDS = {
    KAST: 1,
    KLUIS: 2, 
    DEUR: 3
};

// DOM elementen
const elementen = {
    kast: document.getElementById('kast'),
    kluis: document.getElementById('kluis'),
    deur: document.getElementById('deur'),
    vraagModal: document.getElementById('vraag-modal'),
    vraagTekst: document.getElementById('vraag-tekst'),
    hintTekst: document.getElementById('hint-tekst'),
    antwoordInput: document.getElementById('antwoord-input'),
    bevestigBtn: document.getElementById('bevestig-btn'),
    hintBtn: document.getElementById('hint-btn'),
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
    
    if (tijdOver <= 60) {
        elementen.timer.classList.add('timer-warning');
    }
    
    if (tijdOver <= 0) {
        gameOver();
    }
}, 1000);


// Функция загрузки вопроса из базы данных
async function laadVraagUitDatabase(questionId, roomId) {
    try {
        toonStatusBericht('Loading vraag...', 'info');
        
        const response = await fetch(`get-question.php?id=${questionId}&roomId=${roomId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.data) {
            return {
                id: data.data.id,
                tekst: data.data.question,
                antwoord: data.data.answer,
                hint: data.data.hint,
                beloning: bepaalBeloning(questionId) // Определяем награду по ID вопроса
            };
        } else {
            throw new Error(data.message || 'Onbekende fout bij het laden van de vraag');
        }
    } catch (error) {
        console.error('Fout bij het laden van vraag:', error);
        toonStatusBericht(`❌ Fout bij laden vraag: ${error.message}`, 'error');
        return null;
    }
}

// Функция определения награды по ID вопроса
function bepaalBeloning(questionId) {
    switch(questionId) {
        case VRAAG_IDS.KAST:
            return 'sleutel';
        case VRAAG_IDS.KLUIS:
            return 'notitie';
        case VRAAG_IDS.DEUR:
            return 'ontsnapping';
        default:
            return null;
    }
}

// Функция показа статусного сообщения
function toonStatusBericht(bericht, type = 'info') {
    const bestaandBericht = document.querySelector('.status-message');
    if (bestaandBericht) {
        bestaandBericht.remove();
    }
    
    const statusDiv = document.createElement('div');
    statusDiv.className = 'status-message';
    statusDiv.textContent = bericht;
    
    if (type === 'success') {
        statusDiv.style.borderColor = '#00ff00';
        statusDiv.style.color = '#00ff00';
    } else if (type === 'error') {
        statusDiv.style.borderColor = '#ff0000';
        statusDiv.style.color = '#ff0000';
    }
    
    document.body.appendChild(statusDiv);
    
    setTimeout(() => {
        if (statusDiv.parentNode) {
            statusDiv.remove();
        }
    }, 3000);
}

// Функция показа вопроса (теперь загружает из БД)
async function toonVraag(vraagId) {
    // Показываем индикатор загрузки
    elementen.vraagModal.classList.remove('verborgen');
    elementen.vraagTekst.textContent = 'Vraag wordt geladen...';
    elementen.bevestigBtn.disabled = true;
    
    const vraag = await laadVraagUitDatabase(vraagId, ROOM_ID);
    
    if (!vraag) {
        sluitVraagModal();
        return;
    }
    
    gameState.huidigeVraag = vraag;
    elementen.vraagTekst.textContent = vraag.tekst;
    elementen.hintTekst.classList.add('verborgen');
    elementen.antwoordInput.value = '';
    elementen.bevestigBtn.disabled = false;
    elementen.antwoordInput.focus();
}

// Функция показа хинта
function toonHint() {
    if (!gameState.huidigeVraag || !gameState.huidigeVraag.hint) {
        toonStatusBericht('Geen hint beschikbaar voor deze vraag.', 'info');
        return;
    }
    
    elementen.hintTekst.textContent = `💡 Hint: ${gameState.huidigeVraag.hint}`;
    elementen.hintTekst.classList.remove('verborgen');
    toonStatusBericht('Hint onthuld!', 'info');
}

// Функция проверки ответа (ZONDER FLASH EFFECTEN)
function controleerAntwoord() {
    if (!gameState.huidigeVraag) return;
    
    const gegevenAntwoord = elementen.antwoordInput.value.trim().toLowerCase();
    const juisteAntwoord = gameState.huidigeVraag.antwoord.toLowerCase();
    
    if (gegevenAntwoord === juisteAntwoord) {
        verwerkJuistAntwoord(gameState.huidigeVraag);
        sluitVraagModal();
    } else {
        toonStatusBericht('❌ Fout antwoord! Probeer het opnieuw.', 'error');
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
            toonStatusBericht('🔑 Geweldig! Je hebt een sleutel gevonden!', 'success');
            break;

        case 'notitie':
            gameState.notitieGevonden = true;
            elementen.notitieIcon.classList.remove('verborgen');
            elementen.notitieSlot.classList.add('heeft-item');
            toonStatusBericht('📋 Perfect! Je hebt een belangrijke notitie gevonden!', 'success');
            break;

        case 'ontsnapping':
            winSpel();
            break;
    }
}

// Закрытие модального окна вопроса
function sluitVraagModal() {
    elementen.vraagModal.classList.add('verborgen');
    elementen.bevestigBtn.disabled = false;
    gameState.huidigeVraag = null;
}

// Обработчики событий для объектов
elementen.kast.addEventListener('click', () => {
    if (gameState.sleutelGevonden) {
        toonStatusBericht('Je hebt de sleutel al uit deze kast gehaald.', 'info');
        return;
    }
    toonVraag(VRAAG_IDS.KAST);
});

elementen.kluis.addEventListener('click', () => {
    if (gameState.notitieGevonden) {
        toonStatusBericht('Je hebt de notitie al uit deze kluis gehaald.', 'info');
        return;
    }
    toonVraag(VRAAG_IDS.KLUIS);
});

elementen.deur.addEventListener('click', () => {
    if (!gameState.sleutelGevonden) {
        toonStatusBericht('🔒 De deur is op slot! Je hebt een sleutel nodig.', 'error');
        return;
    }
    
    if (!gameState.notitieGevonden) {
        toonStatusBericht('🔑 Je hebt de sleutel, maar er is nog een code nodig!', 'error');
        return;
    }
    
    toonVraag(VRAAG_IDS.DEUR);
});

// Обработчики для модального окна вопроса
elementen.bevestigBtn.addEventListener('click', controleerAntwoord);
elementen.hintBtn.addEventListener('click', toonHint);
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
        toonStatusBericht('Je hebt nog geen notitie gevonden.', 'info');
    }
});

elementen.sleutelSlot.addEventListener('click', () => {
    if (gameState.sleutelGevonden) {
        toonStatusBericht('🔑 Je hebt de sleutel! Gebruik hem om de deur te openen.', 'success');
    } else {
        toonStatusBericht('Je hebt nog geen sleutel gevonden.', 'info');
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
    
    toonStatusBericht('⏰ Tijd is op! Game Over!', 'error');
    
    setTimeout(() => {
        // Переход на страницу поражения
        window.location.href = '../kamer_1/losevd.html';
    }, 2000);
}

// Функция победы
function winSpel() {
    gameState.spelActief = false;
    clearInterval(timerInterval);
    
    toonStatusBericht('🎉 Gefeliciteerd! Je bent ontsnapt!', 'success');
    
    setTimeout(() => {
        // Переход на страницу победы
        window.location.href = '../kamer_1/winvd.html';
    }, 2000);
}

// Функция проверки подключения к базе данных
async function controleerDatabaseVerbinding() {
    try {
        const response = await fetch('get-question.php?id=1&roomId=2');
        const data = await response.json();
        
        if (data.success) {
            console.log('✅ Database verbinding OK');
            return true;
        } else {
            console.warn('⚠️ Database verbinding probleem:', data.message);
            return false;
        }
    } catch (error) {
        console.error('❌ Database verbinding fout:', error);
        toonStatusBericht('⚠️ Probleem met database verbinding. Sommige functies werken mogelijk niet.', 'error');
        return false;
    }
}

// Инициализация игры
async function initializeGame() {
    console.log('🎮 Escape Room - Kamer 2 geladen');
    console.log('🎯 Doel: Vind de sleutel, ontdek de code, en ontsnap!');
    
    // Проверяем подключение к базе данных
    await controleerDatabaseVerbinding();
    
    toonStatusBericht('🏥 Welkom in de medische afdeling! Zoek naar aanwijzingen...', 'info');
}

// Запуск игры
initializeGame();