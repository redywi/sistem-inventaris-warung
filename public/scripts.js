// File: public/scripts.js

// Fungsi untuk konfirmasi penghapusan
// Ini dipanggil dari atribut onclick pada link hapus
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus item ini?');
}

// Menambahkan event listener ke semua link hapus untuk keamanan yang lebih baik
document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('a.btn-hapus');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                event.preventDefault(); // Mencegah link di-klik jika pengguna membatalkan
            }
        });
    });

    // Validasi Sederhana di Sisi Klien
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const numberInputs = form.querySelectorAll('input[type="number"]');
            
            numberInputs.forEach(input => {
                // Memastikan input angka tidak kosong dan merupakan angka
                if (input.value === '' || isNaN(parseFloat(input.value))) {
                    alert('Kolom harga dan stok harus diisi dengan angka.');
                    input.focus();
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault(); // Mencegah form dikirim jika tidak valid
            }
        });
    });
});