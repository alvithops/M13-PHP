<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pembelian</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        @media print {
            #tombol { display: none; }
            body { margin: 0; }
            .tengah { width: 100%; }
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, td, th {
            border: 1px solid #ddd;
            padding: 12px;
        }
        th { background-color: #f8f9fa; text-align: left; }
        .tengah {
            width: 80%;
            margin: 20px auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header-nota {
            line-height: 1.8;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
<div class="tengah">
    <div id="tombol" style="margin-bottom: 20px;">
        <button onClick="window.location.assign('barangtersedia.php')">Beli lagi</button>
        <button onClick="window.print()">Print Nota</button>
    </div>

    <?php
    require_once "koneksitoko.php";
    $kon = koneksiToko();

    // 1. Validasi ID (Mencegah SQL Injection)
    $idhjual = isset($_GET['idhjual']) ? intval($_GET['idhjual']) : 0;

    if ($idhjual <= 0) {
        echo "<div style='color:red;'>ID Transaksi tidak valid.</div>";
        exit;
    }

    // 2. Ambil data Header dengan Prepared Statement (Lebih Aman)
    $stmt = $kon->prepare("SELECT * FROM hjual WHERE idhjual = ?");
    $stmt->bind_param("i", $idhjual);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();

    if (!$data) {
        echo "<h3>Nota dengan ID #$idhjual tidak ditemukan.</h3>";
        exit;
    }
    ?>

    <h2 style="margin-top:0;">BUKTI PEMBELIAN</h2>
    
    <div class="header-nota">
        <strong>NO. NOTA  :</strong> <?php echo date("Ymd", strtotime($data['tanggal'])) . str_pad($data['idhjual'], 4, "0", STR_PAD_LEFT); ?><br>
        <strong>TANGGAL   :</strong> <?php echo date("d F Y", strtotime($data['tanggal'])); ?><br>
        <strong>NAMA      :</strong> <?php echo htmlspecialchars($data['namacust']); ?><br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 3. Ambil data Detail
            $sql_detail = "SELECT b.nama, d.harga, d.qty 
                          FROM djual d
                          JOIN barang b ON d.idbarang = b.id 
                          WHERE d.idhjual = ?";
            
            $stmt_d = $kon->prepare($sql_detail);
            $stmt_d->bind_param("i", $idhjual);
            $stmt_d->execute();
            $hasil_detail = $stmt_d->get_result();
            
            $total_bayar = 0;

            if ($hasil_detail->num_rows > 0) {
                while($row = $hasil_detail->fetch_assoc()){
                    $subtotal = $row['harga'] * $row['qty'];
                    $total_bayar += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="text-center"><?php echo $row['qty']; ?></td>
                        <td class="text-right">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td class="text-right">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada detail barang.</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">TOTAL PEMBAYARAN:</th>
                <th class="text-right" style="background: #f8f9fa;">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>