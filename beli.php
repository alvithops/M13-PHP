//jika tidak ada eror data siap disimpan
if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
    $qty = 1;
    $simpan = true;

    require_once "koneksitoko.php";
}