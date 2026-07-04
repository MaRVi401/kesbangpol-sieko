document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.getElementById('status');
    
    const wrapperAnalis = document.getElementById('wrapper-analis');
    const selectAnalis = document.getElementById('analis_id');
    
    // 1. Tangkap elemen nomor surat yang baru ditambahkan
    const wrapperNomorSurat = document.getElementById('wrapper-nomor-surat');
    const inputNomorSurat = document.getElementById('nomor_surat');
    
    const inputKomentar = document.getElementById('komentar');
    const labelCatatanReq = document.getElementById('label-catatan-req');
    const helpTextTolak = document.getElementById('help-text-tolak');
    
    // Ambil elemen form
    const formTindakLanjut = document.getElementById('form-tindak-lanjut');

    // Logic untuk mengubah tampilan form
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'pembuatan_draft_skt') {
                // Tampilkan dan wajibkan Analis
                wrapperAnalis.classList.remove('hidden');
                selectAnalis.setAttribute('required', 'required');
                
                // 2. Tampilkan dan wajibkan Nomor Surat
                if(wrapperNomorSurat && inputNomorSurat) {
                    wrapperNomorSurat.classList.remove('hidden');
                    inputNomorSurat.setAttribute('required', 'required');
                }
                
                // Sembunyikan requirement komentar
                inputKomentar.removeAttribute('required');
                labelCatatanReq.classList.add('hidden');
                helpTextTolak.classList.add('hidden');
                
            } else if (this.value === 'data_tidak_sesuai') {
                // Sembunyikan dan bersihkan Analis
                wrapperAnalis.classList.add('hidden');
                selectAnalis.removeAttribute('required');
                selectAnalis.value = ''; 
                
                // 3. Sembunyikan dan bersihkan Nomor Surat
                if(wrapperNomorSurat && inputNomorSurat) {
                    wrapperNomorSurat.classList.add('hidden');
                    inputNomorSurat.removeAttribute('required');
                    inputNomorSurat.value = ''; 
                }
                
                // Wajibkan komentar penolakan
                inputKomentar.setAttribute('required', 'required');
                labelCatatanReq.classList.remove('hidden');
                helpTextTolak.classList.remove('hidden');
                
            } else {
                wrapperAnalis.classList.add('hidden');
                selectAnalis.removeAttribute('required');
                
                if(wrapperNomorSurat && inputNomorSurat) {
                    wrapperNomorSurat.classList.add('hidden');
                    inputNomorSurat.removeAttribute('required');
                }
                
                inputKomentar.removeAttribute('required');
                labelCatatanReq.classList.add('hidden');
                helpTextTolak.classList.add('hidden');
            }
        });
    }

    // Logic untuk SweetAlert2 saat form di-submit (Tidak ada perubahan di blok ini)
    if (formTindakLanjut) {
        formTindakLanjut.addEventListener('submit', function(e) {
            e.preventDefault(); 

            const actionType = statusSelect.value;
            let swalTitle = 'Konfirmasi Tindakan';
            let swalText = 'Apakah Anda yakin ingin melanjutkan?';
            let swalIcon = 'warning';
            let confirmBtnColor = '#3085d6';

            if (actionType === 'pembuatan_draft_skt') {
                swalTitle = 'Kirim ke Analis?';
                swalText = 'Pastikan data sudah benar. Tiket akan diteruskan ke Analis Muda untuk pembuatan draft SKT.';
                swalIcon = 'info';
                confirmBtnColor = '#2563eb'; 
            } else if (actionType === 'data_tidak_sesuai') {
                swalTitle = 'Tolak Tiket?';
                swalText = 'Tiket akan ditolak dan dikembalikan. Pastikan catatan sudah diisi dengan jelas.';
                swalIcon = 'warning';
                confirmBtnColor = '#dc2626'; 
            }

            Swal.fire({
                title: swalTitle,
                text: swalText,
                icon: swalIcon,
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#6b7280', 
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formTindakLanjut.submit();
                }
            });
        });
    }
});