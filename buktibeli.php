<!DOCTYPE html>
<html>
<head>
    <title>Bukti Pembelian</title>
    <style>
        body { font-family: sans-serif; }
        @media print {
            #tombol { display: none; }
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        table, td, th {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th { background-color: #f2f2f2; }
        .tengah {
            width: 75%;
            margin: auto;
        }
        .header-nota {
            line-height: 1.6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="tengah">
    <div id="tombol" style="margin-bottom: 20px;">
        <input type="button" value="Beli lagi" onClick="window.location.assign('barangtersedia.php')">
        <input type="button" value="Print" onClick="window.print()">
    </div>

    <?php
    require_once "koneksitoko.php";
    $kon = koneksiToko();

    // Mengamankan input ID
    $idhjual = isset($_GET['idhjual']) ? intval($_GET['idhjual']) : 0;

    // Ambil data header transaksi
    $sql  = "SELECT * FROM hjual WHERE idhjual = $idhjual";
    $res  = mysqli_query($kon, $sql);
    $data = mysqli_fetch_array($res);

    if (!$data) {
        echo "<h3>Nota tidak ditemukan.</h3>";
        exit;
    }
    ?>

    <h2>BUKTI PEMBELIAN</h2>
    
    <div class="header-nota">
        <strong>NO. NOTA  :</strong> <?php echo date("Ymd", strtotime($data['tanggal'])) . $data['idhjual']; ?><br>
        <strong>TANGGAL   :</strong> <?php echo $data['tanggal']; ?><br>
        <strong>NAMA      :</strong> <?php echo $data['namacust']; ?><br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_detail = "SELECT barang.nama, djual.harga, djual.qty, 
                          (djual.harga * djual.qty) as jumlah 
                          FROM djual 
                          INNER JOIN barang ON djual.idbarang = barang.id 
                          WHERE djual.idhjual = $idhjual";
            
            $hasil_detail = mysqli_query($kon, $sql_detail);
            $total_bayar = 0;

            while($row = mysqli_fetch_array($hasil_detail)){
                $total_bayar += $row['jumlah'];
                echo "<tr>";
                echo "  <td>{$row['nama']}</td>";
                echo "  <td align='center'>{$row['qty']}</td>";
                echo "  <td align='right'>".number_format($row['harga'], 0, ',', '.')."</td>";
                echo "  <td align='right'>".number_format($row['jumlah'], 0, ',', '.')."</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" align="right">Total Bayar:</th>
                <th align="right"><?php echo number_format($total_bayar, 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>