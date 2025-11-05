<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Struk Transaksi</title>
    <style>
        @page {
            size: 76mm auto;
            margin: 0;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .receipt {
            width: 70mm;
            margin: 8px 0;
            padding: 0 4mm;
            box-sizing: border-box;
        }

        .center {
            text-align: center;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .muted {
            color: #555;
            font-size: 11px;
            line-height: 1.2;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 1px 0;
        }

        .qty {
            width: 25px;
            text-align: center;
        }

        .price {
            text-align: right;
            width: 70px;
        }

        .small {
            font-size: 11px;
        }

        @media print {

            html,
            body {
                width: 100%;
                background: none;
            }

            body {
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }

            .no-print {
                display: none;
            }
        }
    </style>

</head>

<body>
    <div class="receipt">
        <div class="center">
            <div class="logo">TapKasir</div>
            <div class="muted">
                Jl. Kampus Bukit Jimbaran, Kuta Selatan, Badung, Bali 80364<br>
                Telp: 08123456789
            </div>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td class="small">Tanggal</td>
                <td class="small" style="text-align:right">
                    <?= date('Y-m-d H:i:s', strtotime($transaction['transaction_date'])) ?>
                </td>
            </tr>
            <tr>
                <td class="small">Kasir</td>
                <td class="small" style="text-align:right">
                    <?= esc($user['username'] ?? $transaction['user_id']) ?>
                </td>
            </tr>
            <tr>
                <td class="small">No Transaksi</td>
                <td class="small" style="text-align:right">
                    <?= esc($transaction['no_transaction'] ?? $transaction['id']) ?>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td colspan="2"><?= esc($it['product_name'] ?? 'Item') ?></td>
                </tr>
                <tr>
                    <td class="qty small"><?= esc($it['quantity']) ?> x</td>
                    <td class="price small">Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td class="small">Total QTY</td>
                <td class="small" style="text-align:right">
                    <?= array_sum(array_column($items, 'quantity')) ?>
                </td>
            </tr>
            <tr>
                <td class="small">Sub Total</td>
                <td class="small" style="text-align:right">
                    Rp <?= number_format($transaction['total'], 0, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td class="small">Pembayaran</td>
                <td class="small" style="text-align:right">
                    Rp <?= number_format($transaction['payment'], 0, ',', '.') ?>
                </td>
            </tr>
            <tr>
                <td class="small">Kembalian</td>
                <td class="small" style="text-align:right">
                    Rp <?= number_format($transaction['change'], 0, ',', '.') ?>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="center small">Terima kasih telah berbelanja</div>

        <div class="center no-print" style="margin-top:8px">
            <button onclick="window.print()">Cetak</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>


</body>

</html>