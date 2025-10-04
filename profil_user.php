<?php
include 'koneksi.php';
cek_user(); // cek login

$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));

$cartCountQuery = mysqli_query($koneksi, "SELECT SUM(qty) as total FROM cart WHERE id_user='$id_user'");
$cartCount = mysqli_fetch_assoc($cartCountQuery)['total'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_biodata'])) {
        $name      = mysqli_real_escape_string($koneksi, $_POST['name']);
        $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
        $email     = mysqli_real_escape_string($koneksi, $_POST['email']);
        $no_telpon = mysqli_real_escape_string($koneksi, $_POST['no_telpon']);

        // handle upload foto
        $profil = $user['profil'];
        if (!empty($_FILES['profil']['name'])) {
            $ext = pathinfo($_FILES['profil']['name'], PATHINFO_EXTENSION);
            $filename = "profil_" . $id_user . "_" . time() . "." . $ext;
            $target = "uploads/" . $filename;
            if (move_uploaded_file($_FILES['profil']['tmp_name'], $target)) {
                $profil = $filename;
            }
        }

        mysqli_query($koneksi, "UPDATE user 
                                SET name='$name', username='$username', email='$email', no_telpon='$no_telpon', profil='$profil'
                                WHERE id_user='$id_user'");
        // refresh data
        $user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));
        echo "<script>alert('Biodata berhasil diperbarui!'); window.location='profil_user.php?page=biodata';</script>";
        exit;
    }

    // tambah alamat
    if (isset($_POST['tambah_alamat'])) {
        $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
        $no_telpon = mysqli_real_escape_string($koneksi, $_POST['no_telpon']);
        mysqli_query($koneksi, "INSERT INTO user_alamat (id_user, alamat, no_telpon, is_default) 
                                VALUES ('$id_user','$alamat','$no_telpon',0)");
    }

    // set default
    if (isset($_POST['set_default'])) {
        $id_alamat = (int) $_POST['id_alamat'];
        mysqli_query($koneksi, "UPDATE user_alamat SET is_default=0 WHERE id_user='$id_user'");
        mysqli_query($koneksi, "UPDATE user_alamat SET is_default=1 WHERE id_alamat='$id_alamat' AND id_user='$id_user'");
    }

    // copot default (biar gak ada yg default)
    if (isset($_POST['unset_default'])) {
        $id_alamat = (int) $_POST['id_alamat'];
        mysqli_query($koneksi, "UPDATE user_alamat SET is_default=0 WHERE id_alamat='$id_alamat' AND id_user='$id_user'");
    }

    // hapus alamat
    if (isset($_POST['hapus_alamat'])) {
        $id_alamat = (int) $_POST['id_alamat'];
        mysqli_query($koneksi, "DELETE FROM user_alamat WHERE id_alamat='$id_alamat' AND id_user='$id_user'");
    }
}

// Ambil alamat tambahan
$query_alamat = mysqli_query($koneksi, "
    SELECT * FROM user_alamat 
    WHERE id_user='$id_user' 
    ORDER BY is_default DESC, id_alamat DESC
");

// ====== Ambil riwayat transaksi user ======
$perPage = 6;
$pageNow = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
if ($pageNow < 1) $pageNow = 1;
$offset = ($pageNow - 1) * $perPage;

// nomor urut transaksi
$i = $offset + 1;

// hitung total data
$totalQuery = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM checkout 
    WHERE id_user='$id_user'
");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalData / $perPage);

// ambil data dengan limit & offset
$query_riwayat = mysqli_query($koneksi, "
    SELECT c.id_checkout, c.created_at, c.total_harga, ci.qty, p.nama_produk, p.harga, p.tumbnail_produk
    FROM checkout c
    JOIN checkout_item ci ON c.id_checkout = ci.id_checkout
    JOIN produk p ON ci.id_produk = p.id_produk
    WHERE c.id_user='$id_user'
    ORDER BY c.created_at DESC
    LIMIT $perPage OFFSET $offset
");

$riwayat = [];
while ($row = mysqli_fetch_assoc($query_riwayat)) {
    $riwayat[$row['id_checkout']]['tanggal'] = date('d M Y H:i', strtotime($row['created_at']));
    $riwayat[$row['id_checkout']]['total'] = $row['total_harga'];
    $riwayat[$row['id_checkout']]['items'][] = [
        'nama' => $row['nama_produk'],
        'qty' => $row['qty'],
        'harga' => $row['harga'],
        'tumbnail' => $row['tumbnail_produk']
    ];
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voidstreetware</title>
    <link rel="icon" href="resource/Voidstreetware_logo.png" type="image/png" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 backdrop-blur-lg bg-white/70 shadow-md">
        <div class="flex items-center justify-between px-6 py-4">

            <!-- Logo -->
            <div class="flex-1 text-center md:text-left">
                <a href="index.php">
                    <img src="resource/Voidstreetware.png" alt="Logo" class="h-10 mx-auto md:mx-0">
                </a>
            </div>

            <!-- Hamburger Button -->
            <div class="md:hidden">
                <button id="hamburgerBtn" class="text-xl focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Menu Desktop -->
            <ul class="items-center hidden md:flex space-x-8 nav">
                <li><a href="jacket_section.php"
                        class="py-1 border-b-2 border-transparent hover:border-black transition">JACKET</a></li>
                <li><a href="hoodie_section.php"
                        class="py-1 border-b-2 border-transparent hover:border-black transition">HOODIE</a></li>
                <li><a href="contact.php"
                        class="py-1 border-b-2 border-transparent hover:border-black transition">CONTACT</a></li>
                <li><a href="about.php"
                        class="py-1 border-b-2 border-transparent hover:border-black transition">ABOUT US</a></li>

                <!-- Search -->
                <li class="nav-right nav-search flex items-center border-b-2 border-transparent transition">
                    <input type="text" placeholder="Search..."
                        class="border rounded-full px-3 py-1 text-sm focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-300">
                    <button class="ml-2 hover:text-black">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </li>

                <!-- Cart -->
                <li class="relative">
                    <a href="cart.php" class="relative inline-block">
                        <i class="fa-solid fa-cart-arrow-down text-xl"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-bold min-w-[16px] h-[16px] flex items-center justify-center px-1 rounded-full">
                                <?= ($cartCount > 99) ? '99+' : $cartCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- User Dropdown -->
                <li class="relative group">
                    <button class="flex items-center gap-2 focus:outline-none">
                        <img src="uploads/<?= $user['profil'] ?: 'default.png' ?>"
                            class="w-8 h-8 rounded-full">
                    </button>

                    <!-- Dropdown -->
                    <ul
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pt-2">
                        <li>
                            <a href="profil_user.php" class="block px-4 py-2 hover:bg-gray-100">Edit Profil</a>
                        </li>
                        <li>
                            <a href="profil_user.php?page=riwayat" class="block px-4 py-2 hover:bg-gray-100">Riwayat
                                Pembelian</a>
                        </li>
                        <li>
                            <hr class="my-1">
                        </li>
                        <li>
                            <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-red-100">Logout</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

        <!-- Menu Mobile -->
        <div id="mobileMenu" class="hidden flex-col md:hidden bg-white px-4 pb-4 shadow-lg">
            <div class="relative">
                <button id="mobileSearchBtn"
                    class="w-full text-left py-2 text-gray-700 hover:text-black flex items-center">
                    <i class="fa-solid fa-search mr-2"></i> Search
                </button>
                <div id="mobileSearchBox" class="hidden mt-2">
                    <input type="text" placeholder="Search..."
                        class="w-full px-3 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm">
                </div>
            </div>

            <a href="index.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-house-user"></i> Home</a>
            <a href="jacket_section.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-shirt"></i> Jacket</a>
            <a href="hoodie_section.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-shirt"></i> Hoodie</a>
            <a href="contact.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-phone"></i> Contact</a>
            <a href="about.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-circle-info"></i> About Us</a>
            <a href="cart.php" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-cart-arrow-down"></i> Cart</a>
            <a href="profil_user.php?page=riwayat" class="block py-2 text-gray-700 hover:text-black"><i
                    class="fa-solid fa-clock"></i> Riwayat Belanja</a>
            <a href="profil.php" class="block py-2 text-gray-700 hover:text-black"><i class="fa-solid fa-user"></i>
                Edit Profil</a>
            <a href="logout.php" class="block py-2 text-red-500 hover:text-black"><i
                    class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </nav>

    <!-- Main Layout -->
    <div class="max-w-6xl mx-auto mt-8 flex gap-6 items-start">
        <!-- Sidebar -->
        <aside class="w-64 bg-white rounded-lg shadow-lg p-4">
            <div class="flex items-center gap-3 mb-6 pb-2 border-b">
                <img src="uploads/<?= $user['profil'] ?: 'default.png' ?>" class="w-12 h-12 rounded-full border"
                    alt="avatar">
                <div>
                    <h2 class="font-semibold"><?= htmlspecialchars($user['name']); ?></h2>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($user['username']); ?></p>
                </div>
            </div>
            <nav class="space-y-2">
                <a href="?page=biodata" class="block px-3 py-2 rounded hover:bg-green-100">Biodata Diri</a>
                <a href="?page=alamat" class="block px-3 py-2 rounded hover:bg-green-100">Daftar Alamat</a>
                <a href="?page=riwayat" class="block px-3 py-2 rounded hover:bg-green-100">Riwayat Pembelian</a>
                <a href="logout.php" class="block px-3 py-2 rounded hover:bg-red-100 text-red-600">Logout</a>
            </nav>
        </aside>

        <!-- Content -->
        <section class="flex-1">
            <!-- Biodata -->
            <div id="biodata" class="bg-white p-6 rounded-lg shadow-lg">
                <h1 class="text-lg font-semibold border-b pb-2 mb-4">Biodata Diri</h1>

                <!-- Tampilan Biodata -->
                <div id="biodata-view">
                    <div class="flex gap-6">
                        <div class="w-48 text-center">
                            <img src="uploads/<?= $user['profil'] ?: 'default.png' ?>"
                                class="rounded-full w-32 h-32 mx-auto border mb-4 object-cover">
                        </div>
                        <div class="flex-1 text-sm space-y-3">
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium text-gray-600">Nama</span>
                                <span><?= htmlspecialchars($user['name']); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium text-gray-600">Username</span>
                                <span><?= htmlspecialchars($user['username']); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium text-gray-600">Email</span>
                                <span><?= htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">No. HP</span>
                                <span><?= htmlspecialchars($user['no_telpon']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol ubah -->
                    <div class="flex justify-end mt-6">
                        <button onclick="toggleForm(true)"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Ubah Biodata
                        </button>
                    </div>
                </div>

                <!-- Form Edit Biodata (hidden dulu) -->
                <div id="biodata-form" class="hidden">
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="update_biodata" value="1">

                        <div class="flex gap-6">
                            <div class="w-48 text-center">
                                <img src="uploads/<?= $user['profil'] ?: 'default.png' ?>"
                                    class="rounded-full w-32 h-32 mx-auto border mb-4 object-cover">
                                <input type="file" name="profil"
                                    class="block w-full text-sm text-gray-600 border rounded cursor-pointer p-2">
                            </div>

                            <div class="flex-1 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No. HP</label>
                                    <input type="text" name="no_telpon" value="<?= htmlspecialchars($user['no_telpon']); ?>"
                                        class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                        </div>

                        <!-- Tombol simpan & batal -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" onclick="toggleForm(false)"
                                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alamat -->
            <div id="alamat" class="hidden bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-lg font-semibold border-b pb-2 mb-4">Daftar Alamat</h1>

                <!-- Alamat Utama dari tabel user -->
                <div class="mb-6 border p-4 rounded bg-green-50">
                    <p class="font-medium"><?= htmlspecialchars($user['alamat'] ?? '-') ?></p>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($user['no_telpon'] ?? '-') ?></p>
                    <span class="text-xs text-green-600 font-semibold">Default</span>
                </div>

                <!-- Form tambah alamat -->
                <form method="POST" class="flex gap-3 mb-6">
                    <input type="text" name="alamat" placeholder="Alamat baru" required
                        class="flex-1 border px-3 py-2 rounded">
                    <input type="text" name="no_telpon" placeholder="No Telpon" required
                        class="w-48 border px-3 py-2 rounded">
                    <button type="submit" name="tambah_alamat"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Tambah
                    </button>
                </form>

                <!-- List alamat tambahan -->
                <div class="space-y-4">
                    <?php while ($a = mysqli_fetch_assoc($query_alamat)): ?>
                        <div
                            class="flex justify-between items-center border p-3 rounded <?= $a['is_default'] ? 'bg-green-50' : 'bg-gray-50' ?>">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($a['alamat']); ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($a['no_telpon']); ?></p>
                                <?php if ($a['is_default']): ?>
                                    <span class="text-xs text-green-600 font-semibold">Default</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <!-- Tombol hapus -->
                                <form method="POST" onsubmit="return confirm('Yakin hapus alamat ini?')">
                                    <input type="hidden" name="id_alamat" value="<?= $a['id_alamat'] ?>">
                                    <button type="submit" name="hapus_alamat"
                                        class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Riwayat Pembelian -->
            <div id="riwayat" class="hidden bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-2xl font-semibold border-b pb-3 mb-6">Riwayat Pembelian</h1>

                <?php if (empty($riwayat)): ?>
                    <p class="text-gray-500 text-center py-12">Belum ada transaksi.</p>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($riwayat as $id_checkout => $data): ?>
                            <div
                                class="bg-white border rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="font-medium text-gray-700">Transaksi #<?= $i++ ?></span>
                                    <span class="text-sm text-gray-400"><?= $data['tanggal'] ?></span>
                                </div>

                                <div class="space-y-3 border-t pt-3">
                                    <?php foreach ($data['items'] as $item): ?>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <img src="uploads/<?= $item['tumbnail'] ?>" alt="<?= $item['nama'] ?>"
                                                    class="w-20 h-20 rounded-lg object-cover border">
                                                <div>
                                                    <h3 class="font-semibold text-gray-800"><?= $item['nama'] ?></h3>
                                                    <p class="text-gray-500 text-sm">
                                                        <?= $item['qty'] ?> barang x
                                                        Rp<?= number_format($item['harga'], 0, ',', '.') ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="pl-4 border-l-2 border-gray-300 flex flex-col items-start">
                                                <span class="text-gray-500 text-sm">Total Harga</span>
                                                <span class="font-semibold text-green-600">
                                                    Rp<?= number_format($item['qty'] * $item['harga'], 0, ',', '.') ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="flex justify-end mt-4">
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="id_checkout" value="<?= $id_checkout ?>">
                                        <button type="submit"
                                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm">
                                            Beli Lagi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($totalPages > 1): ?>
    <div class="flex justify-center mt-6 space-x-2">
        <?php for ($hal = 1; $hal <= $totalPages; $hal++): ?>
            <a href="?page=riwayat&hal=<?= $hal ?>"
               class="px-3 py-1 border rounded <?= ($hal == $pageNow) ? 'bg-green-500 text-white' : 'bg-white hover:bg-gray-100' ?>">
                <?= $hal ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const page = params.get("page") || "biodata";
        document.getElementById("biodata").classList.toggle("hidden", page !== "biodata");
        document.getElementById("alamat").classList.toggle("hidden", page !== "alamat");
        document.getElementById("riwayat").classList.toggle("hidden", page !== "riwayat");

        function toggleForm(show) {
            document.getElementById('biodata-view').classList.toggle('hidden', show);
            document.getElementById('biodata-form').classList.toggle('hidden', !show);
        }
    </script>

</body>

</html>