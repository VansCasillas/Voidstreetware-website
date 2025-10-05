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
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

        <!-- scroll down section -->
        <section class="min-h-screen flex flex-col items-center justify-center relative">
            <!-- Teks Intro -->
            <div class="text-center" data-aos="fade-up" data-aos-duration="1200">
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">Welcome</h1>
                <p class="text-gray-600 text-sm sm:text-base max-w-md mx-auto">
                    Temukan koleksi fashion terbaik dengan gaya modern & nyaman.
                    Scroll ke bawah untuk mulai menjelajah!
                </p>
            </div>

            <!-- Scroll Down Button -->
            <a href="#homepage" id="scrollBtn"
                class="absolute bottom-20 left-1/2 transform -translate-x-1/2 animate-bounce">
                <i class="fa-solid fa-angles-down text-4xl"></i>
            </a>
        </section>


        <!-- Homepage Section -->
        <section id="homepage" class="homepage min-h-screen grid grid-cols-1 md:grid-cols-2 gap-6 items-center px-6">
            <!-- Gambar -->
            <div data-aos="zoom-in-up" data-aos-duration="1000" class="flex items-center justify-center">
                <img src="image/download.png" alt="Jacket"
                    class="max-h-[300px] sm:max-h-[400px] md:max-h-[450px] object-contain" />
            </div>

            <!-- Teks -->
            <div class="flex items-center justify-center">
                <div class="text-center md:text-left max-w-lg" data-aos="fade-up" data-aos-duration="1200">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
                        TEMUKAN GAYAMU DISINI!
                    </h1>
                    <p class="text-gray-700 font-medium mb-6 text-sm sm:text-base md:text-lg">
                        Jelajahi koleksi jaket terbaru kami yang nyaman dan stylish. Cocok untuk segala suasana, dari
                        hangout santai hingga petualangan di luar ruangan. Dapatkan kualitas premium dengan desain modern
                        yang membuatmu selalu tampil percaya diri.
                    </p>
                    <div class="flex justify-center md:justify-start">
                        <div class="box-style rounded-full bg-gradient-to-br from-black to-purple-700">
                            <p class="p-[4px] px-3 bg-white rounded-full text-center text-xs sm:text-sm">
                                <i class="fa-solid fa-star"></i> 4.9 Ratings based on 1,000+ reviews
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- jacket_section -->
        <section class="flex flex-col lg:flex-row p-4 sm:p-6 lg:p-8 gap-6 bg-gray-400">
            <div data-aos="fade-right" class="w-full lg:w-2/5 rounded-md p-2 sm:p-4">
                <div class="flex flex-wrap gap-2">
                    <div class="box-style-2 rounded-full bg-gradient-to-br from-black to-purple-700">
                        <p class="p-[4px] px-3 bg-white rounded-full text-center">Jacket Section</p>
                    </div>
                    <div class="box-style-2 rounded-full bg-gradient-to-br from-black to-purple-700">
                        <p class="p-[4px] px-3 bg-white rounded-full text-center">Top Stylish</p>
                    </div>
                </div>

                <div class="pt-4">
                    <h1 class="text-2xl sm:text-4xl font-bold mb-4">Your Daily Statement Place</h1>
                    <p class="text-white text-sm sm:text-base mb-6">Koleksi jaket yang dirancang untuk menjadi bagian
                        penting dari gaya harianmu. Dengan desian clean, bahan premium yang tahan lama, serta potongan
                        yang timeless, setiap jaket memberikan kesan on-point di setiap kesempatan dari hangout santai
                        sampai acara formal.</p>
                </div>

                <div data-aos="zoom-in-up"
                    class="box-style-3 rounded-full bg-gradient-to-br from-black to-purple-700 hover:bg-gradient-to-br hover:from-white">
                    <p class="p-[10px] px-6 bg-white rounded-full text-center hover:bg-black hover:text-white "><a
                            href="">Beli sekarang</a></p>
                </div>
            </div>

            <div class="hidden lg:block w-px bg-gray-700"></div>

            <div class="produk w-full lg:w-3/5 flex flex-col overflow-y-auto max-h-screen pr-2">

                <div data-aos="zoom-out"
                    class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 px-4 sm:px-6 md:px-20 py-4 mb-4 rounded-md bg-white shadow-md">
                    <img src="image/jaket1.png" alt="Jacket" class="max-h-[200px] w-full sm:w-auto object-contain" />
                    <div class="pt-2 sm:pt-4">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Bomber Jacket</h1>
                        <p class="text-gray-600 text-sm sm:text-base">Jaket Bomber dengan desain clean dan detail
                            sederhana. dibuat dari bahan tebal berkalitas dengan resleting premium, coock untuk tampilan
                            streetwear yang stylist dan nyaman sehari-hari.</p>
                    </div>
                </div>

                <div data-aos="zoom-in"
                    class="flex flex-col-reverse sm:flex-row items-center gap-4 sm:gap-6 px-4 sm:px-6 md:px-20 py-4 mb-4 rounded-md bg-white shadow-md">
                    <div class="pt-2 sm:pt-4">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Jaket Sporty Black-White</h1>
                        <p class="text-gray-600 text-sm sm:text-base">Jaket bernuansa sporty dengan warna dasar hitam
                            dan motif garis “V” besar berwarna putih di bagian dada dan lengan. Menggunakan resleting
                            penuh, kerah tegak, serta karet elastis pada pergelangan tangan dan bagian bawah jaket,
                            cocok untuk tampilan kasual aktif.</p>
                    </div>
                    <img src="image/jaket5.png" alt="Jacket" class="max-h-[200px] w-full sm:w-auto object-contain" />
                </div>

                <div data-aos="zoom-out"
                    class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 px-4 sm:px-6 md:px-20 py-4 mb-4 rounded-md bg-white shadow-md">
                    <img src="image/jaket3.png" alt="Jacket" class="max-h-[200px] w-full sm:w-auto object-contain" />
                    <div class="pt-2 sm:pt-4">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4"> Jaket Hoodie Outdoor Navy-White</h1>
                        <p class="text-gray-600 text-sm sm:text-base">Jaket model windbreaker dengan kombinasi warna
                            navy dan abu-abu gelap, dilengkapi aksen garis putih tipis yang membentuk pola melengkung di
                            bagian depan dan lengan. Memiliki resleting penuh, kerah tegak, serta karet elastis di
                            pergelangan tangan untuk kenyamanan dan perlindungan dari angin.</p>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row items-center gap-4 sm:gap-6 px-4 sm:px-6 md:px-20 py-4 mb-4 rounded-md bg-white shadow-md">
                    <div class="pt-2 sm:pt-4">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Coach Jacket</h1>
                        <p class="text-gray-600 text-sm sm:text-base">Jaket outdoor dengan warna navy dan aksen putih di
                            bagian bahu hingga samping badan. Dilengkapi hoodie dengan tali serut, resleting penuh,
                            serta kancing snap untuk perlindungan ekstra. Terdapat saku besar dengan penutup di sisi
                            depan, karet elastis di pergelangan tangan, dan penyesuaian tali di bagian bawah.</p>
                    </div>
                    <img src="image/jaket10.png" alt="Jacket"
                        class="max-h-[200px] w-full sm:w-auto object-contain" />
                </div>

                <div
                    class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 px-4 sm:px-6 md:px-20 py-4 mb-4 rounded-md bg-white shadow-md">
                    <img src="image/jaket7.png" alt="Jacket" class="max-h-[200px] w-full sm:w-auto object-contain" />
                    <div class="pt-2 sm:pt-4">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Hooded Jacket</h1>
                        <p class="text-gray-600 text-sm sm:text-base">Jaket kasual warna beige polos dengan desain
                            minimalis. Menggunakan resleting penuh, dua saku samping, serta detail patch kecil di dada
                            dan lengan. Dilengkapi karet elastis di pergelangan tangan dan tali serut di bagian bawah
                            untuk menyesuaikan fit. Cocok untuk gaya santai sehari-hari.</p>
                    </div>
                </div>
            </div>

            <!-- Duplicate item blocks should be refactored similarly -->
            </div>
        </section>

        <!-- hoodie_section -->
        <section class="min-h-screen flex flex-col items-center px-4 md:px-0 sm:p-6 lg:p-8">
            <div data-aos="zoom-in" class="text-center w-full md:w-3/5 p-6 md:p-10">
                <div class="box-style-2 rounded-full bg-gradient-to-br from-black to-purple-700 mb-4 inline-block">
                    <p class="p-[4px] px-3 bg-white rounded-full text-center">Hoodie Section</p>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">We Don't Just Offer Jacket — We Offer More For You</h1>
                <p class="text-gray-600 mb-6 text-sm sm:text-base">
                    Jika kamu tertarik dengan Hoodie, kami memiliki beberapa Hoodie yang bisa kami rekomendasikan kepada
                    kamu.
                </p>
            </div>

            <div class="w-full md:w-4/5 space-y-6">
                <!-- Konten 1 -->
                <div data-aos="fade-up-right"
                    class="flex flex-col md:flex-row items-center gap-6 px-4 md:px-10 py-4 rounded-md bg-gray-100 shadow-md">
                    <img src="image/__ORIGINAL___CHAMBRE_DE_LA_VAIN_BEDS_STAR_BLACK.png" alt="Jacket"
                        class="w-full max-w-[200px]" />
                    <div class="pt-4 w-full">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">CHMB - Zipper Hoodie Minimalist</h1>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Hoodie dari CHMB yang dikenal dengan desain clean dan fungsional. Terbuat dari cotten fleece
                            berkualitas dengan detail kantong kangaroo, cocok untuk mendukung aktifitasmu tanpa
                            menggalkan kesan stylish.
                        </p>
                    </div>
                </div>

                <!-- Konten 2 -->
                <div data-aos="fade-up-left"
                    class="flex flex-col-reverse md:flex-row items-center gap-6 px-4 md:px-10 py-4 rounded-md bg-gray-500 shadow-md">
                    <div class="pt-4 w-full">
                        <h1 class="text-white text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Telepati - Hoodie Oversize
                            White Edition</h1>
                        <p class="text-white text-sm sm:text-base">
                            Hoodie dengan potongan oversize khas Telepati yang menghadirkan kesan bold namun tetap
                            minimalis. Dibuat dari fleece premium yang tebal dan lembut, hoodie ini nyaman di pakai
                            seharian dengan vibe streetwear yang kuat.
                        </p>
                    </div>
                    <img src="image/TELEPATICHE_-_KISS_ME_WHITE.png" alt="Jacket" class="w-full max-w-[200px]" />
                </div>

                <!-- Konten 3 -->
                <div data-aos="fade-up-right"
                    class="flex flex-col md:flex-row items-center gap-6 px-4 md:px-10 py-4 rounded-md bg-gray-100 shadow-md">
                    <img src="image/Preface_Signature_Basic_Zip_Hoodie_Black.png" alt="Jacket"
                        class="w-full max-w-[200px]" />
                    <div class="pt-4 w-full">
                        <h1 class="text-xl sm:text-2xl font-bold mb-2 sm:mb-4">Preface - Pullover Hoodie Monochrome</h1>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Hoodie pullover signature dari Preface yng mengusung konsep monokrom minimalis. tanpa
                            resleting, dengan aksen logo kecil yang subtle, memberikan tampilan casual yang elegan dan
                            nyaman di berbagai kesempatan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- comment_section -->
        <section class="lg:flex-row p-4 sm:p-6 lg:p-8 gap-6 bg-gray-400">
            <!-- Judul Section -->
            <div class="w-full text-center mb-6">
                <h2 class="text-3xl font-bold">Apa Kata Pelanggan Kami?</h2>
                <p class="text-gray-700">Ulasan asli dari pembeli kami</p>
            </div>

            <!-- Container Testimonial -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full">

                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/agus.png" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Kualitas produk sangat bagus, pengiriman cepat, dan pelayanan
                        ramah!"</p>
                    <h3 class="font-bold">Agus Saputra</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/ewing.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Top 7 penampakan Toko yang bagus. Pelayanannya juga wajib
                        masuk Top 5 paling bagus."</p>
                    <h3 class="font-bold">Ewing 144p</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/my_bini.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Desainnya keren banget, lucu juga dan sesuai foto. Worth it
                        banget beli
                        di sini!"</p>
                    <h3 class="font-bold">Cwe pling imut</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐</span>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/sutan.jpg" alt="User" class="w-16 h-16 rounded mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Worth it banget cuy hoodienya, BTW ninym cantik banget asli"
                    </p>
                    <h3 class="font-bold">Sutan suami Nynim</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/parto.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Barang sesuai deskripsi, packing rapi, dan pengiriman tepat
                        waktu."</p>
                    <h3 class="font-bold">Andhika kangen band</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/reza_k.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Barang sesuai deskripsi dan packing rapi. Klo ada yang rating
                        bintang 1 haters sih itu."</p>
                    <h3 class="font-bold">Reza Kecap</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/siregar.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"Barang sesuai deskripsi, packing rapi, dan pengiriman tepat
                        waktu. klo bisa bonus mainan kapal kapalan"</p>
                    <h3 class="font-bold">Budiono siregar</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center text-center">
                    <img src="image/bahlil.jpg" alt="User" class="w-16 h-16 rounded-full mb-4 object-cover">
                    <p class="text-gray-700 italic mb-3">"barangnya terlalu sesuai deskripsi. Respon terlalu bagus
                        harusnya bisa diundur-undur lagi. Harga dan Kualitas juga berbanding lurus taktik marketingnya
                        kurang diasah ini mah"</p>
                    <h3 class="font-bold">Bahri sodara bahlil</h3>
                    <span class="text-sm text-gray-500">⭐⭐⭐⭐⭐</span>
                </div>

            </div>
        </section>

        <!-- yapping_section -->
        <section class="lg:flex-row p-4 sm:p-6 lg:p-8 gap-6">
            <!-- Judul Section -->
            <div class="w-full text-center mb-6">
                <h1 class="text-3xl font-bold">Try It NOW. Scale When You're Looks Cool.</h1>
                <p class="text-gray-700">Get started without limits. Explore all features at your own pace — upgrade
                    only when your business grows</p>
            </div>
        </section>

        <script src="script.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init();

            document.getElementById("scrollBtn").addEventListener("click", function(e) {
                e.preventDefault();

                const target = document.querySelector("#homepage");
                const targetPosition = target.getBoundingClientRect().top + window.scrollY;
                const startPosition = window.scrollY;
                const distance = targetPosition - startPosition;
                const duration = 1000;
                let start = null;

                function step(timestamp) {
                    if (!start) start = timestamp;
                    let progress = timestamp - start;
                    let percent = Math.min(progress / duration, 1);

                    // easing biar smooth (easeInOutQuad)
                    let ease = percent < 0.5 ?
                        2 * percent * percent :
                        -1 + (4 - 2 * percent) * percent;

                    window.scrollTo(0, startPosition + distance * ease);

                    if (progress < duration) {
                        window.requestAnimationFrame(step);
                    }
                }

                window.requestAnimationFrame(step);

                // hapus #homepage dari URL
                history.replaceState(null, null, " ");
            });
        </script>

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