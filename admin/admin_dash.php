<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['admin'])) {
    header('Location: ../login.php');
    exit;
}

// update carousel
if (isset($_POST['update_carousel'])) {
    // reset dulu
    mysqli_query($koneksi, "UPDATE produk SET is_carousel_jacket = 0, is_carousel_hoodie = 0");

    // update jacket
    if (!empty($_POST['carousel_jacket'])) {
        foreach ($_POST['carousel_jacket'] as $id) {
            $id = (int)$id;
            mysqli_query($koneksi, "UPDATE produk SET is_carousel_jacket = 1 WHERE id_produk = $id");
        }
    }

    // update hoodie
    if (!empty($_POST['carousel_hoodie'])) {
        foreach ($_POST['carousel_hoodie'] as $id) {
            $id = (int)$id;
            mysqli_query($koneksi, "UPDATE produk SET is_carousel_hoodie = 1 WHERE id_produk = $id");
        }
    }

    echo "<script>alert('Carousel berhasil diperbarui!'); window.location.href='';</script>";
}

// reset carousel
if (isset($_POST['reset_carousel'])) {
    mysqli_query($koneksi, "UPDATE produk SET is_carousel_jacket = 0, is_carousel_hoodie = 0");
    echo "<script>alert('Carousel berhasil direset!'); window.location.href='';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voidstreetware - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="bg-gray-50 flex min-h-screen">
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
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="bg-white py-4 shadow-sm">
            <div class="px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
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

        <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Product Jacket -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Jacket</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='jacket'")); ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-vest text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Product Hoodie -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Hoodie</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='hoodie'")); ?>
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-tshirt text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total User -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total User</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM user")); ?>
                            </p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-users text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Produk Terjual -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Produk Terjual</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">
                                <?php
                                $produkTerjual = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(qty) as total FROM checkout_item"))['total'] ?? 0;
                                echo $produkTerjual;
                                ?>
                            </p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-shopping-basket text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Omset Card -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-md p-6 text-white mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium">Total Omset</p>
                        <p class="text-3xl font-bold mt-2">
                            <?php
                            $omset = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_harga) as omset FROM checkout"))['omset'] ?? 0;
                            echo "Rp " . number_format($omset, 0, ',', '.');
                            ?>
                        </p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-full">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Carousel Management -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Kelola Carousel</h2>
                
                <form method="post">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Jacket Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-vest text-blue-500 mr-2"></i>
                                Carousel Jacket
                            </h3>
                            <div class="space-y-3">
                                <?php
                                $jacket = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='Jacket'");
                                while ($p = mysqli_fetch_assoc($jacket)) {
                                ?>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition checkbox-item">
                                        <input type="checkbox" name="carousel_jacket[]" value="<?= $p['id_produk']; ?>"
                                            class="w-5 h-4   text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            <?= $p['is_carousel_jacket'] ? 'checked' : ''; ?>>
                                        <span class="ml-3 text-gray-700 font-medium"><?= $p['nama_produk']; ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Hoodie Section -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-tshirt text-green-500 mr-2"></i>
                                Carousel Hoodie
                            </h3>
                            <div class="space-y-3">
                                <?php
                                $hoodie = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='Hoodie'");
                                while ($p = mysqli_fetch_assoc($hoodie)) {
                                ?>
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition checkbox-item">
                                        <input type="checkbox" name="carousel_hoodie[]" value="<?= $p['id_produk']; ?>"
                                            class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                            <?= $p['is_carousel_hoodie'] ? 'checked' : ''; ?>>
                                        <span class="ml-3 text-gray-700 font-medium"><?= $p['nama_produk']; ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit" name="update_carousel"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Update Carousel
                        </button>

                        <button type="submit" name="reset_carousel"
                            class="px-6 py-2 bg-red-100 text-red-600 font-semibold rounded-lg hover:bg-red-200 transition flex items-center"
                            onclick="return confirm('Yakin ingin reset semua carousel?')">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Reset Carousel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white py-4 border-t border-gray-200">
            <div class="px-8 text-center text-gray-500 text-sm">
                &copy; 2025 Voidstreetware. All rights reserved.
            </div>
        </footer>
    </main>

    <script>
        // Menambahkan efek animasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const statsCards = document.querySelectorAll('.bg-white.rounded-xl');
            statsCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-slide-in');
            });
        });
    </script>
</body>

</html>