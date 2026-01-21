<?php
$header = $detail['header'] ?? [];
$services = $detail['services'] ?? [];
$items = $detail['items'] ?? [];

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

// Simple inline CSS optimized for A4 printable layout
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Work Order - <?php echo esc($header['NoOrder'] ?? '-'); ?></title>
    <style>
        /* A4 portrait sizing */
        @page { size: A4; margin: 15mm; }
        html, body { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; color: #111; }
        .page { width: 210mm; min-height: 297mm; padding: 10mm; box-sizing: border-box; }
        h1 { font-size: 18px; margin: 0 0 6px 0; text-align: center; }
        .meta { text-align: center; margin-bottom: 8px; }
        .info { margin: 10px 0 14px; }
        .info .row { display: flex; gap: 8px; margin-bottom: 4px; }
        .info .label { width: 120px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #444; padding: 6px; }
        th { background: #eee; }
        .right { text-align: right; }
        .no-border { border: none; }
        .summary { width: 40%; float: right; margin-top: 8px; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="page">
        <h1>WORK ORDER</h1>
        <div class="meta">
            <div><?php echo esc('No. Order: ' . ($header['NoOrder'] ?? '-')); ?></div>
            <div><?php echo esc('Tanggal: ' . (!empty($header['TanggalOrder']) ? date('d/m/Y', strtotime($header['TanggalOrder'])) : '-')); ?></div>
        </div>

        <section class="info">
            <div class="row"><div class="label">Customer</div><div><?php echo esc($header['NamaCustomer'] ?? '-'); ?></div></div>
            <div class="row"><div class="label">Alamat</div><div><?php echo esc($header['AlamatCustomer'] ?? '-'); ?></div></div>
            <div class="row"><div class="label">No. Telepon</div><div><?php echo esc($header['NoTelepon'] ?? '-'); ?></div></div>
            <div class="row"><div class="label">Kendaraan</div><div><?php echo esc($header['NamaKendaraan'] ?? '-'); ?></div></div>
            <div class="row"><div class="label">No. Polisi</div><div><?php echo esc($header['NoPolisi'] ?? '-'); ?></div></div>
            <div class="row"><div class="label">Marketing</div><div><?php echo esc($header['Marketing'] ?? '-'); ?></div></div>
        </section>

        <?php if (!empty($services)): ?>
            <h2>JASA / SERVICE</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width:6%">No</th>
                        <th>Nama Jasa</th>
                        <th>Mekanik</th>
                        <th style="width:6%">Qty</th>
                        <th style="width:16%">Tarif</th>
                        <th style="width:16%">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; foreach ($services as $s): ?>
                    <tr>
                        <td class="right"><?php echo $no++; ?></td>
                        <td><?php echo esc($s['NamaJasa'] ?? '-'); ?></td>
                        <td><?php echo esc($s['Mekanik'] ?? '-'); ?></td>
                        <td class="right"><?php echo esc($s['Qty'] ?? '0'); ?></td>
                        <td class="right"><?php echo esc(number_format((float)($s['Tarif'] ?? 0),0,',','.')); ?></td>
                        <td class="right"><?php echo esc(number_format((float)($s['Total'] ?? 0),0,',','.')); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($items)): ?>
            <h2 style="margin-top:14px">BARANG / SPARE PART</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width:6%">No</th>
                        <th>Nama Barang</th>
                        <th>Merek</th>
                        <th style="width:8%">Sat.</th>
                        <th style="width:6%">Qty</th>
                        <th style="width:16%">Harga</th>
                        <th style="width:16%">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; foreach ($items as $it): ?>
                    <tr>
                        <td class="right"><?php echo $no++; ?></td>
                        <td><?php echo esc($it['NamaBarang'] ?? '-'); ?></td>
                        <td><?php echo esc($it['MerekBarang'] ?? '-'); ?></td>
                        <td class="right"><?php echo esc($it['Satuan'] ?? '-'); ?></td>
                        <td class="right"><?php echo esc($it['Qty'] ?? '0'); ?></td>
                        <td class="right"><?php echo esc(number_format((float)($it['Harga'] ?? 0),0,',','.')); ?></td>
                        <td class="right"><?php echo esc(number_format((float)($it['Total'] ?? 0),0,',','.')); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="summary">
            <table>
                <tbody>
                    <tr>
                        <td class="no-border">Total Jasa</td>
                        <td class="right"><?php echo esc(number_format((float)($header['TotalJasa'] ?? 0),0,',','.')); ?></td>
                    </tr>
                    <tr>
                        <td class="no-border">Total Barang</td>
                        <td class="right"><?php echo esc(number_format((float)($header['TotalBarang'] ?? 0),0,',','.')); ?></td>
                    </tr>
                    <tr>
                        <td class="no-border"><strong>TOTAL ORDER</strong></td>
                        <td class="right"><strong><?php echo esc(number_format((float)($header['TotalOrder'] ?? 0),0,',','.')); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="clear:both;margin-top:40px;">
            <div style="float:left;text-align:center;">____________________________<br>Petugas</div>
            <div style="float:right;text-align:center;">____________________________<br>Customer</div>
        </div>

        <div style="margin-top:20px;text-align:center" class="no-print">
            <button onclick="window.print();">Cetak (Print)</button>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog when this page is opened in a new tab/window
        window.addEventListener('load', function(){
            // Give browser a short moment to render CSS
            setTimeout(function(){ window.print(); }, 300);
        });
    </script>
</body>
</html>
