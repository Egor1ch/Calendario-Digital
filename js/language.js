// Diccionario de traducciones
const translations = {
    es: {
        // General
        title: "Calendario Digital",
        calendar: "Calendario Digital",
        createEvent: "Crear evento",
        today: "Hoy",
        
        // Vistas
        year: "Año",
        month: "Mes",
        week: "Semana",
        day: "Día",
        hour: "Hora",
        
        // Categorías
        myCalendars: "Categorías",
        events: "Eventos",
        tasks: "Tareas",
        parties: "Fiestas",
        personal: "Personal",
        
        // Días de la semana
        mon: "Lun",
        tue: "Mar",
        wed: "Mié",
        thu: "Jue",
        fri: "Vie",
        sat: "Sáb",
        sun: "Dom",
        
        // Mini calendario
        miniDays: ["L", "M", "X", "J", "V", "S", "D"],
        
        // Meses
        months: [
            "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
        ],
        
        // Modal de eventos
        newEvent: "Nuevo Evento",
        editEvent: "Editar evento",
        title_field: "Título",
        date: "Fecha",
        time: "Hora",
        category: "Categorías",
        description: "Descripción",
        save: "Guardar",
        delete: "Eliminar",
        
        // Categorías de eventos
        event: "Evento",
        task: "Tarea",
        party: "Fiesta",
        personalEvent: "Personal",
        
        // Indicador de hoy
        todayIndicator: "Hoy"
    },
    en: {
        // General
        title: "Digital Calendar",
        calendar: "Digital Calendar",
        createEvent: "Create event",
        today: "Today",
        
        // Vistas
        year: "Year",
        month: "Month",
        week: "Week",
        day: "Day",
        hour: "Hour",
        
        // Categorías
        myCalendars: "Categories",
        events: "Events",
        tasks: "Tasks",
        parties: "Parties",
        personal: "Personal",
        
        // Días de la semana
        mon: "Mon",
        tue: "Tue",
        wed: "Wed",
        thu: "Thu",
        fri: "Fri",
        sat: "Sat",
        sun: "Sun",
        
        // Mini calendario
        miniDays: ["M", "T", "W", "T", "F", "S", "S"],
        
        // Meses
        months: [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        
        // Modal de eventos
        newEvent: "New Event",
        editEvent: "Edit Event",
        title_field: "Title",
        date: "Date",
        time: "Time",
        category: "Category",
        description: "Description",
        save: "Save",
        delete: "Delete",
        
        // Categorías de eventos
        event: "Event",
        task: "Task",
        party: "Party",
        personalEvent: "Personal",
        
        // Indicador de hoy
        todayIndicator: "Today"
    }
};

// Idioma actual
let currentLanguage = 'es';

// Inicialización cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    // Cargar preferencia de idioma guardada
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage) {
        currentLanguage = savedLanguage;
        document.getElementById('language-toggle').checked = savedLanguage === 'en';
    }
    
    // Event listener para cambio de idioma
    document.getElementById('language-toggle').addEventListener('change', (e) => {
        currentLanguage = e.target.checked ? 'en' : 'es';
        localStorage.setItem('language', currentLanguage);
        updateInterface();
    });
    
    // Actualizar interfaz con el idioma actual
    updateInterface();
});

// Función para actualizar todos los textos de la interfaz
function updateInterface() {
    // Actualizar título de la página
    document.title = translations[currentLanguage].title;
    
    // Actualizar textos en la barra lateral
    document.querySelector('.logo h1').textContent = translations[currentLanguage].calendar;
    document.querySelector('.create-event-btn span').textContent = translations[currentLanguage].createEvent;
    
    // Actualizar botones de vista
    document.querySelector('button[data-view="year"] span').textContent = translations[currentLanguage].year;
    document.querySelector('button[data-view="month"] span').textContent = translations[currentLanguage].month;
    document.querySelector('button[data-view="week"] span').textContent = translations[currentLanguage].week;
    document.querySelector('button[data-view="day"] span').textContent = translations[currentLanguage].day;
    
    // Actualizar categorías
    document.querySelector('.section-header h3').textContent = translations[currentLanguage].myCalendars;
    document.querySelector('label[for="cat-events"] span:last-child').textContent = translations[currentLanguage].events;
    document.querySelector('label[for="cat-tasks"] span:last-child').textContent = translations[currentLanguage].tasks;
    document.querySelector('label[for="cat-parties"] span:last-child').textContent = translations[currentLanguage].parties;
    document.querySelector('label[for="cat-personal"] span:last-child').textContent = translations[currentLanguage].personal;
    
    // Actualizar botón de hoy
    document.getElementById('today-btn').textContent = translations[currentLanguage].today;
    
    // Actualizar días de la semana en vista mensual
    const weekdays = document.querySelectorAll('.weekdays div');
    if (weekdays.length === 7) {
        weekdays[0].textContent = translations[currentLanguage].mon;
        weekdays[1].textContent = translations[currentLanguage].tue;
        weekdays[2].textContent = translations[currentLanguage].wed;
        weekdays[3].textContent = translations[currentLanguage].thu;
        weekdays[4].textContent = translations[currentLanguage].fri;
        weekdays[5].textContent = translations[currentLanguage].sat;
        weekdays[6].textContent = translations[currentLanguage].sun;
    }
    
    // Actualizar modal de eventos
    const modalTitle = document.getElementById('modal-title');
    if (modalTitle) {
        modalTitle.textContent = translations[currentLanguage].newEvent;
        document.querySelector('label[for="event-title"]').textContent = translations[currentLanguage].title_field;
        document.querySelector('label[for="event-date"]').textContent = translations[currentLanguage].date;
        document.querySelector('label[for="event-time"]').textContent = translations[currentLanguage].time;
        document.querySelector('label[for="event-category"]').textContent = translations[currentLanguage].category;
        document.querySelector('label[for="event-description"]').textContent = translations[currentLanguage].description;
        document.querySelector('.form-actions button[type="submit"]').textContent = translations[currentLanguage].save;
        document.querySelector('.delete-btn').textContent = translations[currentLanguage].delete;
    }
    
    // Actualizar opciones en el select de categorías
    const categorySelect = document.getElementById('event-category');
    if (categorySelect && categorySelect.options.length >= 4) {
        categorySelect.options[0].textContent = translations[currentLanguage].event;
        categorySelect.options[1].textContent = translations[currentLanguage].task;
        categorySelect.options[2].textContent = translations[currentLanguage].party;
        categorySelect.options[3].textContent = translations[currentLanguage].personalEvent;
    }
    
    // Si estamos en la vista diaria y hay un indicador de "Hoy"
    const todayIndicator = document.querySelector('.today-indicator');
    if (todayIndicator) {
        todayIndicator.textContent = translations[currentLanguage].todayIndicator;
    }
    
    // Actualizar encabezado con el mes/año actual
    updateCalendarHeader();
    
    // Actualizar mini calendario
    renderMiniCalendar();
}

// Función para actualizar el encabezado del calendario
function updateCalendarHeader() {
    const headerElement = document.getElementById('current-month-year');
    if (!headerElement) return;
    
    if (currentView === 'year') {
        headerElement.textContent = `${translations[currentLanguage].year} ${currentDate.getFullYear()}`;
    } else {
        // Obtener el nombre del mes y capitalizarlo
        const month = translations[currentLanguage].months[currentDate.getMonth()];
        const capitalizedMonth = month.charAt(0).toUpperCase() + month.slice(1);
        headerElement.textContent = `${capitalizedMonth} ${currentDate.getFullYear()}`;
    }
}

// Función para capitalizar un texto (primera letra en mayúscula)
function capitalizeFirstLetter(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
}

// Función auxiliar para obtener traducciones
function getTranslation(key, capitalize = false) {
    const translation = translations[currentLanguage][key] || key;
    return capitalize ? capitalizeFirstLetter(translation) : translation;
}

// Función para obtener nombres de meses capitalizados
function getMonthName(monthIndex, capitalize = true) {
    const month = translations[currentLanguage].months[monthIndex];
    return capitalize ? capitalizeFirstLetter(month) : month;
}

// Función para obtener formatos de fecha localizados
function getLocalizedDate(date, options) {
    const locale = currentLanguage === 'es' ? 'es-ES' : 'en-US';
    return date.toLocaleDateString(locale, options);
}
