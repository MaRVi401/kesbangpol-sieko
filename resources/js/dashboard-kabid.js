import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Tangkap semua form dengan class 'form-paraf'
    const formsParaf = document.querySelectorAll('.form-paraf');
    
    formsParaf.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Hentikan proses submit bawaan browser
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Dokumen ini akan diparaf dan diteruskan ke Sekban.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669', // Warna hijau disesuaikan dengan tombol
                cancelButtonColor: '#ef4444', // Warna merah
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Paraf!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik "Ya", lanjutkan proses submit form
                    form.submit(); 
                }
            });
        });
    });

    // 2. Fungsi buka/tutup modal tolak (Bisa dipindahkan ke sini juga agar lebih rapi)
    window.bukaModalTolak = function(uuid, no_tiket) {
        document.getElementById('nomor_tiket_tolak').innerText = no_tiket;
        
        // Asumsi base URL untuk form action tolak
        const baseUrl = window.location.origin + '/kabid/dashboard/proses';
        document.getElementById('formTolakTiket').action = baseUrl + '/' + uuid;
        
        document.getElementById('modalTolakTiket').classList.remove('hidden');
        document.getElementById('modalTolakTiket').classList.add('flex');
    };

    window.tutupModalTolak = function() {
        document.getElementById('modalTolakTiket').classList.add('hidden');
        document.getElementById('modalTolakTiket').classList.remove('flex');
    };

});