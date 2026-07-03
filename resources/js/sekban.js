import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Tangkap semua form dengan class 'form-paraf-sekban'
    const parafForms = document.querySelectorAll('.form-paraf-sekban');

    parafForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Mencegah form langsung tersubmit

            Swal.fire({
                title: 'Konfirmasi Paraf',
                text: 'Apakah Anda yakin ingin memparaf draft SKT ini untuk diteruskan ke Kepala Badan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a', // Warna hijau tailwind (green-600)
                cancelButtonColor: '#d33',     // Warna merah untuk batal
                confirmButtonText: 'Ya, Paraf Dokumen!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik Ya, submit form secara terprogram
                    form.submit();
                }
            });
        });
    });
});