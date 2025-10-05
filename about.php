<?php
include 'koneksi.php';
cek_user(); // kalau halaman ini khusus untuk user

$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));

$cartCountQuery = mysqli_query($koneksi, "SELECT SUM(qty) as total FROM cart WHERE id_user='$id_user'");
$cartCount = mysqli_fetch_assoc($cartCountQuery)['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voidstreetware</title>
    <link rel="icon" href="image/Voidstreetware_logo.png" type="image/png" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="relative">
    <main>
        <nav class="sticky top-0 z-50 backdrop-blur-lg bg-white/70 shadow-md">
            <div class="flex items-center justify-between px-6 py-4">

                <!-- Logo -->
                <div class="flex-1 text-center md:text-left">
                    <a href="index.php">
                        <img src="image/Voidstreetware.png" alt="Logo" class="h-10 mx-auto md:mx-0">
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

        <section class="min-h-screen grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-12 items-center">
            <div>
                <div align="center">
                    <img src="resource/Voidstreetware.png" width="400px" class="mb-8">
                </div>
                <h1>
                    Dari atasan kasual yang nyaman, jaket fungsional, hingga outherwear dengan sentuhan premium setiap
                    produk dikerjakan dengan material
                    pilihan, detail yang presisi, dan standar kualitas yang tidak kami kompromikan. Kami ingin
                    memastikan setiap item Void Streetware bukan
                    hanya terlihat keren, tapi juga tahan lama dan nyaman di pakai setiap hari.
                </h1>
                <br>
                <h1>
                    Nama Void sendiri memiliki arti "ruang kosong" tapi bagi kami, ruang itu bukan kekosongan yang
                    hampa. Void adalah canvas, yang
                    kami isi dengan desain, cerita, dan semangat. Dan ketika pakaian kami berada ditangan (dan dibadan)
                    kamu, canvas itu menjadi
                    milikmu sepenuhnya, untuk kamu isi dengan cerita hidupmu.
                </h1>
                <br>
                <h1>
                    Kami hadir bukan untuk mengikuti tren, tapi untuk membangun budyaa. Void Streetware adalah tempat
                    dimana gaya bertemu sikap,
                    kenyamanan bertemu keaslian, dan pakaian menjadi bahasa yang berbicara tanpa kata-kata.
                </h1>
            </div>
            <div>
                <h1>
                    Void streetwear lahir dari ide sederhana: menciptakan pakaian yang tidak hanya dipakai, tapi juga
                    menggambarkan siapa pemakainya.
                    Di tengah arus tren yang terus berubah, kami memilih untuk membangun identitas sendiri, sebuah
                    perbaduan
                    antara estetika minimalis,
                    kualitas premium, dan jiwa street cultur yang berbeda.
                </h1>
                <br>
                <h1>
                    Kami percaya streetwear bukan sekedar fashion. Tapi bentuk ekspresi diri yang kuat.Setiap potongan
                    koleksi kami terinspirasi dari
                    kehidupan manusia: dinamis, penuh energi, dan tidak terikat aturan. Desain kami sengaja dibuat clean
                    dan
                    bold, dengan warna-warna
                    yang tegas dan detail yang fungsional, agar bisa menyatu dengan gaya hidup modern tanpa kehilangan
                    karakter.
                </h1>
                <br>
                <div class="grid grid-cols-2 py-4 gap-6 text-center">
                    <div class="">
                        <h1 class="text-2xl font-bold">45+</h1>
                        <p>Produk best seller</p>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">200+</h1>
                        <p>Produk berkualitas</p>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">2000+</h1>
                        <p>Pelanggan senang</p>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold">10+</h1>
                        <p>Kolaborasi dengan Artis ternama</p>
                    </div>
                </div>
            </div>
        </section>

        <script src="script.js"></script>

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

</html>