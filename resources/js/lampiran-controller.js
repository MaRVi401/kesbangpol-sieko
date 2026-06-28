document.addEventListener('DOMContentLoaded', () => {
    const compressImageToWebP = async (file) => {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    canvas.toBlob((blob) => {
                        resolve(new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".webp", { type: "image/webp", lastModified: Date.now() }));
                    }, 'image/webp', 0.7);
                };
            };
        });
    };

    // Auto-compress gambar saat file dipilih
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/jpg') {
                try {
                    const webpFile = await compressImageToWebP(file);
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(webpFile);
                    e.target.files = dataTransfer.files; 
                } catch (err) {
                    console.error('Compression failed:', err);
                }
            }
        });
    });

    // Tangani Submit pada masing-masing form
    document.querySelectorAll('.lampiran-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Limit 8MB per form (karena dipecah)
            let totalSize = 0;
            this.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.files.length > 0) totalSize += input.files[0].size;
            });

            if (totalSize > (8 * 1024 * 1024)) {
                Swal.fire({
                    icon: 'error', title: 'Batas Ukuran Terlampaui!',
                    html: `Total file di bagian ini adalah <b>${(totalSize / 1048576).toFixed(2)} MB</b>.<br>Maksimal <b>8 MB</b> per bagian.`,
                    confirmButtonColor: '#d33'
                });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Mengunggah...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 2000, showConfirmButton: false });
                    
                    // Kunci form secara visual tanpa perlu refresh
                    this.querySelector('fieldset').disabled = true;
                    submitBtn.outerHTML = '<span class="inline-flex items-center text-sm font-medium text-green-600 bg-green-100 px-4 py-2 rounded-lg"><svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tersimpan</span>';
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message || 'Periksa kembali dokumen Anda.' });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Sistem Error', text: 'Terjadi kegagalan koneksi.' });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });

    // Tangani Tombol Finalisasi
    const btnSelesai = document.getElementById('btn-selesaikan-permohonan');
    if (btnSelesai) {
        btnSelesai.addEventListener('click', async function() {
            // Pastikan tidak ada tombol upload yang masih tersisa (artinya semua sudah terkunci/tersimpan)
            const remainingUploadButtons = document.querySelectorAll('.lampiran-form button[type="submit"]');
            if (remainingUploadButtons.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Selesai',
                    text: 'Anda harus mengunggah dan menyimpan seluruh 3 bagian formulir sebelum menyelesaikan permohonan.',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            btnSelesai.disabled = true;
            btnSelesai.innerHTML = 'Memproses...';

            try {
                const response = await fetch(this.dataset.url, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    Swal.fire({
                        icon: 'success', title: 'Selesai!', text: result.message, confirmButtonColor: '#3085d6', allowOutsideClick: false
                    }).then(() => {
                        window.location.href = result.redirect_url;
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: result.message });
                    btnSelesai.disabled = false;
                    btnSelesai.innerHTML = 'Selesaikan Permohonan';
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi ke server terputus.' });
                btnSelesai.disabled = false;
            }
        });
    }
});