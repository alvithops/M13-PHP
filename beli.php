<?php
// PASTIKAN DATA DIAMBIL DARI FORM TERLEBIH DAHULU
$namacust = $_POST['namacust'] ?? '';
$email = $_POST['email'] ?? '';
$notelp = $_POST['notelp'] ?? '';
$tanggal = date("Y-m-d"); // Tanggal otomatis hari ini
$barangPilih = $_POST['barangPilih'] ?? ''; // Asumsi dikirim via POST

if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
    $qty = 1;
    $simpan = true;
    $pesan_error = "";

    require_once "koneksitoko.php";
    $kon = koneksiToko();

    // Memulai transaksi
    mysqli_begin_transaction($kon);

    // 1. Simpan ke tabel Header (hjual)
    // Gunakan mysqli_real_escape_string untuk keamanan dasar
    $sql = "INSERT INTO hjual (tanggal, namacust, email, notelp) 
              VALUES ('$tanggal', '$namacust', '$email', '$notelp')";
    $hasil = mysqli_query($kon, $sql);

    if (!$hasil) {
        $simpan = false;
        $pesan_error = "Data customer gagal disimpan: " . mysqli_error($kon);
    } else {
        // Mendapatkan ID dari query terakhir
        $idhjual = mysqli_insert_id($kon);

        if ($idhjual == 0) {
            $simpan = false;
            $pesan_error = "ID Transaksi tidak tergenerate";
        }
    }

    if ($simpan) {
        $barang_array = explode(",", $barangPilih);
        if (empty($barangPilih) || count($barang_array) == 0) {
            $simpan = false;
            $pesan_error = "Tidak ada barang yang dipilih";
        } else {
            foreach ($barang_array as $idbarang) {
                $idbarang = intval($idbarang); // Pastikan angka
                if ($idbarang == 0)
                    continue;

                // Cek stok dan harga
                $sql_cek = "SELECT nama, stok, harga FROM barang WHERE id = $idbarang FOR UPDATE";
                $res_cek = mysqli_query($kon, $sql_cek);

                if (!$res_cek || mysqli_num_rows($res_cek) == 0) {
                    $simpan = false;
                    $pesan_error = "Barang ID $idbarang tidak ada";
                    break;
                }

                $row = mysqli_fetch_array($res_cek);
                $stok_baru = $row['stok'] - $qty;
                $harga = $row['harga'];

                if ($stok_baru < 0) {
                    $simpan = false;
                    $pesan_error = "Stok " . $row['nama'] . " tidak mencukupi";
                    break;
                }

                // 2. Simpan ke Detail (djual)
                $sql_det = "INSERT INTO djual (idhjual, idbarang, qty, harga) 
                            VALUES ($idhjual, $idbarang, $qty, $harga)";
                if (!mysqli_query($kon, $sql_det)) {
                    $simpan = false;
                    $pesan_error = "Gagal simpan detail";
                    break;
                }

                // 3. Update stok
                $sql_upd = "UPDATE barang SET stok = $stok_baru WHERE id = $idbarang";
                if (!mysqli_query($kon, $sql_upd)) {
                    $simpan = false;
                    $pesan_error = "Gagal update stok";
                    break;
                }
            }
        }
    }

    // Finalisasi
    if ($simpan) {
        mysqli_commit($kon);
        setcookie('keranjang', '', time() - 3600, "/");
        header("Location: buktibeli.php?idhjual=$idhjual");
        exit();
    } else {
        mysqli_rollback($kon);
        echo "<h3>Pembelian Gagal!</h3>";
        echo "<p>Alasan: $pesan_error</p>";
        echo "<a href='barangtersedia.php'>Kembali</a>";
    }
}