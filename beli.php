//jika tidak ada eror data siap disimpan
if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
    $qty = 1;
    $simpan = true;

    require_once "koneksitoko.php";
    $kon = koneksiToko();

    //memulai transaksi
    $mulaiTransaksi = mysqli_begin_transaction($kon);
    $sql = "insert into hjual (tanggal, namacust, email, notelp) value ('$tanggal','$namacust','$email','$notelp')";
    $hasil = mysqli_query($kon, $sql);
    if(!$hasil){
        echo "Data customer gagal disimpan" <br>";
        $simpan = false;
    }

    //mendapat id dr query terakhir
    $idhjual = mysqli_insert_id($kon);
    if($idhjual == 0) {
        echo "Data customer tidak ada <br>";
        $simpan = false;
    }

    // mengkonversi string separator ',' $barangPilih menjadi
array
    $barang_array = explode(",", $barangPilih);
    $jumlah = count($barang_array);

    if($jumlah == 0){
        echo "Tidak ada barang yang dipilih <br>";
        $simpan = false;
    } else {
        foreach($barang_array as $idbarang){
            if($idbarang == 0){
                continue;
            }
           $sql = "select * from barang where id = $idbarang ";
           $hasil = mysqli_query($kon, $sql); 
        }
    }
}