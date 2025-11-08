function openSystem(softwareId) {
    window.location.href = 'system-detail.php?id=' + softwareId;
}

function openAddSoftwareModal() {
    document.getElementById('add-software-modal').style.display = 'block';
}

function openEditSoftwareModal(id, nome, descricao) {
    document.getElementById('edit-software-id').value = id;
    document.getElementById('edit-software-name').value = nome;
    document.getElementById('edit-software-description').value = descricao;
    document.getElementById('edit-software-modal').style.display = 'block';
}

function deleteSoftware() {
    const softwareId = document.getElementById('edit-software-id').value;
    if (confirm('Tem certeza que deseja excluir este software? Todos os problemas relacionados também serão excluídos!')) {
        window.location.href = 'actions.php?action=delete_software&id=' + softwareId;
    }
}

function deleteContact(contactId) {
    if (confirm('Tem certeza que deseja remover este contato?')) {
        window.location.href = 'actions.php?action=delete_contact&id=' + contactId;
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.display = 'none';
    });
}, 5000);

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

document.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }
});