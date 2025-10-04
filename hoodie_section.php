<?php
include 'koneksi.php';
cek_user(); //pastikan user login

$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));

$cartCountQuery = mysqli_query($koneksi, "SELECT SUM(qty) as total FROM cart WHERE id_user='$id_user'");
$cartCount = mysqli_fetch_assoc($cartCountQuery)['total'] ?? 0;

// proses tambah ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produk'])) {
    $id_produk = (int) ($_POST['id_produk'] ?? 0);
    $qty = max(1, (int) ($_POST['qty'] ?? 1));
    $size = mysqli_real_escape_string($koneksi, $_POST['size'] ?? '');

    if ($id_produk > 0 && $size !== '') {
        // cek apakah produk + size sudah ada di cart
        $cek = mysqli_query($koneksi, "SELECT * FROM cart 
                                       WHERE id_user='$id_user' 
                                       AND id_produk='$id_produk' 
                                       AND size='$size'");

        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($koneksi, "UPDATE cart 
                                    SET qty = qty + $qty 
                                    WHERE id_user='$id_user' 
                                    AND id_produk='$id_produk' 
                                    AND size='$size'");
        } else {
            mysqli_query($koneksi, "INSERT INTO cart (id_user, id_produk, qty, size) 
                                    VALUES ('$id_user', '$id_produk', '$qty', '$size')");
        }

        echo "<script>
            window.onload = function() {
                showCustomAlert('Produk berhasil ditambahkan ke keranjang!');
            }
            </script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk, data tidak valid');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voidstreetware</title>
    <link rel="icon" href="resource/Voidstreetware_logo.png" type="image/png" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="relative">
    <main class="bg-gray-200">
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
                        <a href="cart.php" class="nav-right relative inline-block">
                            <i class="fa-solid fa-cart-arrow-down"></i>
                            <?php if ($cartCount > 0): ?>
                                <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-bold min-w-[16px] h-[16px] flex items-center justify-center px-1 rounded-full">
                                    <?= ($cartCount > 99) ? '99+' : $cartCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <li class="relative group">
                        <button class="flex items-center inline-block focus:outline-none">
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

        <section class="max-w-6xl mx-auto px-4 py-8">
            <!-- Carousel -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full mx-auto">
                <!-- Carousel Wrapper -->
                <div class="relative overflow-hidden h-auto md:h-64">
                    <div id="carousel" class="flex transition-transform duration-500 ease-in-out h-full">
                        <?php
                        $jacket_carousel = mysqli_query($koneksi, "SELECT * FROM produk WHERE is_carousel_hoodie = 1");
                        while ($slide = mysqli_fetch_assoc($jacket_carousel)) {
                        ?>
                            <!-- Slide -->
                            <div class="flex flex-col md:flex-row min-w-full flex-shrink-0 h-auto md:h-full bg-white">
                                <img src="uploads/<?= $slide['tumbnail_produk']; ?>"
                                    class="w-full md:w-1/2 h-64 md:h-full object-contain">
                                <div class="w-full md:w-1/2 p-4 md:p-6 flex flex-col justify-center">
                                    <h2 class="text-xl md:text-2xl font-bold mb-2"><?= $slide['nama_produk']; ?></h2>
                                    <p class="text-gray-600 mb-4">
                                        Rp <?= number_format($slide['harga'], 0, ',', '.'); ?>
                                    </p>
                                    <button
                                        class="add-to-cart bg-black text-white w-full md:w-auto px-3 py-2 rounded hover:bg-purple-700 transition"
                                        data-id="<?= $slide['id_produk']; ?>">
                                        Beli Sekarang
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Dots Navigation -->
                <div class="flex justify-center mt-4 mb-2 space-x-2">
                    <button class="dot bg-gray-300 rounded-full h-3 w-3"></button>
                    <button class="dot bg-gray-300 rounded-full h-3 w-3"></button>
                    <button class="dot bg-gray-300 rounded-full h-3 w-3"></button>
                    <button class="dot bg-gray-300 rounded-full h-3 w-3"></button>
                </div>
            </div>

            <h2 class="text-2xl sm:text-3xl font-bold mb-6 mt-6">Hoodie Collection</h2>

            <!-- Product -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='hoodie'");
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <!-- Produk -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition flex flex-col">
                        <img src="uploads/<?= $data['tumbnail_produk']; ?>" class="w-full h-64 object-cover">
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="text-lg font-semibold"><?= $data['nama_produk']; ?></h3>
                            <p class="text-gray-500 text-sm mb-2">Rp <?= number_format($data['harga'], 0, ',', '.'); ?>
                            </p>
                            <button
                                class="add-to-cart bg-black text-white px-2 py-2 rounded hover:bg-purple-700 transition mt-auto"
                                data-id="<?= $data['id_produk']; ?>"
                                data-name="<?= htmlspecialchars($data['nama_produk'], ENT_QUOTES); ?>"
                                data-price="<?= $data['harga']; ?>"
                                data-image="uploads/<?= $data['tumbnail_produk']; ?>">
                                + keranjang
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div id="custom-alert-container" class="fixed right-5 flex flex-col gap-2 z-50" style="top: 64px;"></div>
        </section>

        <script src="script.js" defer></script>

    </main>

    <footer class="bg-gray-400 text-center p-4 border-t text-white text-sm">
        <div
            class="flex flex-col sm:flex-row justify-between items-center sm:items-start max-w-6xl mx-auto px-6 sm:px-8 mb-2 gap-8">

            <!-- Bagian Kiri -->
            <div class="sm:w-1/3 text-center sm:text-left">
                <h1 class="text-sm sm:text-md font-bold mb-4">TEMUKAN GAYAMU DISINI!</h1>
                <p class="text-white mb-4">
                    Voidstreetware menghadirkan koleksi streetwear terkini dengan kualitas premium
                    dan desain eksklusif.
                </p>
                <div class="flex justify-center sm:justify-start">
                    <a href="index.php">
                        <img src="resource/Voidstreetware.png" alt="Logo" class="h-15">
                    </a>
                </div>
            </div>

            <!-- Bagian Kanan -->
            <div class="flex flex-col sm:flex-row gap-8 text-center sm:text-left w-full sm:w-auto">
                <div>
                    <h3 class="font-semibold mb-2">Social links</h3>
                    <ul class="space-y-1">
                        <li><a href="" class="hover:text-black">Instagram</a></li>
                        <li><a href="" class="hover:text-black">Facebook</a></li>
                        <li><a href="" class="hover:text-black">X/Twitter</a></li>
                        <li><a href="" class="hover:text-black">TikTok</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-2">Contact</h3>
                    <ul class="space-y-1">
                        <li><a href="" class="hover:text-black">Email</a></li>
                        <li><a href="" class="hover:text-black">WhatsApp</a></li>
                        <li><a href="" class="hover:text-black">Telegram</a></li>
                        <li><a href="" class="hover:text-black">Live Chat</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <p class="mt-4">&copy; 2025 Voidstreetware Project. All rights reserved.</p>
    </footer>
</body>
<!-- Popup / Offcanvas (DIBUAT JADI FORM, tampilan tetap sama) -->
<form method="POST" id="productPopup"
    class="fixed top-0 right-0 h-full w-80 backdrop-blur-lg bg-white/70 shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <div class="p-4 flex flex-col h-full">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-2">
            <h2 id="popupTitle" class="text-lg font-semibold">Produk</h2>
            <button type="button" id="closePopup" class="text-gray-600 hover:text-black"> <i
                    class="fa-solid fa-xmark"></i> </button>
        </div>

        <!-- Isi Popup -->
        <div class="flex-1 overflow-y-auto py-4">
            <img id="popupImage" src="" alt="Produk" class="w-full h-40 object-contain rounded-md mb-4">
            <p id="popupPrice" class="text-gray-700 font-semibold mb-4">Rp 0</p>

            <!-- Pilih Ukuran -->
            <div class="mb-4">
                <label for="sizeSelect" class="block text-sm font-medium mb-1">Pilih Ukuran</label>
                <select id="sizeSelect" name="size" class="w-full border rounded px-2 py-2">
                    <option class="text-sm" value="S">S</option>
                    <option class="text-sm" value="M">M</option>
                    <option class="text-sm" value="L">L</option>
                    <option class="text-sm" value="XL">XL</option>
                </select>
            </div>

            <!-- Pilih Jumlah -->
            <div class="mb-4">
                <label for="qtyInput" class="block text-sm font-medium mb-1">Jumlah</label>
                <input id="qtyInput" name="qty" type="number" value="1" min="1" class="w-full border rounded px-2 py-2">
            </div>

            <!-- warning kalau tombol dari carousel tidak punya id_produk -->
            <p id="popupWarn" class="hidden text-xs text-red-600">Produk ini belum terhubung ke database.</p>
        </div>

        <!-- Hidden untuk submit -->
        <input type="hidden" name="id_produk" id="popupId">
        <input type="hidden" name="add_to_cart" value="1">

        <!-- Footer -->
        <div class="border-t pt-2">
            <button id="addToCartPopup" type="submit"
                class="w-full bg-black text-white py-2 rounded hover:bg-purple-700 transition">
                Tambahkan ke Keranjang
            </button>
        </div>
    </div>
</form>

</html>