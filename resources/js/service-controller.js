document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-pencatatan-ormas');
    if (!form) return;

    const saveStatusElement = document.getElementById('save-status');
    let tiketUuidInput = document.getElementById('tiket_uuid');
    let timeoutId;

    // 1. Fungsi Kompresi (Dibuat lebih ringkas mengikuti skema lampiran-controller)
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

    // 2. Auto-compress gambar saat file dipilih
    form.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Opsional: Tetap pertahankan batas 5MB per 1 file jika mau
            if (file.size > (5 * 1024 * 1024)) {
                Swal.fire({
                    icon: 'warning', title: 'Ukuran File Terlalu Besar!',
                    text: `Maksimal ukuran yang diizinkan per satu file adalah 5 MB.`
                });
                e.target.value = '';
                return; 
            }

            if (file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/jpg') {
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerText = 'Mengompresi gambar...';

                try {
                    const webpFile = await compressImageToWebP(file);
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(webpFile);
                    e.target.files = dataTransfer.files; 
                } catch (err) {
                    console.error('Compression failed:', err);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
    });

    // 3. Tangani Submit Form Utama
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // --- SKEMA BARU: Pengecekan Total Ukuran File 8MB ---
        let totalSize = 0;
        this.querySelectorAll('input[type="file"]').forEach(input => {
            if (input.files.length > 0) totalSize += input.files[0].size;
        });

        if (totalSize > (8 * 1024 * 1024)) {
            Swal.fire({
                icon: 'error', 
                title: 'Batas Ukuran Terlampaui!',
                html: `Total keseluruhan file Anda adalah <b>${(totalSize / 1048576).toFixed(2)} MB</b>.<br>Maksimal total lampiran form ini adalah <b>8 MB</b>.`,
                confirmButtonColor: '#d33'
            });
            return; // Hentikan submit jika lebih dari 8 MB
        }
        // ----------------------------------------------------

        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-3 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;

        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tahap 1 Berhasil!',
                        text: result.message || `Data tiket ${result.no_tiket || ''} tersimpan.`,
                        confirmButtonText: 'Lanjut Unggah Lampiran',
                        confirmButtonColor: '#1d4ed8',
                        allowOutsideClick: false
                    }).then((sweetResult) => {
                        if (sweetResult.isConfirmed) {
                            window.location.href = result.redirect_url || this.getAttribute('data-history-url') || '/history';
                        }
                    });
                } else {
                    let errorHtml = '';
                    if (result.errors) {
                        errorHtml = '<div style="text-align: left;"><ul class="pl-5 text-sm list-disc text-gray-700">';
                        Object.entries(result.errors).forEach(([field, err]) => {
                            let cleanFieldName = field.replace(/\./g, ' ').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            errorHtml += `<li class="mb-1"><b>${cleanFieldName}:</b> ${err[0]}</li>`; 
                        });
                        errorHtml += '</ul></div>';
                    } else {
                        errorHtml = `<p>${result.message || ''}</p>`;
                    }

                    Swal.fire({
                        icon: 'warning', title: 'Periksa Kembali Form Anda', html: errorHtml, confirmButtonColor: '#d33', confirmButtonText: 'Perbaiki Data'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            } else {
                const htmlText = await response.text();
                document.open();
                document.write(htmlText);
                document.close();
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Sistem Error', text: 'Terjadi kegagalan koneksi.', confirmButtonColor: '#d33' });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // 4. Fitur Autosave (Tidak dirubah logikanya)
    const performAutosave = () => {
        saveStatusElement.innerText = "Menyimpan draft...";
        const formData = new FormData(form);
        
        for (let [key, value] of Array.from(formData.entries())) {
            if (value instanceof File) {
                formData.delete(key);
            }
        }

        if (tiketUuidInput && tiketUuidInput.value) {
            formData.set('tiket_uuid', tiketUuidInput.value);
        }
        
        fetch(form.getAttribute('data-autosave-url'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                tiketUuidInput.value = res.tiket_uuid; 
                saveStatusElement.innerText = res.message; 
            }
        })
        .catch(error => {
            saveStatusElement.innerText = "Gagal menyimpan draft.";
        });
    };

    form.addEventListener('input', function(e) {
        if (e.target.type === 'file') return; 
        clearTimeout(timeoutId);
        saveStatusElement.innerText = "Mengetik...";
        timeoutId = setTimeout(performAutosave, 2000); 
    });

    // 5. Fitur Autofill Dummy (Tidak dirubah logikanya)
    const btnAutofill = document.getElementById('btn-autofill');
    if (btnAutofill) {
        btnAutofill.addEventListener('click', function(e) {
            e.preventDefault();
            const fill = (name, value) => {
                const elements = document.getElementsByName(name);
                if (elements.length > 0) {
                    elements[0].value = value;
                    elements[0].dispatchEvent(new Event('input', { bubbles: true }));
                    elements[0].dispatchEvent(new Event('change', { bubbles: true }));
                }
            };

            // Isian Dummy Data
            fill('jenis_permohonan', 'baru');
            fill('nomor', '001/DEV/ORMAS/V/2026');
            fill('perihal', 'Permohonan Pencatatan Ormas Baru');
            fill('tanggal_permohonan', '2026-05-20');
            fill('nama_pemohon', 'Jack Maulana');
            fill('tempat_lahir', 'Indramayu');
            fill('tanggal_lahir', '2005-01-10');
            fill('jabatan_pemohon', 'Ketua Umum');
            fill('nomor_ktp', '3212001122334455'); 
            fill('alamat_rumah', 'Jl. Lohbener Raya No. 12, Indramayu');
            fill('nama_organisasi', 'Himpunan Developer Muda Indramayu');
            fill('sifat_kekhususan', 'Kegiatan Pemuda / Fungsional');
            fill('nomor_akte_pendirian', '12/NOT/VIII/2025');
            fill('nomor_npwp_organisasi', '123456789012345'); 
            fill('alamat_organisasi', 'Gedung Pusat Kegiatan, Indramayu');
            fill('alamat_sekretariat', 'Sekretariat Polindra Blok C');
            fill('nama_ketua', 'Jack Maulana');
            fill('nama_sekretaris', 'Memet Zxce');
            fill('nama_bendahara', 'Budi Santoso');
            fill('jumlah_anggota', '45');
            fill('jumlah_cabang', '2');

            const pengurusData = {
                'ketua': { nama: 'Jack Maulana', telp: '081223344556', jabatan: 'Ketua Umum' },
                'sekretaris': { nama: 'Memet Zxce', telp: '081998877665', jabatan: 'Sekretaris Jenderal' },
                'bendahara': { nama: 'Budi Santoso', telp: '081554433221', jabatan: 'Bendahara Umum' }
            };

            ['ketua', 'sekretaris', 'bendahara'].forEach(role => {
                const d = pengurusData[role];
                fill(`pengurus[${role}][nama_lengkap]`, d.nama);
                fill(`pengurus[${role}][tempat_lahir]`, 'Indramayu');
                fill(`pengurus[${role}][tanggal_lahir]`, '2005-01-10');
                fill(`pengurus[${role}][jenis_kelamin]`, 'Pria');
                fill(`pengurus[${role}][status_perkawinan]`, 'Belum Kawin');
                fill(`pengurus[${role}][agama]`, 'Islam');
                fill(`pengurus[${role}][utusan_organisasi]`, 'Pusat Daerah');
                fill(`pengurus[${role}][alamat_organisasi]`, 'Gedung Pusat Kegiatan, Indramayu');
                fill(`pengurus[${role}][telepon_organisasi]`, '0234112233');
                fill(`pengurus[${role}][pendidikan_terakhir]`, 'D4 RPL');
                fill(`pengurus[${role}][alamat_rumah]`, 'Jl. Lohbener Raya No. 12');
                fill(`pengurus[${role}][telepon_rumah_hp]`, d.telp);
                fill(`pengurus[${role}][hobi]`, 'Coding, CTF');
                fill(`pengurus[${role}][tanggal_pengisian]`, '2026-05-20');
                
                const riwayatEls = document.getElementsByName(`pengurus[${role}][riwayat_organisasi][]`);
                if(riwayatEls.length > 0) {
                    riwayatEls[0].value = 'BEM Polindra 2024';
                    riwayatEls[0].dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            fill('surat_pernyataan[nama_ketua]', 'Jack Maulana');
            fill('surat_pernyataan[nomor_ktp_ketua]', '3212001122334455');
            fill('surat_pernyataan[nama_sekretaris]', 'Memet Zxce');
            fill('surat_pernyataan[nomor_ktp_sekretaris]', '3212009988776655');
            fill('surat_pernyataan[tanggal_surat_pernyataan]', '2026-05-20');
            fill('formulir_isian[nama_organisasi]', 'Himpunan Developer Muda Indramayu');
            fill('formulir_isian[bidang_kegiatan]', 'Teknologi & Keamanan Siber');
            fill('formulir_isian[ruang_lingkup]', 'Kabupaten');
            fill('formulir_isian[alamat_sekretariat]', 'Sekretariat Polindra Blok C');
            fill('formulir_isian[tempat_pendirian]', 'Indramayu');
            fill('formulir_isian[tanggal_pendirian]', '2025-08-17');
            fill('formulir_isian[asas_ciri_organisasi]', 'Pancasila & UUD 1945');
            fill('formulir_isian[tujuan_organisasi]', 'Meningkatkan literasi digital dan keamanan siber masyarakat');
            fill('formulir_isian[nama_pendiri]', 'Jack Maulana, Memet Zxce');
            fill('formulir_isian[nama_ketua]', 'Jack Maulana');
            fill('formulir_isian[nama_sekretaris]', 'Memet Zxce');
            fill('formulir_isian[nama_bendahara]', 'Budi Santoso');
            fill('formulir_isian[masa_bhakti_kepengurusan]', '2025 - 2030');
            fill('formulir_isian[keputusan_tertinggi_organisasi]', 'Musyawarah Besar');
            fill('formulir_isian[sumber_keuangan]', 'Iuran Anggota');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Dummy Data Diisi!',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }
});