document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.getElementById('status');
    const wrapperAnalis = document.getElementById('wrapper-analis');
    const selectAnalis = document.getElementById('analis_id');
    
    const inputKomentar = document.getElementById('komentar');
    const labelCatatanReq = document.getElementById('label-catatan-req');
    const helpTextTolak = document.getElementById('help-text-tolak');
    
    // Ambil elemen form
    const formTindakLanjut = document.getElementById('form-tindak-lanjut');

    // 1. Logic untuk mengubah tampilan form (tetap sama)
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'pembuatan_draft_skt') {
                wrapperAnalis.classList.remove('hidden');
                selectAnalis.setAttribute('required', 'required');
                
                inputKomentar.removeAttribute('required');
                labelCatatanReq.classList.add('hidden');
                helpTextTolak.classList.add('hidden');
            } else if (this.value === 'data_tidak_sesuai') {
                wrapperAnalis.classList.add('hidden');
                selectAnalis.removeAttribute('required');
                selectAnalis.value = ''; 
                
                inputKomentar.setAttribute('required', 'required');
                labelCatatanReq.classList.remove('hidden');
                helpTextTolak.classList.remove('hidden');
            } else {
                wrapperAnalis.classList.add('hidden');
                selectAnalis.removeAttribute('required');
                inputKomentar.removeAttribute('required');
                labelCatatanReq.classList.add('hidden');
                helpTextTolak.classList.add('hidden');
            }
        });
    }

    // 2. Logic untuk SweetAlert2 saat form di-submit
    if (formTindakLanjut) {
        formTindakLanjut.addEventListener('submit', function(e) {
            e.preventDefault(); // Tahan dulu form agar tidak langsung terkirim

            const actionType = statusSelect.value;
            let swalTitle = 'Konfirmasi Tindakan';
            let swalText = 'Apakah Anda yakin ingin melanjutkan?';
            let swalIcon = 'warning';
            let confirmBtnColor = '#3085d6';

            // Kustomisasi pesan Swal berdasarkan pilihan dropdown
            if (actionType === 'pembuatan_draft_skt') {
                swalTitle = 'Kirim ke Analis?';
                swalText = 'Pastikan data sudah benar. Tiket akan diteruskan ke Analis Muda untuk pembuatan draft SKT.';
                swalIcon = 'info';
                confirmBtnColor = '#2563eb'; // Warna biru Tailwind
            } else if (actionType === 'data_tidak_sesuai') {
                swalTitle = 'Tolak Tiket?';
                swalText = 'Tiket akan ditolak dan dikembalikan. Pastikan catatan sudah diisi dengan jelas.';
                swalIcon = 'warning';
                confirmBtnColor = '#dc2626'; // Warna merah Tailwind
            }

            // Tampilkan SweetAlert
            Swal.fire({
                title: swalTitle,
                text: swalText,
                icon: swalIcon,
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#6b7280', // Warna abu-abu Tailwind
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik 'Ya', submit form secara terprogram
                    formTindakLanjut.submit();
                }
            });
        });
    }
});