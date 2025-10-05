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
            <!-- Map -->
            <div class="flex items-center justify-center">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.9068011521313!2d105.32703787482045!3d-5.1187194948583405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40bc1d176fd9af%3A0x804b3b4145837ec4!2sSMK%20Negeri%203%20Metro!5e0!3m2!1sid!2sid!4v1754739108202!5m2!1sid!2sid"
                    class="w-full h-72 md:h-[450px] rounded-lg" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Konten -->
            <div class="flex flex-col justify-center space-y-6">
                <div class="text-center md:text-left space-y-3">
                    <h1 class="text-2xl sm:text-4xl font-bold">TEMUKAN KAMI DISINI!</h1>
                    <h2 class="sm:text-2xl font-semibold">Kota Metro</h2>
                    <p>Jl Kemiri, 15 A Iring Mulyo, Metro Timur, Kota Metro, 41103</p>
                </div>

                <div class="text-center md:text-left space-y-2">
                    <h1 class="font-semibold text-lg">Ikuti Kami Disini</h1>
                    <p><i class="fa-brands fa-instagram"></i> Void_streetware</p>
                    <p><i class="fa-brands fa-facebook"></i> Void_streetware</p>
                    <p><i class="fa-brands fa-x-twitter"></i> Void_streetware</p>
                    <p><i class="fa-brands fa-whatsapp"></i> 086435177129</p>
                </div>

                <div class="text-center md:text-left space-y-1">
                    <h3 class="font-semibold text-gray-800">Jam Operasional</h3>
                    <p class="text-gray-700">Senin - Jumat: 09:00 - 18:00</p>
                    <p class="text-gray-700">Sabtu: 09:00 - 14:00</p>
                    <p class="text-gray-700">Minggu: Tutup</p>
                </div>
            </div>
        </section>



        <script src="script.js"></script>

    </main>

    <footer class="bg-gray-400 text-center p-4 border-t text-white text-sm">

        &copy; 2025 Voidstreetware Project. All rights reserved.
    </footer>
</body>

</html>