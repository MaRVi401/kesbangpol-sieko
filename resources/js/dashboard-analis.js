import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    // Tangkap semua form dengan class form-paraf-draft
    const parafForms = document.querySelectorAll('.form-paraf-draft');

    parafForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Mencegah form langsung tersubmit

            Swal.fire({
                title: 'Konfirmasi Paraf',
                text: 'Apakah Anda yakin sudah mengecek draft SKT ini dan ingin memparafnya untuk diteruskan ke Kabid Kesbak?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9333ea', // Menyesuaikan dengan warna bg-purple-600 Tailwind
                cancelButtonColor: '#6b7280',  // Warna abu-abu untuk tombol batal
                confirmButtonText: 'Ya, Paraf & Teruskan!',
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