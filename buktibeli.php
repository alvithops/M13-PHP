<!DOCTYPE html>
<html>
    <head>
        <title>Bukti Pembelian</title>
        <style>
            @media print{
                #tombol{
                    display: none;
                }
            }
            table, td, th {
                border: 1px solid gray;
            }

            table {
                border-collapse: collapse;
            }

            .tengah{
                width: 75%;
                margin: auto;
            }

            small{
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="tengah">
            <input type="button" value="Beli Lagi" onclick="window.location.href='tampilbarang.php'">
            <input type="button" value="Cetak" onclick="window.print()">
        </div>
        <?php
        $idhjual = $_GET
    </body>

</html>