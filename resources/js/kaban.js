import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    
    
    const tandaTanganForms = document.querySelectorAll('.form-tanda-tangan');

    tandaTanganForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); 

            Swal.fire({
                title: 'Konfirmasi Persetujuan Final',
                text: 'Apakah Anda yakin ingin menyetujui dan menandatangani SKT ini secara final?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a', 
                confirmButtonText: 'Ya, Tanda Tangani!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                   
                    form.submit();
                }
            });
        });
    });

    
});