// === Elemen Modal ===
var addsng = document.getElementById("addSong");
var editsng = document.getElementById("editSong");
var btnOpenAdd = document.getElementById("addSongBtn");
var closeButtons = document.querySelectorAll(".close-btn");

if (btnOpenAdd && addModal) {
    btnOpenAdd.onclick = function() {
        addModal.style.display = "block";
    }
} else {
    if (!addsng) console.error("Modal element with ID 'addSong' not found.");
    if (!btnOpenAdd) console.error("Button element with ID 'addSongBtn' not found.");
}
var editLinks = document.querySelectorAll(".edit-link");

editLinks.forEach(link => {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        const card = this.closest('.song-card');
        if (!card) {
            console.error("Parent .song-card not found for edit link.");
            return;
        }

        const songId = card.dataset.id;
        const songJudul = card.dataset.judul;
        const songArtist = card.dataset.artist;
        const songGenre = card.dataset.genre;
        const songTahun = card.dataset.tahun;

        const editForm = editModal ? editModal.querySelector('form') : null;
        if (!editForm) {
             console.error("Edit form within #editSongModal not found.");
             return;
        }
        editForm.querySelector('#edit-song-id').value = songId || '';
        editForm.querySelector('#edit-judul').value = songJudul || '';
        editForm.querySelector('#edit-artist').value = songArtist || '';
        editForm.querySelector('#edit-genre').value = songGenre || '';
        editForm.querySelector('#edit-tahun').value = songTahun || '';
        if (editModal) {
            editModal.style.display = "block";
        } else {
            console.error("Modal element with ID 'editSongModal' not found when trying to display.");
        }
    });
});
closeButtons.forEach(button => {
    button.onclick = function() {
        let modalToClose = this.closest('.modal');
        if (modalToClose) {
            modalToClose.style.display = "none";
        } else {
            console.error("Could not find parent modal for close button:", this);
        }
    }
});
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}
const actionButtons = document.querySelectorAll('.song-actions-btn');

actionButtons.forEach(button => {
    button.addEventListener('click', function(event) {
        const dropdown = this.nextElementSibling;
        closeAllDropdowns(dropdown);
        if (dropdown && dropdown.classList.contains('dropdown-menu')) {
            dropdown.classList.toggle('show');
        }
        event.stopPropagation();
    });
});
function closeAllDropdowns(exceptThisOne = null) {
    const allDropdowns = document.querySelectorAll('.dropdown-menu.show');
    allDropdowns.forEach(dropdown => {
        if (dropdown !== exceptThisOne) {
            dropdown.classList.remove('show');
        }
    });
}
window.addEventListener('click', function(event) {
    if (!event.target.matches('.song-actions-btn') && !event.target.closest('.song-actions-btn')) {
        closeAllDropdowns();
    }
});