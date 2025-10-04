<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

// proses hapus produk
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = $_GET['id'];

    // ambil data untuk hapus file thumbnail juga
    $produk = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id'"));
    if ($produk && file_exists("../uploads/" . $produk['tumbnail_produk'])) {
        unlink("../uploads/" . $produk['tumbnail_produk']);
    }

    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");
    echo "<script>alert('Produk Jacket berhasil dihapus!'); window.location='produk_jacket.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Jacket - Dashboard</title>
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
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateY(-10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
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
                <h1 class="text-2xl font-bold text-gray-800">Produk Jacket</h1>
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

        <!-- Konten -->
        <div class="flex-1 p-6">
            <!-- Tombol Tambah -->
            <div class="mb-4">
                <a href="?action=tambah"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 transition">
                    + Tambah Produk
                </a>
            </div>

            <?php
            // FORM TAMBAH PRODUK
            if (isset($_GET['action']) && $_GET['action'] == 'tambah') { ?>
                <div class="bg-white shadow rounded p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Form Tambah Produk</h2>
                    <form method="post" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block mb-1 font-semibold">Nama Jacket</label>
                            <input type="text" name="nama_produk" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Harga</label>
                            <input type="number" name="harga" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Deskripsi</label>
                            <textarea name="deskripsi" required class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Upload Thumbnail</label>
                            <input type="file" name="thumbnail" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="submit" name="simpan"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500 transition">Simpan</button>
                            <a href="produk_jacket.php"
                                class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-300 transition">Batal</a>
                        </div>
                    </form>
                </div>
                <?php
                if (isset($_POST['simpan'])) {
                    $nama = $_POST['nama_produk'];
                    $harga = $_POST['harga'];
                    $deskripsi = $_POST['deskripsi'];
                    $filename = $_FILES['thumbnail']['name'];
                    $tmp_name = $_FILES['thumbnail']['tmp_name'];
                    $folder = "../uploads/" . $filename;
                    move_uploaded_file($tmp_name, $folder);

                    mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga, deskripsi, tumbnail_produk, kategori) 
                                VALUES ('$nama', '$harga', '$deskripsi', '$filename', 'jacket')");
                    echo "<script>alert('Produk Jacket berhasil ditambahkan!'); window.location='produk_jacket.php';</script>";
                }
            }

            // FORM UBAH PRODUK
            if (isset($_GET['action']) && $_GET['action'] == 'ubah') {
                $id = $_GET['id'];
                $produk = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id'"));
                ?>
                <div class="bg-white shadow rounded p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Form Ubah Produk</h2>
                    <form method="post" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block mb-1 font-semibold">Nama Jacket</label>
                            <input type="text" name="nama_produk" value="<?= $produk['nama_produk']; ?>"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Harga</label>
                            <input type="number" name="harga" value="<?= $produk['harga']; ?>"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full border rounded px-3 py-2"
                                required><?= $produk['deskripsi']; ?></textarea>
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Thumbnail Saat Ini</label>
                            <img src="../uploads/<?= $produk['tumbnail_produk']; ?>" width="100" class="rounded mb-2">
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold">Ganti Thumbnail (opsional)</label>
                            <input type="file" name="thumbnail" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button type="submit" name="update"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-500 transition">Update</button>
                            <a href="produk_jacket.php"
                                class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-300 transition">Batal</a>
                        </div>
                    </form>
                </div>
            <?php
                if (isset($_POST['update'])) {
                    $nama = $_POST['nama_produk'];
                    $harga = $_POST['harga'];
                    $deskripsi = $_POST['deskripsi'];

                    if (!empty($_FILES['thumbnail']['name'])) {
                        $filename = $_FILES['thumbnail']['name'];
                        $tmp_name = $_FILES['thumbnail']['tmp_name'];
                        $folder = "../uploads/" . $filename;
                        move_uploaded_file($tmp_name, $folder);
                        $update = "UPDATE produk SET nama_produk='$nama', harga='$harga', deskripsi='$deskripsi', tumbnail_produk='$filename' WHERE id_produk='$id'";
                    } else {
                        $update = "UPDATE produk SET nama_produk='$nama', harga='$harga', deskripsi='$deskripsi' WHERE id_produk='$id'";
                    }
                    mysqli_query($koneksi, $update);
                    echo "<script>alert('Produk Jacket berhasil diubah!'); window.location='produk_jacket.php';</script>";
                }
            }
            ?>

            <!-- Tabel Produk Jacket -->
            <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Produk Jacket</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nama Jacket</th>
                                <th class="px-10 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Thumbnail</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            $i = 1;
                            $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='jacket'");
                            while ($data = mysqli_fetch_array($query)) { ?>
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-700"><?= $i++; ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-700 font-medium"><?= htmlspecialchars($data['nama_produk']); ?></td>
                                    <td class="px-6 py-3 text-sm text-right text-gray-700">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-3 text-sm text-gray-600"><?= htmlspecialchars($data['deskripsi']); ?></td>
                                    <td class="px-6 py-3 text-center">
                                        <img src="../uploads/<?= $data['tumbnail_produk']; ?>"
                                            class="h-16 w-auto mx-auto rounded object-cover">
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="inline-flex space-x-2">
                                            <a href="?action=ubah&id=<?= $data['id_produk']; ?>"
                                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-500 transition">Ubah</a>
                                            <a href="?action=hapus&id=<?= $data['id_produk']; ?>"
                                                onclick="return confirm('Yakin ingin hapus produk ini?');"
                                                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 transition">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) === 0): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                        Belum ada produk jacket.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

        <!-- Footer -->
        <footer class="mt-6 text-center text-gray-500 py-4 border-t border-gray-300">
            &copy; 2025 Voidstreetware
        </footer>
    </main>

</body>

</html>