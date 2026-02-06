<?php
// 1. PINDAHKAN LOGIKA KE PALING ATAS
require_once "koneksitoko.php";
$kon = koneksiToko();

// Validasi ID (Mencegah SQL Injection)
$idhjual = isset($_GET['idhjual']) ? intval($_GET['idhjual']) : 0;

if ($idhjual <= 0) {
    die("Error: ID Transaksi tidak valid.");
}

// 2. Ambil data Header
$stmt = $kon->prepare("SELECT * FROM hjual WHERE idhjual = ?");
$stmt->bind_param("i", $idhjual);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if (!$data) {
    die("Error: Nota dengan ID #$idhjual tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pembelian #<?php echo $idhjual; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .tengah {
            width: 90%;
            max-width: 800px;
            margin: auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header-nota {
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        td,
        th {
            border: 1px solid #eee;
            padding: 12px;
        }

        th {
            background-color: #fcfcfc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        #tombol {
            margin-bottom: 25px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
            background: #007bff;
            color: white;
        }

        @media print {
            #tombol {
                display: none;
            }

            .tengah {
                width: 100%;
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="tengah">
        <div id="tombol">
            <button onClick="window.location.assign('barangtersedia.php')">Beli lagi</button>
            <button onClick="window.print()" style="background: #28a745;">Print Nota</button>
        </div>

        <h2 style="margin:0 0 20px 0;">BUKTI PEMBELIAN</h2>

        <div class="header-nota">
            <div style="float: left;">
                <strong>NO. NOTA :</strong>
                <?php echo date("Ymd", strtotime($data['tanggal'])) . str_pad($data['idhjual'], 4, "0", STR_PAD_LEFT); ?><br>
                <strong>TANGGAL :</strong> <?php echo date("d F Y", strtotime($data['tanggal'])); ?>
            </div>
            <div style="float: right; text-align: right;">
                <strong>CUSTOMER :</strong><br>
                <?php echo htmlspecialchars($data['namacust']); ?>
            </div>
            <div style="clear: both;"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
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
                    while ($row = $hasil_detail->fetch_assoc()) {
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
                    // PERBAIKAN: Menggunakan kutip tunggal di dalam kutip ganda
                    echo "<tr><td colspan='4' class='text-center'>Data barang kosong.</td></tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">TOTAL AKHIR</th>
                    <th class="text-right" style="color: #d9534f;">
                        Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>

</html>