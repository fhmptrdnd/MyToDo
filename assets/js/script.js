const DEBUG = true;

function debugLog(message) {
    if (DEBUG) {
        console.log('[DEBUG]', message);
    }
}

// Modal Functions
function showCreateBoardModal() {
    debugLog('showCreateBoardModal called');
    const modal = document.getElementById('createBoardModal');
    if (modal) {
        modal.style.display = 'block';
        debugLog('Modal displayed');
    } else {
        console.error('createBoardModal not found!');
    }
}

function showCreateListModal() {
    debugLog('showCreateListModal called');
    const modal = document.getElementById('createListModal');
    if (modal) {
        modal.style.display = 'block';
        debugLog('Modal displayed');
    } else {
        console.error('createListModal not found!');
    }
}

function showCreateCardModal(listId) {
    debugLog('showCreateCardModal called with listId: ' + listId);
    document.getElementById('create_card_list_id').value = listId;
    const modal = document.getElementById('createCardModal');
    if (modal) {
        modal.style.display = 'block';
        debugLog('Modal displayed');
    } else {
        console.error('createCardModal not found!');
    }
}

function closeModal(modalId) {
    debugLog('closeModal called: ' + modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        debugLog('Modal closed');
    } else {
        console.error('Modal not found: ' + modalId);
    }
}

// Close modal
// window.onclick = function(event) {
//     if (event.target.classList.contains('modal')) {
//         debugLog('Clicked outside modal, closing...');
//         event.target.style.display = 'none';
//     }
// }

document.addEventListener('click', function(event) {
    // Handle modal close when clicking outside
    if (event.target.classList.contains('modal')) {
        debugLog('Clicked outside modal, closing...');
        event.target.style.display = 'none';
    }
    
    // Handle edit card buttons using event 
    if (event.target.classList.contains('edit-card-btn')) {
        const cardElement = event.target.closest('[data-card-id]');
        if (cardElement) {
            const cardId = cardElement.dataset.cardId;
            const listId = cardElement.dataset.listId;
            const title = cardElement.dataset.title;
            const description = cardElement.dataset.description;
            const imagePath = cardElement.dataset.imagePath;
            
            editCard(cardId, listId, title, description, imagePath);
        }
    }
});

// Board Functions
function editBoard(id, title, description, color) {
    debugLog('editBoard called - ID: ' + id);
    
    document.getElementById('edit_board_id').value = id;
    document.getElementById('edit_board_title').value = title;
    document.getElementById('edit_board_description').value = description;
    
    // Set color radio button
    const colorInputs = document.querySelectorAll('#editBoardModal input[name="color"]');
    colorInputs.forEach(input => {
        if (input.value === color) {
            input.checked = true;
        }
    });
    
    document.getElementById('editBoardModal').style.display = 'block';
    debugLog('Edit board modal opened');
}

function deleteBoard(id) {
    debugLog('deleteBoard called - ID: ' + id);
    if (confirm('Are you sure you want to delete this board? All lists and cards will be deleted.')) {
        debugLog('Delete confirmed, redirecting...');
        window.location.href = 'controllers/board_controller.php?delete=1&id=' + id;
    } else {
        debugLog('Delete cancelled');
    }
}

// List Functions
function editList(id, title) {
    debugLog('editList called - ID: ' + id);
    
    document.getElementById('edit_list_id').value = id;
    document.getElementById('edit_list_title').value = title;
    document.getElementById('editListModal').style.display = 'block';
    debugLog('Edit list modal opened');
}

function deleteList(id) {
    debugLog('deleteList called - ID: ' + id);
    if (confirm('Are you sure you want to delete this list? All cards will be deleted.')) {
        const urlParams = new URLSearchParams(window.location.search);
        const boardId = urlParams.get('id');
        debugLog('Delete confirmed, Board ID: ' + boardId);
        window.location.href = 'controllers/list_controller.php?delete=' + id + '&board_id=' + boardId;
    } else {
        debugLog('Delete cancelled');
    }
}

// Card Functions
function editCard(id, listId, title, description, imagePath) {
    debugLog('editCard called - ID: ' + id + ', List ID: ' + listId);
    
    document.getElementById('edit_card_id').value = id;
    document.getElementById('edit_card_list_id').value = listId;
    document.getElementById('edit_card_title').value = title;
    document.getElementById('edit_card_description').value = description;
    document.getElementById('edit_card_old_image').value = imagePath;
    
    const previewDiv = document.getElementById('current_image_preview');
    if (imagePath) {
        previewDiv.innerHTML = '<p style="font-size: 12px; color: #9B9B9B;">Current image:</p><img src="' + imagePath + '" style="max-width: 100%; max-height: 150px; border-radius: 8px;">';
    } else {
        previewDiv.innerHTML = '';
    }
    
    document.getElementById('editCardModal').style.display = 'block';
    debugLog('Edit card modal opened');
}

function deleteCard(id) {
    debugLog('deleteCard called - ID: ' + id);
    if (confirm('Are you sure you want to delete this card?')) {
        const urlParams = new URLSearchParams(window.location.search);
        const boardId = urlParams.get('id');
        debugLog('Delete confirmed, Board ID: ' + boardId);
        window.location.href = 'controllers/card_controller.php?delete=' + id + '&board_id=' + boardId;
    } else {
        debugLog('Delete cancelled');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    debugLog('DOM Content Loaded');
    
    const fileInputs = document.querySelectorAll('input[type="file"]');
    debugLog('Found ' + fileInputs.length + ' file inputs');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                debugLog('File selected: ' + file.name + ', Size: ' + file.size);
                
                // Check file size (2MB)
                if (file.size > 2097152) {
                    alert('File size must be less than 2MB');
                    e.target.value = '';
                    debugLog('File too large');
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only JPG, PNG, and GIF images are allowed');
                    e.target.value = '';
                    debugLog('Invalid file type: ' + file.type);
                    return;
                }
                
                debugLog('File validation passed');
            }
        });
    });

function refreshCardEventListeners() {
    debugLog('Refreshing card event listeners');
    
    // Delet old event listener (kalau ada)
    const oldButtons = document.querySelectorAll('.edit-card-btn');
    oldButtons.forEach(btn => {
        btn.replaceWith(btn.cloneNode(true));
    });
    
    // New event listener
    const editButtons = document.querySelectorAll('.edit-card-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cardElement = this.closest('[data-card-id]');
            if (cardElement) {
                const cardId = cardElement.dataset.cardId;
                const listId = cardElement.dataset.listId;
                const title = cardElement.dataset.title;
                const description = cardElement.dataset.description;
                const imagePath = cardElement.dataset.imagePath;
                
                editCard(cardId, listId, title, description, imagePath);
            }
        });
    });
    
    debugLog('Refreshed ' + editButtons.length + ' card event listeners');
}
    
    debugLog('Script initialized successfully');
});
debugLog('script.js loaded successfully');