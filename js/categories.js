// Variables globales para las categorías
let userCategories = [];

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    // Cargar categorías desde el servidor
    loadCategories();
    
    // Event listener para botón de añadir categoría
    document.getElementById('add-category-btn').addEventListener('click', () => {
        showCategoryModal();
    });
    
    // Event listener para cerrar modal de categoría
    document.querySelector('#category-modal .close-modal').addEventListener('click', () => {
        hideCategoryModal();
    });
    
    // Event listener para envío del formulario de categoría
    document.getElementById('category-form').addEventListener('submit', handleCategorySubmit);
    
    // Event listener para botón de eliminar categoría
    document.getElementById('delete-category-btn').addEventListener('click', deleteCategory);
});

// Cargar categorías desde el servidor
function loadCategories() {
    fetch('api/category_actions.php?action=getAll')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userCategories = data.categories;
                renderCategories();
                updateCategoryDropdown();
            } else {
                console.error('Error al cargar categorías:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al realizar la petición:', error);
        });
}

// Renderizar categorías en la lista del sidebar
function renderCategories() {
    const categoriesList = document.querySelector('.categories-list');
    
    // Mantener las categorías por defecto
    const defaultCategories = Array.from(categoriesList.querySelectorAll('.category-item.default-category'));
    
    // Eliminar solo las categorías personalizadas
    const customCategories = categoriesList.querySelectorAll('.category-item.custom-category');
    customCategories.forEach(category => category.remove());
    
    // Añadir las categorías personalizadas del usuario
    userCategories.forEach(category => {
        const categoryItem = document.createElement('div');
        categoryItem.classList.add('category-item', 'custom-category');
        
        const categoryId = `cat-custom-${category.id}`;
        categoryItem.innerHTML = `
            <input type="checkbox" id="${categoryId}" checked>
            <label for="${categoryId}">
                <span class="color-dot" style="background-color: ${category.color};"></span>
                <span>${category.nombre}</span>
            </label>
            <button class="edit-category-btn" data-id="${category.id}">
                <i class="fas fa-edit"></i>
            </button>
        `;
        
        categoriesList.appendChild(categoryItem);
        
        // Event listener para el checkbox de la categoría
        const checkbox = categoryItem.querySelector(`#${categoryId}`);
        checkbox.addEventListener('change', filterEventsByCategory);
        
        // Event listener para el botón de editar
        const editBtn = categoryItem.querySelector('.edit-category-btn');
        editBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const categoryId = e.currentTarget.dataset.id;
            const category = userCategories.find(cat => cat.id == categoryId);
            if (category) {
                showCategoryModal(category);
            }
        });
    });
}

// Actualizar el dropdown de categorías en el formulario de eventos
function updateCategoryDropdown() {
    const categorySelect = document.getElementById('event-category');
    
    // Guardar la selección actual
    const currentSelection = categorySelect.value;
    
    // Mantener solo las opciones por defecto
    const defaultOptions = Array.from(categorySelect.querySelectorAll('option:not(.custom-option)'));
    categorySelect.innerHTML = '';
    defaultOptions.forEach(option => categorySelect.appendChild(option));
    
    // Añadir las categorías personalizadas
    userCategories.forEach(category => {
        const option = document.createElement('option');
        option.value = `custom_${category.id}`;
        option.textContent = category.nombre;
        option.classList.add('custom-option');
        option.dataset.color = category.color;
        categorySelect.appendChild(option);
    });
    
    // Restaurar la selección si existe
    if (currentSelection && categorySelect.querySelector(`option[value="${currentSelection}"]`)) {
        categorySelect.value = currentSelection;
    }
}

// Mostrar modal para crear/editar categoría
function showCategoryModal(category = null) {
    const modal = document.getElementById('category-modal');
    const form = document.getElementById('category-form');
    const deleteBtn = document.getElementById('delete-category-btn');
    const modalTitle = modal.querySelector('h2');
    
    // Limpiar formulario
    form.reset();
    
    // Si se está editando una categoría existente
    if (category) {
        modalTitle.textContent = 'Editar Categoría';
        document.getElementById('category-id').value = category.id;
        document.getElementById('category-name').value = category.nombre;
        document.getElementById('category-color').value = category.color;
        deleteBtn.style.display = 'block';
    } else {
        // Nueva categoría
        modalTitle.textContent = 'Nueva Categoría';
        document.getElementById('category-id').value = '';
        document.getElementById('category-color').value = getRandomColor();
        deleteBtn.style.display = 'none';
    }
    
    modal.style.display = 'flex';
}

// Ocultar modal de categoría
function hideCategoryModal() {
    const modal = document.getElementById('category-modal');
    modal.style.display = 'none';
}

// Manejar envío del formulario de categoría
function handleCategorySubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const categoryId = document.getElementById('category-id').value;
    const categoryName = document.getElementById('category-name').value;
    const categoryColor = document.getElementById('category-color').value;
    
    // Validar campos
    if (!categoryName.trim()) {
        alert('Por favor, introduce un nombre para la categoría');
        return;
    }
    
    // Crear formData para enviar al servidor
    const formData = new FormData();
    
    if (categoryId) {
        // Actualizar categoría existente
        formData.append('action', 'update');
        formData.append('id', categoryId);
    } else {
        // Crear nueva categoría
        formData.append('action', 'create');
    }
    
    // Añadir datos de la categoría
    formData.append('nombre', categoryName);
    formData.append('color', categoryColor);
    
    // Enviar datos al servidor
    fetch('api/category_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (categoryId) {
                // Actualizar categoría en el array
                const index = userCategories.findIndex(cat => cat.id == categoryId);
                if (index !== -1) {
                    userCategories[index] = {
                        id: categoryId,
                        nombre: categoryName,
                        color: categoryColor
                    };
                }
            } else {
                // Añadir nueva categoría al array
                userCategories.push({
                    id: data.category.id,
                    nombre: categoryName,
                    color: categoryColor
                });
            }
            
            // Actualizar interfaz
            renderCategories();
            updateCategoryDropdown();
            
            // Actualizar eventos con esta categoría
            if (categoryId) {
                updateEventCategoryStyle(`custom_${categoryId}`, categoryColor);
            }
            
            hideCategoryModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la categoría. Inténtalo de nuevo.');
    });
}

// Actualizar estilo de eventos con una categoría específica
function updateEventCategoryStyle(categoryValue, color) {
    document.querySelectorAll(`.event[data-category="${categoryValue}"]`).forEach(eventEl => {
        eventEl.style.backgroundColor = color;
        // Ajustar color de texto según el brillo del fondo
        eventEl.style.color = isLightColor(color) ? 'black' : 'white';
    });
}

// Eliminar categoría
function deleteCategory() {
    const categoryId = document.getElementById('category-id').value;
    
    if (!categoryId) return;
    
    if (!confirm('¿Estás seguro de que deseas eliminar esta categoría? Los eventos asociados se cambiarán a la categoría "Evento".')) {
        return;
    }
    
    // Crear formData para enviar al servidor
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', categoryId);
    
    // Enviar petición al servidor
    fetch('api/category_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Eliminar categoría del array
            userCategories = userCategories.filter(cat => cat.id != categoryId);
            
            // Actualizar interfaz
            renderCategories();
            updateCategoryDropdown();
            
            // Volver a cargar eventos para actualizar las categorías
            if (typeof loadEvents === 'function') {
                loadEvents();
            }
            
            hideCategoryModal();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la categoría. Inténtalo de nuevo.');
    });
}

// Generar un color aleatorio para nuevas categorías
function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

// Determinar si un color es claro (para usar texto negro) u oscuro (para usar texto blanco)
function isLightColor(color) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    return brightness > 128;
}

// Obtener color para una categoría específica
function getCategoryColor(categoryValue) {
    if (categoryValue.startsWith('custom_')) {
        const categoryId = categoryValue.replace('custom_', '');
        const category = userCategories.find(cat => cat.id == categoryId);
        return category ? category.color : '#cccccc';
    }
    
    // Colores por defecto
    switch(categoryValue) {
        case 'event':
            return getComputedStyle(document.documentElement).getPropertyValue('--event-color').trim();
        case 'task':
            return getComputedStyle(document.documentElement).getPropertyValue('--task-color').trim();
        case 'party':
            return getComputedStyle(document.documentElement).getPropertyValue('--party-color').trim();
        case 'personal':
            return getComputedStyle(document.documentElement).getPropertyValue('--personal-color').trim();
        default:
            return '#cccccc';
    }
}

// Obtener el nombre de la categoría según su valor
function getCategoryName(categoryValue) {
    if (categoryValue.startsWith('custom_')) {
        const categoryId = categoryValue.replace('custom_', '');
        const category = userCategories.find(cat => cat.id == categoryId);
        return category ? category.nombre : 'Categoría personalizada';
    }
    
    // Nombres por defecto
    switch(categoryValue) {
        case 'event':
            return 'Evento';
        case 'task':
            return 'Tarea';
        case 'party':
            return 'Fiesta';
        case 'personal':
            return 'Personal';
        default:
            return 'Otro';
    }
}
