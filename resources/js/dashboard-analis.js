const uploadModal = document.getElementById('uploadModal');
const modalContent = document.getElementById('modalContent');
const inputUuid = document.getElementById('modal_input_uuid');
const displayTiket = document.getElementById('modal_display_tiket');

window.openUploadModal = function(uuid, noTiket) {
    inputUuid.value = uuid;
    displayTiket.textContent = noTiket;

    uploadModal.classList.remove('hidden');
    
    setTimeout(() => {
        uploadModal.classList.remove('opacity-0');
        uploadModal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
}

window.closeUploadModal = function() {
    uploadModal.classList.remove('opacity-100');
    uploadModal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');

    setTimeout(() => {
        uploadModal.classList.add('hidden');
        document.getElementById('uploadForm').reset();
    }, 300);
}

uploadModal.addEventListener('click', function(e) {
    if (e.target === uploadModal) {
        window.closeUploadModal();
    }
});