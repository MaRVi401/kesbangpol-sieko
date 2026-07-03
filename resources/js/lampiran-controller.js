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
                    console.error(err);
                }
            }
        });
    });

    document.querySelectorAll('.lampiran-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
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

    const actionContainer = document.querySelector('.flex.justify-between.items-center.pt-6');
    if (actionContainer) {
        const btnAutofill = document.createElement('button');
        btnAutofill.type = 'button';
        btnAutofill.id = 'btn-autofill-lampiran';
        btnAutofill.className = 'text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 mr-3 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700';
        btnAutofill.innerHTML = '<svg class="w-4 h-4 mr-2 inline-block -mt-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>Isi Dummy Lampiran';
        actionContainer.insertBefore(btnAutofill, document.getElementById('btn-selesaikan-permohonan'));

        btnAutofill.addEventListener('click', async function(e) {
            e.preventDefault();

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
                    canvas.toBlob((blob) => resolve(new File([blob], 'dummy-lampiran.jpg', { type: 'image/jpeg', lastModified: Date.now() })), 'image/jpeg', 0.8);
                });
            };

            const createDummyPdf = () => {
                const pdfBase64 = "JVBERi0xLjEKJcKlwrQKMSAwIG9iago8PCAvVHlwZSAvQ2F0YWxvZwovUGFnZXMgMiAwIFIKPj4KZW5kb2JqCjIgMCBvYmoKPDwgL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPj4KZW5kb2JqCjMgMCBvYmoKPDwgL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovTWVkaWFCb3ggWzAgMCAzMDAgMTQ0XQovQ29udGVudHMgNCAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwgL0xlbmd0aCAzMgo+PgpzdHJlYW0KQlQKL0YxIDEyIFRmCjEwIDEwIFRkCihEdW1teSBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKNSAwIG9iago8PCAvVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EKPj4KZW5kb2JqCnhyZWYKMCA2CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDAxNyAwMDAwMCBuIAowMDAwMDAwMDY1IDAwMDAwIG4gCjAwMDAwMDAxMjIgMDAwMDAgbiAKMDAwMDAwMDIxMiAwMDAwMCBuIAowMDAwMDAwMjk0IDAwMDAwIG4gCnRyYWlsZXIKPDwgL1NpemUgNgovUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKMzQzCiUlRU9GCg==";
                const byteCharacters = atob(pdfBase64);
                const byteNumbers = new Array(byteCharacters.length);
                for (let i = 0; i < byteCharacters.length; i++) {
                    byteNumbers[i] = byteCharacters.charCodeAt(i);
                }
                return new File([new Uint8Array(byteNumbers)], 'dummy-dokumen.pdf', { type: 'application/pdf', lastModified: Date.now() });
            };

            const dummyImageFile = await createDummyImage();
            const dummyPdfFile = createDummyPdf();

            document.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.closest('fieldset') && input.closest('fieldset').disabled) return;

                const acceptAttr = (input.getAttribute('accept') || '').toLowerCase();
                const dataTransfer = new DataTransfer();

                if (acceptAttr.includes('.pdf') && !acceptAttr.includes('image/*') && !acceptAttr.includes('.jpg') && !acceptAttr.includes('.png')) {
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
                    icon: 'success', title: 'Seluruh Lampiran Diisi!', timer: 2000, showConfirmButton: false
                });
            }
        });
    }

    const btnSelesai = document.getElementById('btn-selesaikan-permohonan');
    if (btnSelesai) {
        btnSelesai.addEventListener('click', async function() {
            const remainingUploadButtons = document.querySelectorAll('.lampiran-form button[type="submit"]');
            if (remainingUploadButtons.length > 0) {
                Swal.fire({
                    icon: 'warning', title: 'Belum Selesai', text: 'Anda harus mengunggah dan menyimpan seluruh 3 bagian formulir sebelum menyelesaikan permohonan.', confirmButtonColor: '#d33'
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