document.addEventListener("DOMContentLoaded", () => {
    // =========================
    // HAMBURGER MENU & SEARCH
    // =========================
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    hamburgerBtn?.addEventListener('click', () => mobileMenu?.classList.toggle('hidden'));

    const searchBtn = document.getElementById('mobileSearchBtn');
    const searchBox = document.getElementById('mobileSearchBox');
    searchBtn?.addEventListener('click', () => searchBox?.classList.toggle('hidden'));

    // =========================
    // CAROUSEL
    // =========================
    const carousel = document.getElementById("carousel");
    const dots = document.querySelectorAll(".dot");
    if (carousel && dots.length) {
        const slides = Array.from(carousel.children);
        let index = 1;
        const totalSlides = slides.length;

        const firstClone = slides[0].cloneNode(true);
        const lastClone = slides[totalSlides - 1].cloneNode(true);
        carousel.appendChild(firstClone);
        carousel.insertBefore(lastClone, carousel.firstChild);
        carousel.style.transform = `translateX(-${index * 100}%)`;

        function updateDots(i) {
            dots.forEach((dot, idx) => {
                dot.classList.toggle("bg-gray-800", idx === i - 1);
                dot.classList.toggle("bg-gray-300", idx !== i - 1);
            });
        }

        function moveToSlide(i) {
            carousel.style.transition = "transform 0.5s ease-in-out";
            index = i;
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }

        let autoSlide = setInterval(() => moveToSlide(index + 1), 3000);

        carousel.addEventListener("transitionend", () => {
            if (index === 0) index = totalSlides;
            else if (index === totalSlides + 1) index = 1;
            carousel.style.transition = "none";
            carousel.style.transform = `translateX(-${index * 100}%)`;
            updateDots(index);
        });

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                moveToSlide(idx + 1);
                clearInterval(autoSlide);
                autoSlide = setInterval(() => moveToSlide(index + 1), 3000);
            });
        });

        updateDots(index);
    }

    // =========================
    // PRODUCT POPUP
    // =========================
    const popup = document.getElementById('productPopup');
    const closeBtn = document.getElementById('closePopup');
    const warn = document.getElementById('popupWarn');
    const submitBtn = document.getElementById('addToCartPopup');

    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id || '';
            const name = btn.dataset.name || 'Produk';
            const price = btn.dataset.price || '0';
            const image = btn.dataset.image || '';

            document.getElementById('popupId').value = id;
            document.getElementById('popupTitle').innerText = name;
            document.getElementById('popupPrice').innerText = 'Rp ' + (parseInt(price) || 0).toLocaleString('id-ID');
            document.getElementById('popupImage').src = image;

            if (!id) {
                warn.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warn.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            popup.classList.remove('translate-x-full');
        });
    });

    closeBtn?.addEventListener('click', () => popup.classList.add('translate-x-full'));

    // =========================
    // CUSTOM ALERT
    // =========================
    window.showCustomAlert = function (message) {
        const container = document.getElementById("custom-alert-container");
        if (!container) return;

        const alertBox = document.createElement("div");
        alertBox.className = "bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 animate-fade-in-down";
        alertBox.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${message}</span>`;

        container.appendChild(alertBox);

        setTimeout(() => {
            alertBox.classList.add("opacity-0", "translate-y-2", "transition", "duration-500");
            setTimeout(() => alertBox.remove(), 500);
        }, 2000);
    };
});
