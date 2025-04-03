// Variables globales para los eventos
let events = [];
let currentEditingEvent = null;

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    // Cargar eventos desde localStorage
    loadEvents();
    
    // Event listener para botón de crear evento desde la barra lateral
    document.getElementById('create-event-sidebar').addEventListener('click', () => {
        showEventModal();
    });
    
    // Event listener para cerrar modal
    document.querySelector('.close-modal').addEventListener('click', () => {
        hideEventModal();
    });
    
    // Event listener para envío del formulario
    document.getElementById('event-form').addEventListener('submit', handleEventSubmit);
    
    // Event listener para botón de eliminar
    document.getElementById('delete-btn').addEventListener('click', deleteEvent);
    
    // Event listener para filtros de categoría
    setupCategoryFilters();
});

// Configurar los filtros de categoría
function setupCategoryFilters() {
    const categoryCheckboxes = document.querySelectorAll('.category-item input[type="checkbox"]');
    
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            filterEventsByCategory();
        });
    });
}

// Función para filtrar eventos por categoría (actualizada para incluir vista de año)
function filterEventsByCategory() {
    const checkedCategories = Array.from(document.querySelectorAll('.category-item input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.id.replace('cat-', ''));
    
    // Para eventos en la vista mensual
    document.querySelectorAll('.event').forEach(eventEl => {
        const category = eventEl.dataset.category;
        const matchCategory = (category === 'event' && checkedCategories.includes('events')) ||
                             (category === 'task' && checkedCategories.includes('tasks')) ||
                             (category === 'party' && checkedCategories.includes('parties')) ||
                             (category === 'personal' && checkedCategories.includes('personal'));
        
        eventEl.style.display = matchCategory ? 'block' : 'none';
    });
    
    // Si estamos en vista anual, actualizar los indicadores de eventos
    if (currentView === 'year') {
        // Volver a renderizar la vista anual para reflejar el filtrado
        renderYearView();
    }
}

// Obtener el nombre de categoría a partir del ID
function getCategoryFromId(id) {
    switch(id) {
        case 'events':
            return 'event';
        case 'tasks':
            return 'task';
        case 'parties':
            return 'party';
        case 'personal':
            return 'personal';
        default:
            return '';
    }
}

// Mostrar u ocultar eventos según la categoría
function toggleCategoryVisibility(category, isVisible) {
    document.querySelectorAll(`.event[data-category="${category}"]`).forEach(event => {
        event.style.display = isVisible ? 'block' : 'none';
    });
}

// Cargar eventos desde localStorage
function loadEvents() {
    const storedEvents = localStorage.getItem('calendarEvents');
    if (storedEvents) {
        events = JSON.parse(storedEvents);
        
        // Convertir strings de fecha a objetos Date
        events.forEach(event => {
            event.date = new Date(event.date);
        });
        
        renderEvents();
    }
}

// Guardar eventos en localStorage
function saveEvents() {
    localStorage.setItem('calendarEvents', JSON.stringify(events));
}

// Renderizar eventos en el calendario
function renderEvents() {
    // Limpiar eventos existentes
    document.querySelectorAll('.events').forEach(container => {
        container.innerHTML = '';
    });
    
    // Agregar eventos al calendario
    events.forEach(event => {
        const dateStr = formatDate(event.date);
        const dayElement = document.querySelector(`.day[data-date="${dateStr}"]`);
        
        if (dayElement) {
            const eventsContainer = dayElement.querySelector('.events');
            const eventElement = document.createElement('div');
            eventElement.classList.add('event');
            eventElement.dataset.id = event.id;
            eventElement.dataset.category = event.category;
            eventElement.textContent = event.title;
            
            eventElement.addEventListener('click', (e) => {
                e.stopPropagation();
                showEventModal(event);
            });
            
            eventsContainer.appendChild(eventElement);
        }
    });
}

// Renderizar eventos en la vista semanal mejorada
function renderEventsInWeekView() {
    // Limpiar eventos existentes
    document.querySelectorAll('.week-event').forEach(el => el.remove());
    
    events.forEach(event => {
        const dateStr = formatDate(event.date);
        const hour = event.date.getHours();
        const minutes = event.date.getMinutes();
        const dayColumn = document.querySelector(`.day-column[data-date="${dateStr}"]`);
        
        if (dayColumn) {
            const hourCell = dayColumn.querySelector(`.hour-cell[data-hour="${hour}"]`);
            
            if (hourCell) {
                const eventElement = document.createElement('div');
                eventElement.classList.add('event', 'week-event');
                eventElement.dataset.id = event.id;
                eventElement.dataset.category = event.category;
                
                // Calcular altura y posición según duración
                let durationMinutes = 60; // Por defecto, 1 hora
                if (event.endTime) {
                    const endTime = new Date(event.endTime);
                    durationMinutes = (endTime - event.date) / (1000 * 60);
                }
                
                // Ajustar posición vertical basada en minutos
                const topOffset = (minutes / 60) * 100;
                eventElement.style.top = `${topOffset}%`;
                
                // Ajustar altura basada en duración (máximo hasta el final de la hora)
                const heightPercentage = Math.min((durationMinutes / 60) * 100, 100 - topOffset);
                eventElement.style.height = `${heightPercentage}%`;
                
                // Formatear hora
                const timeStr = event.date.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                
                // Contenido del evento
                eventElement.innerHTML = `
                    <div class="event-time">${timeStr}</div>
                    <div class="event-title">${event.title}</div>
                `;
                
                eventElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showEventModal(event);
                });
                
                hourCell.appendChild(eventElement);
            }
        }
    });
}

// Renderizar eventos en la vista diaria mejorada
function renderEventsInDayView() {
    // Limpiar eventos existentes
    document.querySelectorAll('.day-event').forEach(el => el.remove());
    
    const dateStr = formatDate(currentDate);
    const contentColumn = document.querySelector('.day-content-column');
    
    if (!contentColumn) return;
    
    // Agrupar eventos por hora
    const eventsByHour = {};
    
    events.forEach(event => {
        if (formatDate(event.date) === dateStr) {
            const hour = event.date.getHours();
            if (!eventsByHour[hour]) {
                eventsByHour[hour] = [];
            }
            eventsByHour[hour].push(event);
        }
    });
    
    // Renderizar eventos agrupados por hora
    for (const hour in eventsByHour) {
        const hourContent = contentColumn.querySelector(`.day-hour-content[data-hour="${hour}"]`);
        
        if (hourContent) {
            eventsByHour[hour].forEach(event => {
                const eventElement = document.createElement('div');
                eventElement.classList.add('event', 'day-event');
                eventElement.dataset.id = event.id;
                eventElement.dataset.category = event.category;
                
                // Formatear hora
                const timeStr = event.date.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
                
                // Contenido del evento
                eventElement.innerHTML = `
                    <div class="event-time">${timeStr}</div>
                    <div class="event-title">${event.title}</div>
                `;
                
                eventElement.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showEventModal(event);
                });
                
                hourContent.appendChild(eventElement);
            });
        }
    }
}

// Mostrar modal para crear/editar evento
function showEventModal(event = null, date = new Date()) {
    const modal = document.getElementById('event-modal');
    const form = document.getElementById('event-form');
    const deleteBtn = document.getElementById('delete-btn');
    const modalTitle = document.getElementById('modal-title');
    
    // Limpiar formulario
    form.reset();
    
    // Si se está editando un evento existente
    if (event) {
        currentEditingEvent = event;
        modalTitle.textContent = 'Editar evento';
        
        document.getElementById('event-id').value = event.id;
        document.getElementById('event-title').value = event.title;
        
        // Formatear fecha para el input type="date"
        const dateStr = formatDate(event.date);
        document.getElementById('event-date').value = dateStr;
        
        // Formatear hora para el input type="time"
        const hours = String(event.date.getHours()).padStart(2, '0');
        const minutes = String(event.date.getMinutes()).padStart(2, '0');
        document.getElementById('event-time').value = `${hours}:${minutes}`;
        
        document.getElementById('event-category').value = event.category;
        document.getElementById('event-description').value = event.description || '';
        
        deleteBtn.style.display = 'block';
    } else {
        // Nuevo evento
        currentEditingEvent = null;
        modalTitle.textContent = 'Nuevo evento';
        
        document.getElementById('event-id').value = '';
        
        // Establecer la fecha seleccionada
        const dateStr = formatDate(date);
        document.getElementById('event-date').value = dateStr;
        
        // Si se proporciona hora, establecerla también
        if (date.getHours() || date.getMinutes()) {
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            document.getElementById('event-time').value = `${hours}:${minutes}`;
        }
        
        deleteBtn.style.display = 'none';
    }
    
    modal.style.display = 'flex';
}

// Ocultar modal
function hideEventModal() {
    const modal = document.getElementById('event-modal');
    modal.style.display = 'none';
    currentEditingEvent = null;
}

// Manejar envío del formulario
function handleEventSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const title = document.getElementById('event-title').value;
    const dateStr = document.getElementById('event-date').value;
    const timeStr = document.getElementById('event-time').value || '00:00';
    const category = document.getElementById('event-category').value;
    const description = document.getElementById('event-description').value;
    
    // Crear objeto Date
    const [year, month, day] = dateStr.split('-').map(num => parseInt(num));
    const [hours, minutes] = timeStr.split(':').map(num => parseInt(num));
    const date = new Date(year, month - 1, day, hours, minutes);
    
    if (currentEditingEvent) {
        // Actualizar evento existente
        currentEditingEvent.title = title;
        currentEditingEvent.date = date;
        currentEditingEvent.category = category;
        currentEditingEvent.description = description;
    } else {
        // Crear nuevo evento
        const newEvent = {
            id: Date.now().toString(),
            title,
            date,
            category,
            description
        };
        
        events.push(newEvent);
    }
    
    // Guardar y actualizar
    saveEvents();
    updateCalendar();
    hideEventModal();
}

// Eliminar evento
function deleteEvent() {
    if (currentEditingEvent) {
        events = events.filter(event => event.id !== currentEditingEvent.id);
        saveEvents();
        updateCalendar();
        hideEventModal();
    }
}
