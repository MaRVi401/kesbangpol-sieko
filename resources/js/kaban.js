// Import SweetAlert2 jika Anda menggunakan NPM
import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Ambil semua tombol dengan class 'btn-selesai'
    const tombolSelesai = document.querySelectorAll('.btn-selesai');

    // 2. Looping untuk menambahkan event listener ke masing-masing tombol
    tombolSelesai.forEach(tombol => {
        tombol.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah aksi default (jika ada)

            // 3. Tampilkan SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Apakah anda yakin ingin merubah status tiket ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a', // Warna hijau tailwind (green-600)
                cancelButtonColor: '#d33',     // Warna merah untuk batal
                confirmButtonText: 'Ya, Yakin!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                
                // 4. Jika tombol "Ya, Yakin!" diklik
                if (result.isConfirmed) {
                    
                    // Di sini seharusnya ada proses AJAX (Axios/Fetch) ke backend
                    // Tapi karena Anda tidak ingin merubah database, 
                    // kita lewati proses AJAX dan langsung tampilkan pesan sukses.
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Status pura-puranya berhasil dirubah (Tidak ada perubahan di database).',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    });

    // (Opsional) Jika Anda juga ingin mengatur modal tolak sekban yang ada di HTML Anda:
    const btnTolak = document.querySelectorAll('.btn-tolak-sekban');
    const modalTolak = document.getElementById('modalTolakSekban');
    
    if(btnTolak && modalTolak) {
        btnTolak.forEach(btn => {
            btn.addEventListener('click', function() {
                const noTiket = this.getAttribute('data-notiket');
                document.getElementById('label_no_tiket').innerText = noTiket;
                modalTolak.classList.remove('hidden');
                modalTolak.classList.add('flex');
            });
        });

        // Fungsi global untuk menutup modal tolak 
        // (Ditaruh di window agar bisa dipanggil onclick="tutupModalTolakSekban()")
        window.tutupModalTolakSekban = function() {
            modalTolak.classList.add('hidden');
            modalTolak.classList.remove('flex');
        };
    }
});