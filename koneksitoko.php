<?php
function koneksitoko()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "toko";

    // menciptakan koneksi
    $koneksi = mysqli_connect($servername, $username, $password, $dbname);

    // cek koneksi
    if (!$koneksi) {
        die("koneksi gagal: " . mysqli_connect_error());
    }

    return $koneksi;
}
?>