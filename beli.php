// Jika tidak ada error, data siap disimpan
if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
$qty = 1;
$simpan = true;

require_once "koneksitoko.php";
$kon = koneksiToko();

// Memulai transaksi
mysqli_begin_transaction($kon);

// 1. Simpan ke tabel Header (hjual)
$sql = "INSERT INTO hjual (tanggal, namacust, email, notelp)
VALUES ('$tanggal', '$namacust', '$email', '$notelp')";
$hasil = mysqli_query($kon, $sql);

if (!$hasil) {
$simpan = false;
$pesan_error = "Data customer gagal disimpan";
}

// Mendapatkan ID dari query terakhir
$idhjual = mysqli_insert_id($kon);
if ($idhjual == 0) {
$simpan = false;
$pesan_error = "Data customer tidak ditemukan";
}

// Mengkonversi string separator ',' menjadi array
$barang_array = explode(",", $barangPilih);
$jumlah = count($barang_array);

if ($jumlah == 0) {
$simpan = false;
$pesan_error = "Tidak ada barang yang dipilih";
} else {
// Loop setiap barang yang dipilih
foreach ($barang_array as $idbarang) {
if ($idbarang == 0) continue;

// Cek stok dan harga barang
$sql = "SELECT * FROM barang WHERE id = $idbarang";
$hasil = mysqli_query($kon, $sql);

if (!$hasil || mysqli_num_rows($hasil) == 0) {
$simpan = false;
$pesan_error = "Barang dengan ID $idbarang tidak ditemukan";
break;
} else {
$row = mysqli_fetch_array($hasil);
$stok = $row['stok'] - $qty;
$harga = $row['harga'];
$nama_barang = $row['nama'];

if ($stok < 0) { $simpan=false; $pesan_error="Stok barang $nama_barang habis!" ; break; } } // 2. Simpan ke tabel Detail
    (djual) $sql_detail="INSERT INTO djual (idhjual, idbarang, qty, harga) 
                           VALUES ('$idhjual', '$idbarang', '$qty', '$harga')" ; $hasil_detail=mysqli_query($kon,
    $sql_detail); if (!$hasil_detail) { $simpan=false; $pesan_error="Gagal menyimpan detail pembelian" ; break; } // 3.
    Update stok barang $sql_update="UPDATE barang SET stok = $stok WHERE id = $idbarang" ;
    $hasil_update=mysqli_query($kon, $sql_update); if (!$hasil_update) { $simpan=false;
    $pesan_error="Gagal mengupdate stok barang" ; break; } } // End foreach } // Finalisasi Transaksi if ($simpan) {
    mysqli_commit($kon); // Hapus cookie keranjang karena transaksi sukses setcookie('keranjang', '' , time() - 3600);
    header("Location: buktibeli.php?idhjual=$idhjual"); exit(); } else { mysqli_rollback($kon); echo "Pembelian Gagal: "
    . $pesan_error; } } // End if tidak ada errorr