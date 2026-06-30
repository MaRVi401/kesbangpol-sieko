document.addEventListener('DOMContentLoaded', function () {
    // Mengambil semua form dengan class form-berita-acara
    const forms = document.querySelectorAll('.form-berita-acara');

    forms.forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault(); // Mencegah form reload halaman

            const uuid = this.getAttribute('data-uuid');
            const submitButton = document.getElementById(`btn-action-${uuid}`);
            const formData = new FormData(this);
            
            // Ganti URL ini dengan endpoint route Laravel Anda yang sebenarnya
            const actionUrl = `/verifikator-lapangan/tiket/${uuid}/simpan-berita-ajax`;

            // 1. Tampilkan status Loading
            Swal.fire({
                title: 'Menyimpan Data...',
                html: 'Mohon tunggu sebentar, data sedang diproses.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Menonaktifkan tombol submit untuk mencegah double klik
            submitButton.disabled = true;

            try {
                // 2. Proses pengiriman data menggunakan Fetch API
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        // CSRF token otomatis terkirim karena ada di dalam FormData bawaan @csrf
                    },
                    body: formData
                });

                const result = await response.json();

                // 3. Menangani Hasil Berhasil
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message || 'Berita Acara berhasil disimpan.',
                        confirmButtonColor: '#ea580c' // Warna orange menyesuaikan tema form Anda
                    }).then(() => {
                        // Tutup modal atau reload halaman jika diperlukan
                        window.location.reload(); 
                    });
                } 
                // 4. Menangani Proses Gagal (Validasi atau Error dari server)
                else {
                    let errorMessage = result.message || 'Terjadi kesalahan saat memproses data.';
                    
                    // Jika ada error validasi dari Laravel
                    if (result.errors) {
                        errorMessage = Object.values(result.errors).flat().join('<br>');
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Proses Gagal',
                        html: errorMessage,
                        confirmButtonColor: '#ea580c'
                    });
                }

            } catch (error) {
                // 5. Menangani Bug/Error Jaringan atau Server
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Terjadi Bug!',
                    text: 'Koneksi terputus atau terjadi kesalahan pada sistem. Silakan hubungi administrator.',
                    confirmButtonColor: '#d33'
                });
            } finally {
                // Mengaktifkan kembali tombol submit
                submitButton.disabled = false;
            }
        });
    });
});