const btnAutofill = document.getElementById('btn-autofill');
    if (btnAutofill) {
        btnAutofill.addEventListener('click', async function(e) {
            e.preventDefault();
            const fill = (name, value) => {
                const elements = document.getElementsByName(name);
                if (elements.length > 0) {
                    elements[0].value = value;
                    elements[0].dispatchEvent(new Event('input', { bubbles: true }));
                    elements[0].dispatchEvent(new Event('change', { bubbles: true }));
                }
            };

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

            const createDummyImage = () => {
                return new Promise((resolve) => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 400; 
                    canvas.height = 400;
                    const ctx = canvas.getContext('2d');
                    
                    ctx.fillStyle = '#cccccc';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#000000';
                    ctx.font = '30px Arial';
                    ctx.fillText('Dummy Image', 100, 200);

                    canvas.toBlob((blob) => {
                        resolve(new File([blob], 'dummy-lampiran.jpg', { 
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    }, 'image/jpeg', 0.8);
                });
            };

            const createDummyPdf = () => {
                const pdfBase64 = "JVBERi0xLjEKJcKlwrQKMSAwIG9iago8PCAvVHlwZSAvQ2F0YWxvZwovUGFnZXMgMiAwIFIKPj4KZW5kb2JqCjIgMCBvYmoKPDwgL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPj4KZW5kb2JqCjMgMCBvYmoKPDwgL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovTWVkaWFCb3ggWzAgMCAzMDAgMTQ0XQovQ29udGVudHMgNCAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwgL0xlbmd0aCAzMgo+PgpzdHJlYW0KQlQKL0YxIDEyIFRmCjEwIDEwIFRkCihEdW1teSBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKNSAwIG9iago8PCAvVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EKPj4KZW5kb2JqCnhyZWYKMCA2CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDAxNyAwMDAwMCBuIAowMDAwMDAwMDY1IDAwMDAwIG4gCjAwMDAwMDAxMjIgMDAwMDAgbiAKMDAwMDAwMDIxMiAwMDAwMCBuIAowMDAwMDAwMjk0IDAwMDAwIG4gCnRyYWlsZXIKPDwgL1NpemUgNgovUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKMzQzCiUlRU9GCg==";
                const byteCharacters = atob(pdfBase64);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                const byteArray = new Uint8Array(byteNumbers);
                return new File([byteArray], 'dummy-dokumen.pdf', { 
                    type: 'application/pdf', 
                    lastModified: Date.now() 
                });
            };

            const dummyImageFile = await createDummyImage();
            const dummyPdfFile = createDummyPdf();

            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                const acceptAttr = (input.getAttribute('accept') || '').toLowerCase();
                const dataTransfer = new DataTransfer();

                if (acceptAttr.includes('.pdf') && !acceptAttr.includes('image/*') && !acceptAttr.includes('file_tanda_tangan_pemohon')) {
                    dataTransfer.items.add(dummyPdfFile);
                } else if (acceptAttr.includes('image/*') || acceptAttr.includes('.jpg') || acceptAttr.includes('.png')) {
                    dataTransfer.items.add(dummyImageFile);
                } else {
                    dataTransfer.items.add(dummyPdfFile);
                }

                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Dummy Data & Lampiran Diisi!',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }