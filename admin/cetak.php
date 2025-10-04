<?php
include '../koneksi.php';

// ambil filter tanggal dari GET (kalau ada)
$tanggal = '';
if (isset($_GET['tanggal']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
}

$where = '';
if ($tanggal !== '') {
    $where = "WHERE DATE(c.created_at) = '" . mysqli_real_escape_string($koneksi, $tanggal) . "'";
}

// query data sesuai filter
$query = mysqli_query($koneksi, "
    SELECT c.id_checkout, c.created_at, u.name, 
           p.nama_produk, ci.qty, ci.harga_item, (ci.qty * ci.harga_item) as total_harga
    FROM checkout c
    JOIN user u ON c.id_user = u.id_user
    JOIN checkout_item ci ON c.id_checkout = ci.id_checkout
    JOIN produk p ON ci.id_produk = p.id_produk
    $where
    ORDER BY c.created_at DESC
");
?>

<h2 align="center">Laporan Pembelian 
    <?= ($tanggal !== '' ? date('d M Y', strtotime($tanggal)) : 'Semua Tanggal'); ?>
</h2>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr style="background:#f3f4f6">
            <th>No</th>
            <th>Nama User</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i = 1; 
        while ($data = mysqli_fetch_assoc($query)) { ?>
            <tr align="center">
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($data['name']); ?></td>
                <td><?= htmlspecialchars($data['nama_produk']); ?></td>
                <td><?= (int)$data['qty']; ?></td>
                <td align="right">Rp <?= number_format($data['harga_item'], 0, ',', '.'); ?></td>
                <td align="right">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
                <td><?= $data['created_at']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    window.print();
    setTimeout(() => window.close(), 100);
</script>
