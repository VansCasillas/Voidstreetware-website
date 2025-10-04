<?php
include 'koneksi.php';
cek_user();
$id_user = $_SESSION['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));

$cartCountQuery = mysqli_query($koneksi, "SELECT SUM(qty) as total FROM cart WHERE id_user='$id_user'");
$cartCount = mysqli_fetch_assoc($cartCountQuery)['total'] ?? 0;

// ====================
// PROSES CHECKOUT
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $alamat = ($_POST['alamatSelect'] === 'new') ? $_POST['newAlamat'] : $_POST['alamatSelect'];
    $no_telpon = ($_POST['telpSelect'] === 'new') ? $_POST['newTelp'] : $_POST['telpSelect'];

    $selected = $_POST['selected_cart'] ?? [];

    if (empty($selected)) {

        
        echo "<script>alert('Tidak ada produk yang dipilih.');</script>";
        exit;
    }

    // escape ID_cart biar aman
    $selected_ids = implode(',', array_map('intval', $selected));

    $cartQuery = mysqli_query(
        $koneksi,
        "SELECT cart.id_cart, cart.qty, produk.id_produk, produk.harga 
        FROM cart 
        INNER JOIN produk ON cart.id_produk = produk.id_produk 
        WHERE cart.id_user = '$id_user' 
        AND cart.id_cart IN ($selected_ids)"
    );

    // Jika alamat baru diisi, insert ke tabel user_alamat
    if ($_POST['alamatSelect'] === 'new' && !empty($_POST['newAlamat'])) {
        $newAlamatEscaped = mysqli_real_escape_string($koneksi, $_POST['newAlamat']);
        $noTelpEscaped = mysqli_real_escape_string($koneksi, $no_telpon);
        mysqli_query(
            $koneksi,
            "INSERT INTO user_alamat (id_user, alamat, no_telpon) VALUES (
                '$id_user',
                '$newAlamatEscaped',
                '$noTelpEscaped'
            )"
        );
    }

    // Jika nomor telepon baru diisi, insert ke tabel user_alamat
    if ($_POST['telpSelect'] === 'new' && !empty($_POST['newTelp'])) {
        $newTelpEscaped = mysqli_real_escape_string($koneksi, $_POST['newTelp']);
        $alamatEscaped = mysqli_real_escape_string($koneksi, $alamat);
        mysqli_query(
            $koneksi,
            "INSERT INTO user_alamat (id_user, alamat, no_telpon) VALUES (
                '$id_user',
                '$alamatEscaped',
                '$newTelpEscaped'
            )"
        );
    }

    if (mysqli_num_rows($cartQuery) > 0) {
        $total_harga = 0;
        $cart_items = [];

        while ($row = mysqli_fetch_assoc($cartQuery)) {
            $subtotal = $row['harga'] * $row['qty']; // total harga per produk
            $total_harga += $subtotal;

            $cart_items[] = [
                'id_produk' => $row['id_produk'],
                'qty' => $row['qty'],
                'harga_item' => $row['harga'] // harga satuan
            ];
        }

        $insertCheckout = mysqli_query(
            $koneksi,
            "INSERT INTO checkout (id_user, total_harga, alamat, no_telpon) 
            VALUES ('$id_user', '$total_harga', '$alamat', '$no_telpon')"
        );

        if ($insertCheckout) {
            $id_checkout = mysqli_insert_id($koneksi);
            foreach ($cart_items as $item) {
                mysqli_query(
                    $koneksi,
                    "INSERT INTO checkout_item (id_checkout, id_produk, qty, harga_item) 
                    VALUES ('$id_checkout', '{$item['id_produk']}', '{$item['qty']}', '{$item['harga_item']}')"
                );
            }

            mysqli_query($koneksi, "DELETE FROM cart WHERE id_user='$id_user' AND id_cart IN ($selected_ids)");
            echo "<script>alert('Checkout berhasil! Pesanan akan diproses :)');window.location.href = 'cart.php';</script>";
            exit;
        } else {
            echo "<script>alert('Checkout gagal, coba lagi.');</script>";
        }
    } else {
        echo "<script>alert('Keranjang kosong, tidak bisa checkout.');</script>";
    }
}

// ====================
// AJAX update qty
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty_ajax'])) {
    $id_cart = (int) ($_POST['id_cart'] ?? 0);
    $qty = max(1, (int) ($_POST['qty'] ?? 1));
    if ($id_cart > 0) {
        mysqli_query($koneksi, "UPDATE cart SET qty=$qty WHERE id_cart=$id_cart AND id_user=$id_user");
    }
    echo 'ok';
    exit;
}

// ====================
// Hapus / tambah / kurangi
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_qty_ajax']) && !isset($_POST['checkout'])) {
    $id_cart = (int) ($_POST['id_cart'] ?? 0);
    if (isset($_POST['tambah']) && $id_cart > 0)
        mysqli_query($koneksi, "UPDATE cart SET qty=qty+1 WHERE id_cart=$id_cart AND id_user=$id_user");
    if (isset($_POST['kurangi']) && $id_cart > 0)
        mysqli_query($koneksi, "UPDATE cart SET qty=GREATEST(qty-1,1) WHERE id_cart=$id_cart AND id_user=$id_user");
    if (isset($_POST['hapus']) && $id_cart > 0)
        mysqli_query($koneksi, "DELETE FROM cart WHERE id_cart=$id_cart AND id_user=$id_user");
    if (isset($_POST['hapus_semua']))
        mysqli_query($koneksi, "DELETE FROM cart WHERE id_user=$id_user");
    header("Location: cart.php");
    exit;
}

// Ambil data user & cart
$userData = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'"));
$cartQuery = mysqli_query(
    $koneksi,
    "SELECT cart.id_cart, cart.qty, cart.size, produk.nama_produk, produk.harga, produk.tumbnail_produk 
     FROM cart INNER JOIN produk ON cart.id_produk = produk.id_produk 
     WHERE cart.id_user='$id_user'"
);
$alamatQuery = mysqli_query($koneksi, "SELECT alamat FROM user_alamat WHERE id_user='$id_user'");
$telpQuery = mysqli_query($koneksi, "SELECT no_telpon FROM user_alamat WHERE id_user='$id_user'");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Voidstreetware</title>
    <link rel="icon" href="resource/Voidstreetware_logo.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class=" bg-gray-200">

    <main>
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

        <section class="min-h-screen bg-gray-200 py-8 px-4">
            <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-6">
                <!-- KIRI: CART ITEMS -->
                <div class="flex-1 bg-white p-6 rounded shadow">
                    <?php if (mysqli_num_rows($cartQuery) == 0): ?>
                        <div class="text-center py-16 text-gray-500">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" class="mx-auto w-32 mb-4">
                            <h1 class="text-lg font-semibold">Keranjang kosong</h1>
                            <p class="mt-2">Yuk, isi dengan jacket atau hoodie yang kamu mau!</p>
                            <a href="jacket_section.php"
                                class="mt-4 inline-block px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Belanja
                                Jacket</a>
                            <a href="hoodie_section.php"
                                class="mt-4 inline-block px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Belanja
                                Hoodie</a>
                        </div>
                    <?php else: ?>
                        <h2 class="text-xl font-semibold mb-4">Keranjang Belanja</h2>
                        <div class="flex items-center justify-between mb-4">
                            <label class="flex items-center gap-2"><input type="checkbox" id="select-all" class="w-5 h-5"
                                    checked><span class="text-gray-700 font-medium">Pilih Semua</span></label>
                            <form method="POST"><button type="submit" name="hapus_semua"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">Hapus
                                    Semua</button>
                            </form>
                        </div>
                        <div id="cart-items-container" class="space-y-4">
                            <?php while ($data = mysqli_fetch_assoc($cartQuery)): ?>
                                <div
                                    class="flex flex-col md:flex-row md:items-center md:justify-between border-b pb-4 cart-item gap-2 md:gap-4">
                                    <!-- BAGIAN PRODUK -->
                                    <div class="flex items-start md:items-center gap-4">
                                        <input type="checkbox" class="cart-checkbox w-5 h-5" data-qty="<?= $data['qty']; ?>"
                                            data-price="<?= $data['harga']; ?>" checked>
                                        <img src="uploads/<?= $data['tumbnail_produk']; ?>"
                                            class="w-20 h-20 rounded-md object-cover">
                                        <div>
                                            <h3 class="font-semibold"><?= $data['nama_produk']; ?></h3>
                                            <p class="text-gray-500 text-sm">Jumlah: <?= $data['qty']; ?></p>
                                            <p class="text-gray-500 text-sm">Ukuran: <?= $data['size']; ?></p>
                                        </div>
                                    </div>

                                    <!-- BAGIAN HARGA + QTY + HAPUS -->
                                    <div
                                        class="flex flex-row md:flex-col items-center justify-between w-full md:w-auto gap-2 md:gap-4 mt-2 md:mt-0 px-2 md:px-0">
                                        <div
                                            class="flex items-center justify-between w-full md:w-auto gap-2 md:flex-col md:items-end">
                                            <p class="font-semibold text-green-600 whitespace-nowrap">Rp
                                                <?= number_format($data['harga'] * $data['qty'], 0, ',', '.'); ?>
                                            </p>
                                            <form method="POST" class="flex items-center gap-2 w-full md:w-auto">
                                                <input type="hidden" name="id_cart" value="<?= $data['id_cart']; ?>">
                                                <div class="flex items-center border rounded-full overflow-hidden">
                                                    <button type="submit" name="kurangi"
                                                        class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center hover:bg-gray-100">-</button>
                                                    <input type="number" name="qty" value="<?= $data['qty']; ?>" min="1"
                                                        class="w-8 md:w-10 text-center focus:outline-none bg-transparent qty-input"
                                                        data-id="<?= $data['id_cart']; ?>">
                                                    <button type="submit" name="tambah"
                                                        class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center hover:bg-gray-100">+</button>
                                                </div>
                                                <button type="submit" name="hapus"
                                                    class="px-2 py-1 md:px-4 md:py-2 cursor-pointer">
                                                    <img src="resource/bin.png" class="w-4 h-4 md:w-6 md:h-6">
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                    <?php endif; ?>
                </div>

                <!-- KANAN: SUMMARY -->
                <div class="w-full lg:w-1/3 bg-white rounded-md shadow p-6 h-fit">
                    <h2 class="text-xl font-semibold mb-4">Ringkasan Belanja</h2>
                    <div class="flex justify-between text-gray-700 mb-2"><span>Total</span><span id="total-price"
                            class="font-semibold">Rp 0</span></div>
                    <button id="buy-now-btn"
                        class="mt-4 w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600">Beli
                        Sekarang (0)</button>

                    <!-- MODAL CHECKOUT -->
                    <div id="checkoutModal"
                        class="fixed inset-0 flex items-center justify-center z-50 hidden backdrop-blur-sm bg-white/30">
                        <div class="bg-white rounded-lg w-11/12 max-w-md p-6 relative shadow-lg">
                            <button id="closeModal"
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl"><i
                                    class="fa-solid fa-xmark"></i></button>
                            <h2 class="text-xl font-semibold mb-4 text-center">Checkout</h2>
                            <form id="checkoutForm" method="POST" class="space-y-4">
                                <input type="hidden" name="checkout" value="1">
                                <div class="mb-4 border-b pb-2">
                                    <p class="font-medium">Ringkasan Belanja:</p>
                                    <ul id="modalCartSummary" class="text-gray-700 text-sm"></ul>
                                    <p class="mt-2 font-semibold">Total: <span id="modalTotal">Rp 0</span></p>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Nama</label>
                                    <input type="text" readonly class="w-full border rounded px-3 py-2 bg-gray-100"
                                        value="<?= htmlspecialchars($userData['name']); ?>">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Alamat</label>
                                    <select name="alamatSelect" id="alamatSelect"
                                        class="w-full border rounded px-3 py-2">
                                        <?php if (!empty($userData['alamat'])): ?>
                                            <option value="<?= htmlspecialchars($userData['alamat']); ?>">
                                                <?= htmlspecialchars($userData['alamat']); ?>
                                            </option><?php endif; ?>
                                        <?php while ($row = mysqli_fetch_assoc($alamatQuery)): ?>
                                            <option value="<?= htmlspecialchars($row['alamat']); ?>">
                                                <?= htmlspecialchars($row['alamat']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                        <option value="new">+ Tambah alamat baru</option>
                                    </select>
                                    <textarea name="newAlamat" id="newAlamat"
                                        class="w-full border rounded px-3 py-2 mt-2 hidden"
                                        placeholder="Masukkan alamat baru"></textarea>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-1">Nomor Telepon</label>
                                    <select name="telpSelect" id="telpSelect" class="w-full border rounded px-3 py-2">
                                        <?php if (!empty($userData['no_telpon'])): ?>
                                            <option value="<?= htmlspecialchars($userData['no_telpon']); ?>">
                                                <?= htmlspecialchars($userData['no_telpon']); ?>
                                            </option><?php endif; ?>
                                        <?php while ($row = mysqli_fetch_assoc($telpQuery)): ?>
                                            <option value="<?= htmlspecialchars($row['no_telpon']); ?>">
                                                <?= htmlspecialchars($row['no_telpon']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                        <option value="new">+ Tambah nomor baru</option>
                                    </select>
                                    <input type="text" name="newTelp" id="newTelp"
                                        class="w-full border rounded px-3 py-2 mt-2 hidden"
                                        placeholder="Masukkan nomor baru">
                                </div>
                                <button type="submit"
                                    class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600">Checkout
                                    Sekarang</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

        </section>


    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ==== HAMBURGER MENU & SEARCH ====
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            if (hamburgerBtn && mobileMenu) {
                hamburgerBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const mobileSearchBtn = document.getElementById('mobileSearchBtn');
            const mobileSearchBox = document.getElementById('mobileSearchBox');
            if (mobileSearchBtn && mobileSearchBox) {
                mobileSearchBtn.addEventListener('click', () => {
                    mobileSearchBox.classList.toggle('hidden');
                });
            }

            // ==== CART & MODAL CHECKOUT ====
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.cart-checkbox');
            const qtyInputs = document.querySelectorAll('.qty-input');
            const totalPrice = document.getElementById('total-price');
            const buyBtn = document.getElementById('buy-now-btn');
            const checkoutModal = document.getElementById('checkoutModal');
            const closeModal = document.getElementById('closeModal');
            const modalCartSummary = document.getElementById('modalCartSummary');
            const modalTotal = document.getElementById('modalTotal');
            const alamatSelect = document.getElementById('alamatSelect');
            const newAlamat = document.getElementById('newAlamat');
            const telpSelect = document.getElementById('telpSelect');
            const newTelp = document.getElementById('newTelp');

            function updateCart() {
                let total = 0,
                    count = 0;
                checkboxes.forEach(cb => {
                    const qty = parseInt(cb.dataset.qty);
                    const price = parseInt(cb.dataset.price);
                    if (cb.checked) {
                        total += qty * price;
                        count += qty;
                    }
                });

                totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
                buyBtn.textContent = `Checkout Sekarang (${count})`;

                // Jika count 0, disable tombol
                if (count === 0) {
                    buyBtn.disabled = true;
                    buyBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    buyBtn.disabled = false;
                    buyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }

                if (selectAll) selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateCart));

            // ==== EVENT UNTUK PILIH SEMUA ====
            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateCart();
                });
            }

            qtyInputs.forEach(input => {
                input.addEventListener('change', () => {
                    let id = input.dataset.id;
                    let qty = Math.max(1, parseInt(input.value));
                    input.value = qty;
                    const cb = document.querySelector(`.cart-checkbox[data-id='${id}']`);
                    cb.dataset.qty = qty;
                    updateCart();

                    fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `update_qty_ajax=1&id_cart=${id}&qty=${qty}`
                    });
                });
            });

            // checkout modal
            buyBtn.addEventListener('click', () => {
                modalCartSummary.innerHTML = '';
                let total = 0;

                // bersihkan hidden input lama
                document.querySelectorAll('#checkoutForm input[name="selected_cart[]"]').forEach(e => e.remove());

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        const row = cb.closest('.cart-item');
                        const name = row.querySelector('h3').textContent;
                        const qty = parseInt(cb.dataset.qty);
                        const price = parseInt(cb.dataset.price);
                        const id_cart = row.querySelector('input[name="id_cart"]').value;

                        total += qty * price;
                        modalCartSummary.innerHTML += `<li>${name} x ${qty} = Rp ${(qty * price).toLocaleString('id-ID')}</li>`;

                        // tambahkan hidden input ke form checkout
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'selected_cart[]';
                        hidden.value = id_cart;
                        document.getElementById('checkoutForm').appendChild(hidden);
                    }
                });

                modalTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
                checkoutModal.classList.remove('hidden');
            });

            closeModal.addEventListener('click', () => checkoutModal.classList.add('hidden'));

            // alamat & telp baru
            if (alamatSelect) {
                alamatSelect.addEventListener('change', () => {
                    newAlamat.style.display = (alamatSelect.value === 'new') ? 'block' : 'none';
                });
            }
            if (telpSelect) {
                telpSelect.addEventListener('change', () => {
                    newTelp.style.display = (telpSelect.value === 'new') ? 'block' : 'none';
                });
            }

            updateCart();
        });
    </script>



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