<?php
session_start();
// Cek apakah pengguna sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';
require_once 'templates/header.php';

// Ambil semua barang yang stoknya > 0
$stmt = $pdo->query("SELECT id_barang, nama_barang, harga_jual, stok FROM tabel_barang WHERE stok > 0 AND is_deleted = 0 ORDER BY nama_barang");
$barang_tersedia = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Formulir Kasir / Penjualan Baru</h2>
<form action="penjualan_handler.php" method="post" id="form-penjualan">
    <div class="form-group">
        <label for="barang">Pilih Barang</label>
        <select id="barang-select">
            <option value="">-- Pilih Barang --</option>
            <?php foreach ($barang_tersedia as $barang):?>
                <option value="<?php echo $barang['id_barang'];?>" data-harga="<?php echo $barang['harga_jual'];?>" data-stok="<?php echo $barang['stok'];?>">
                    <?php echo htmlspecialchars($barang['nama_barang']);?> (Stok: <?php echo $barang['stok'];?>)
                </option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="form-group">
        <label for="jumlah">Jumlah</label>
        <input type="number" id="jumlah-barang" min="1">
    </div>
    <button type="button" id="tambah-ke-keranjang" class="btn">Tambah ke Keranjang</button>

    <h3>Keranjang Belanja</h3>
    <table id="keranjang-tabel">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>
    
    <h3 id="total-harga">Total: Rp 0</h3>
    
    <button type="submit" name="simpan_penjualan" class="btn">Simpan Transaksi</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barangSelect = document.getElementById('barang-select');
    const jumlahInput = document.getElementById('jumlah-barang');
    const tambahBtn = document.getElementById('tambah-ke-keranjang');
    const keranjangTbody = document.querySelector('#keranjang-tabel tbody');
    const totalHargaEl = document.getElementById('total-harga');
    const form = document.getElementById('form-penjualan');

    let keranjang = {};
    let totalHarga = 0;

    tambahBtn.addEventListener('click', function() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const idBarang = selectedOption.value;
        const namaBarang = selectedOption.text.split(' (Stok:')[0];
        const harga = parseFloat(selectedOption.dataset.harga);
        const stok = parseInt(selectedOption.dataset.stok);
        const jumlah = parseInt(jumlahInput.value);

        if (!idBarang || !jumlah || jumlah <= 0) {
            alert('Pilih barang dan masukkan jumlah yang valid.');
            return;
        }

        if (jumlah > stok) {
            alert('Jumlah melebihi stok yang tersedia.');
            return;
        }

        if (keranjang[idBarang]) {
            if (keranjang[idBarang].jumlah + jumlah > stok) {
                alert('Jumlah total melebihi stok yang tersedia.');
                return;
            }
            keranjang[idBarang].jumlah += jumlah;
        } else {
            keranjang[idBarang] = { nama: namaBarang, jumlah: jumlah, harga: harga, stok: stok };
        }

        updateKeranjangTampilan();
        // Reset jumlah setelah tambah
        jumlahInput.value = '';
    });

    function updateKeranjangTampilan() {
        keranjangTbody.innerHTML = '';
        totalHarga = 0;

        for (const id in keranjang) {
            const item = keranjang[id];
            const subtotal = item.jumlah * item.harga;
            totalHarga += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.nama}</td>
                <td>${item.jumlah}</td>
                <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td><button type="button" class="btn-hapus-item" data-id="${id}">Hapus</button></td>
            `;
            keranjangTbody.appendChild(tr);
        }

        totalHargaEl.textContent = `Total: Rp ${totalHarga.toLocaleString('id-ID')}`;
        updateFormInputs();
    }

    keranjangTbody.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-hapus-item')) {
            const id = e.target.dataset.id;
            delete keranjang[id];
            updateKeranjangTampilan();
        }
    });

    function updateFormInputs() {
        // Hapus input lama
        const oldInputs = form.querySelectorAll('input[name^="barang["]');
        oldInputs.forEach(input => input.remove());

        // Tambah input baru
        for (const id in keranjang) {
            const item = keranjang[id];
            // Validasi jumlah harus angka dan > 0
            if (typeof item.jumlah === 'number' && item.jumlah > 0) {
                const hiddenInputId = document.createElement('input');
                hiddenInputId.type = 'hidden';
                hiddenInputId.name = `barang[${id}][id]`;
                hiddenInputId.value = id;
                form.appendChild(hiddenInputId);

                const hiddenInputJumlah = document.createElement('input');
                hiddenInputJumlah.type = 'hidden';
                hiddenInputJumlah.name = `barang[${id}][jumlah]`;
                hiddenInputJumlah.value = item.jumlah;
                form.appendChild(hiddenInputJumlah);
            }
        }
    }
});
</script>

<?php require_once 'templates/footer.php';?>