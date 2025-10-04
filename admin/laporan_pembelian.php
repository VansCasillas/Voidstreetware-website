<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        slideIn: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            },
                        },
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 flex min-h-screen">

    <!-- Sidebar -->
    <aside class="min-h-screen bg-gradient-to-b from-gray-800 to-gray-900 text-white w-64 flex-shrink-0 flex flex-col shadow-xl">
        <div class="text-center py-6 text-2xl font-bold border-b border-gray-700">
            <a href="admin_dash.php" class="hover:text-primary-300 transition flex items-center justify-center">
                <span class="bg-white p-2 rounded-lg mr-2">
                    <img src="../resource/Voidstreetware_logo.png" class="w-8">
                </span>
                Voidstreetware
            </a>
        </div>
        <nav class="flex-1 px-4 py-6 flex flex-col justify-between">
            <div class="space-y-1">
                <a href="produk_jacket.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition text-gray-200 hover:text-white">
                    <i class="fas fa-vest text-primary-300 w-6"></i>
                    <span class="ml-3">Produk Jacket</span>
                </a>
                <a href="produk_hoodie.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition text-gray-200 hover:text-white">
                    <i class="fas fa-tshirt text-primary-300 w-6"></i>
                    <span class="ml-3">Produk Hoodie</span>
                </a>
                <a href="kelola_user.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition text-gray-200 hover:text-white">
                    <i class="fas fa-users text-primary-300 w-6"></i>
                    <span class="ml-3">Kelola User</span>
                </a>
                <a href="laporan_pembelian.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-gray-700 transition text-gray-200 hover:text-white">
                    <i class="fas fa-chart-bar text-primary-300 w-6"></i>
                    <span class="ml-3">Laporan Pembelian</span>
                </a>
                <a href="../logout.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-red-700 transition text-gray-200 hover:text-white">
                    <i class="fas fa-sign-out-alt text-red-400 w-6"></i>
                    <span class="ml-3">Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-white py-4 shadow-sm">
            <div class="px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Laporan Pembelian</h1>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                        <?php echo strtoupper(substr($_SESSION['admin']['name'], 0, 1)); ?>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700"><?php echo $_SESSION['admin']['name']; ?></p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // --- FILTER TANGGAL (opsional) ---
        $tanggal = '';
        if (isset($_GET['tanggal']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['tanggal'])) {
            $tanggal = $_GET['tanggal'];
        }
        $where = '';
        if ($tanggal !== '') {
            $where = "WHERE DATE(c.created_at) = '" . mysqli_real_escape_string($koneksi, $tanggal) . "'";
        }

        // --- PAGINATION SETUP ---
        $limit = 7;

        // HITUNG TOTAL BARIS SESUAI QUERY YANG SAMA (ikut JOIN)
        $totalQuery = mysqli_query($koneksi, "
            SELECT COUNT(*) AS total
            FROM checkout c
            JOIN checkout_item ci ON c.id_checkout = ci.id_checkout
            JOIN produk p ON ci.id_produk = p.id_produk
            $where
        ");
        $totalData = (int) mysqli_fetch_assoc($totalQuery)['total'];

        // pastikan minimal 1 halaman agar logic aman
        $totalPages = max(1, (int) ceil($totalData / $limit));

        // halaman aktif (clamp 1..totalPages)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;

        // offset setelah page valid
        $offset = ($page - 1) * $limit;

        // ambil data (terbaru dulu) sesuai filter + pagination
        $query = mysqli_query($koneksi, "
            SELECT c.id_checkout, c.created_at, u.name,
                   p.nama_produk, ci.qty, ci.harga_item, (ci.qty * ci.harga_item) as total_harga
            FROM checkout c
            JOIN user u ON c.id_user = u.id_user
            JOIN checkout_item ci ON c.id_checkout = ci.id_checkout
            JOIN produk p ON ci.id_produk = p.id_produk
            $where
            ORDER BY c.created_at DESC, ci.id_checkout DESC
            LIMIT $limit OFFSET $offset
        ");

        // query string tambahan untuk pagination & cetak
        $qs = ($tanggal !== '') ? "&tanggal=" . urlencode($tanggal) : "";
        ?>

        <!-- Filter Tanggal -->
        <div class="bg-white shadow rounded-lg p-6 mb-6 mt-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <label for="tanggal" class="text-gray-700 font-medium">Pilih Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>"
                    class="w-[80%] border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 transition">
                        Filter
                    </button>
                    <?php if ($tanggal !== ''): ?>
                        <a href="?page=1"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                            Bersihkan
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            <p class="text-sm text-gray-500 mt-2">
                Menampilkan: <span class="font-medium">
                    <?= ($tanggal !== '') ? date('d M Y', strtotime($tanggal)) : 'Semua Tanggal'; ?>
                </span>
            </p>
        </div>

        <!-- Laporan Penjualan -->
        <div class="mt-2 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    Laporan Penjualan <?= ($tanggal !== '') ? '(' . htmlspecialchars($tanggal) . ')' : '(Terbaru)'; ?>
                </h2>
            </div>
            <div class="overflow-x-auto bg-white shadow rounded p-4">
                <div class="mb-4">
                    <a href="cetak.php<?= ($tanggal !== '' ? '?tanggal=' . $tanggal : ''); ?>"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 transition">
                        <i class="fa fa-print"></i> Cetak
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nama User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $no = $offset + 1;
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $no++; ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars($data['name']); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars($data['nama_produk']); ?></td>
                                    <td class="px-6 py-3 text-sm text-center text-gray-700"><?= (int)$data['qty']; ?></td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-700">Rp <?= number_format($data['harga_item'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-700">Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-3 text-sm text-center text-gray-500"><?= $data['created_at']; ?></td>
                                </tr>
                            <?php } ?>
                            <?php if ($totalData === 0): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">
                                        Tidak ada data untuk periode ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php
                $prevDisabled = ($page <= 1);
                $nextDisabled = ($page >= $totalPages);
                $prevLink = "?page=" . max(1, $page - 1) . $qs;
                $nextLink = "?page=" . min($totalPages, $page + 1) . $qs;
                ?>
                <div class="flex justify-between items-center px-6 py-4 bg-gray-50">
                    <a href="<?= $prevLink; ?>"
                        class="px-4 py-2 text-sm rounded bg-gray-200 text-gray-700 hover:bg-gray-300 <?= $prevDisabled ? 'pointer-events-none opacity-50' : ''; ?>">
                        &laquo; Sebelumnya
                    </a>
                    <span class="text-sm text-gray-600">Halaman <?= $page; ?> dari <?= $totalPages; ?></span>
                    <a href="<?= $nextLink; ?>"
                        class="px-4 py-2 text-sm rounded bg-gray-200 text-gray-700 hover:bg-gray-300 <?= $nextDisabled ? 'pointer-events-none opacity-50' : ''; ?>">
                        Selanjutnya &raquo;
                    </a>
                </div>
            </div>

            <!-- Ringkasan Omset Penjual -->
            <div class="mt-8 bg-white shadow rounded p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    Laporan Omset <?= ($tanggal !== '') ? '(' . htmlspecialchars($tanggal) . ')' : '(Semua Tanggal)'; ?>
                </h2>

                <?php
                // Total omset dan qty (menghormati filter bila ada)
                $omsetRow = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT
                    SUM(ci.qty * ci.harga_item) AS omset,
                    SUM(ci.qty) AS total_produk
                FROM checkout c
                JOIN checkout_item ci ON c.id_checkout = ci.id_checkout
                $where
            "));
                $omset = (int) ($omsetRow['omset'] ?? 0);
                $produkTerjual = (int) ($omsetRow['total_produk'] ?? 0);

                // Omset per kategori (menghormati filter bila ada)
                $kategoriQuery = mysqli_query($koneksi, "
                SELECT p.kategori,
                       SUM(ci.qty) AS total_qty,
                       SUM(ci.harga_item * ci.qty) AS total_kategori
                FROM checkout_item ci
                JOIN produk p ON ci.id_produk = p.id_produk
                JOIN checkout c ON ci.id_checkout = c.id_checkout
                $where
                GROUP BY p.kategori
                ORDER BY total_kategori DESC
            ");
                ?>

                <!-- Tabel Ringkasan -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200 text-gray-800">
                                <th class="px-6 py-3 text-xs font-medium text-gray-600 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-600 uppercase tracking-wider">Total Terjual</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-600 uppercase tracking-wider">Total Omset</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (mysqli_num_rows($kategoriQuery) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($kategoriQuery)) { ?>
                                    <tr class="text-center">
                                        <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars(ucfirst($row['kategori'])); ?></td>
                                        <td class="px-6 py-3 text-sm text-gray-700"><?= (int)$row['total_qty']; ?></td>
                                        <td class="px-6 py-3 text-sm text-gray-700">Rp <?= number_format((int)$row['total_kategori'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-500">Tidak ada data.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Total Omset & Produk -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-indigo-100 rounded-lg text-center">
                        <p class="text-sm text-gray-600">Total Produk Terjual</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $produkTerjual; ?> pcs</p>
                    </div>
                    <div class="p-4 bg-green-100 rounded-lg text-center">
                        <p class="text-sm text-gray-600">Total Omset</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?= number_format($omset, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-6 text-center text-gray-500 py-4 border-t border-gray-300">
                &copy; 2025 Voidstreetware
            </footer>
        </div>
    </main>


</body>

</html>