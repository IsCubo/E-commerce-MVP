/**
 * Client-side shopping cart.
 *
 * There is no user account or server-side session for shoppers on this
 * storefront (only the admin panel has authentication), and checkout means
 * sending one WhatsApp message with the order — not a real payment. So the
 * cart lives entirely in the browser (localStorage) and "checkout" just
 * builds and opens a wa.me link with every item in it.
 *
 * Product cards / the detail page add items via a delegated click on any
 * [data-cart-add] element carrying data-id/name/price/image attributes.
 * The drawer markup (#cart-drawer) and window.APP_CONFIG (whatsapp number +
 * welcome message) are rendered once in layouts/app.blade.php.
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'ecommerce_cart_v1';

    function readCart() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            var items = raw ? JSON.parse(raw) : [];
            return Array.isArray(items) ? items : [];
        } catch (e) {
            return [];
        }
    }

    function writeCart(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        renderCart();
    }

    function formatMoney(amount) {
        var value = Number(amount) || 0;
        return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function getTotal(items) {
        return items.reduce(function (sum, item) {
            return sum + (Number(item.price) || 0) * item.quantity;
        }, 0);
    }

    function getCount(items) {
        return items.reduce(function (sum, item) { return sum + item.quantity; }, 0);
    }

    function addItem(product) {
        var items = readCart();
        var existing = items.filter(function (i) { return i.id === product.id; })[0];
        if (existing) {
            existing.quantity += 1;
        } else {
            product.quantity = 1;
            items.push(product);
        }
        writeCart(items);
        openDrawer();
    }

    function removeItem(id) {
        writeCart(readCart().filter(function (i) { return i.id !== id; }));
    }

    function setQuantity(id, quantity) {
        var items = readCart();
        if (quantity <= 0) {
            writeCart(items.filter(function (i) { return i.id !== id; }));
            return;
        }
        var item = items.filter(function (i) { return i.id === id; })[0];
        if (!item) return;
        item.quantity = quantity;
        writeCart(items);
    }

    function renderItemRow(item) {
        var img = item.image
            ? '<img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.name) + '" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">'
            : '<div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0"></div>';

        return (
            '<li class="flex gap-3 py-4 border-b border-gray-100 dark:border-gray-700">' +
                img +
                '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm font-semibold text-gray-900 dark:text-white truncate">' + escapeHtml(item.name) + '</p>' +
                    '<p class="text-xs text-gray-500 dark:text-gray-400">' + formatMoney(item.price) + ' c/u</p>' +
                    '<div class="flex items-center gap-2 mt-2">' +
                        '<button type="button" class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" data-cart-decrement="' + escapeHtml(item.id) + '">&minus;</button>' +
                        '<span class="text-sm w-6 text-center text-gray-900 dark:text-white">' + item.quantity + '</span>' +
                        '<button type="button" class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" data-cart-increment="' + escapeHtml(item.id) + '">+</button>' +
                        '<button type="button" class="ml-auto text-xs text-red-500 hover:text-red-700" data-cart-remove="' + escapeHtml(item.id) + '">Eliminar</button>' +
                    '</div>' +
                '</div>' +
                '<p class="text-sm font-bold text-gray-900 dark:text-white whitespace-nowrap">' + formatMoney(item.price * item.quantity) + '</p>' +
            '</li>'
        );
    }

    function renderCart() {
        var items = readCart();
        var count = getCount(items);

        var countEls = document.querySelectorAll('[data-cart-count]');
        for (var i = 0; i < countEls.length; i++) {
            countEls[i].textContent = String(count);
            countEls[i].classList.toggle('hidden', count === 0);
        }

        var listEl = document.querySelector('[data-cart-list]');
        var emptyEl = document.querySelector('[data-cart-empty]');
        var totalEl = document.querySelector('[data-cart-total]');
        var checkoutBtn = document.querySelector('[data-cart-checkout]');

        if (!listEl) return; // drawer markup not present on this page

        if (items.length === 0) {
            listEl.innerHTML = '';
            if (emptyEl) emptyEl.classList.remove('hidden');
            if (checkoutBtn) checkoutBtn.setAttribute('disabled', 'disabled');
        } else {
            if (emptyEl) emptyEl.classList.add('hidden');
            if (checkoutBtn) checkoutBtn.removeAttribute('disabled');
            listEl.innerHTML = items.map(renderItemRow).join('');
        }

        if (totalEl) totalEl.textContent = formatMoney(getTotal(items));
    }

    function openDrawer() {
        var drawer = document.getElementById('cart-drawer');
        var backdrop = document.getElementById('cart-backdrop');
        if (!drawer) return;
        drawer.classList.remove('translate-x-full');
        if (backdrop) backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDrawer() {
        var drawer = document.getElementById('cart-drawer');
        var backdrop = document.getElementById('cart-backdrop');
        if (!drawer) return;
        drawer.classList.add('translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function buildWhatsappMessage(items) {
        var lines = items.map(function (item) {
            return item.quantity + 'x ' + item.name + ' - ' + formatMoney(item.price * item.quantity);
        });
        // Intentionally not reusing the single-product "welcome_message" setting
        // here: that phrase is written to lead into one product's name inline
        // and reads oddly in front of an itemized multi-product list.
        return 'Hola, quiero hacer el siguiente pedido:' + '\n\n' + lines.join('\n') + '\n\nTotal: ' + formatMoney(getTotal(items));
    }

    function checkout() {
        var items = readCart();
        if (items.length === 0) return;

        var config = window.APP_CONFIG || {};
        if (!config.whatsappNumber) {
            alert('El número de WhatsApp de la tienda no está configurado.');
            return;
        }

        var message = buildWhatsappMessage(items);
        var url = 'https://wa.me/' + config.whatsappNumber + '?text=' + encodeURIComponent(message);
        window.open(url, '_blank');
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderCart();

        document.addEventListener('click', function (e) {
            var target = e.target;

            var addBtn = target.closest('[data-cart-add]');
            if (addBtn) {
                e.preventDefault();
                addItem({
                    id: addBtn.getAttribute('data-id'),
                    name: addBtn.getAttribute('data-name'),
                    price: parseFloat(addBtn.getAttribute('data-price')) || 0,
                    image: addBtn.getAttribute('data-image') || ''
                });
                return;
            }

            if (target.closest('[data-cart-open]')) {
                e.preventDefault();
                openDrawer();
                return;
            }

            if (target.closest('[data-cart-close]') || target.id === 'cart-backdrop') {
                e.preventDefault();
                closeDrawer();
                return;
            }

            var incBtn = target.closest('[data-cart-increment]');
            if (incBtn) {
                var incId = incBtn.getAttribute('data-cart-increment');
                var incItem = readCart().filter(function (i) { return i.id === incId; })[0];
                if (incItem) setQuantity(incId, incItem.quantity + 1);
                return;
            }

            var decBtn = target.closest('[data-cart-decrement]');
            if (decBtn) {
                var decId = decBtn.getAttribute('data-cart-decrement');
                var decItem = readCart().filter(function (i) { return i.id === decId; })[0];
                if (decItem) setQuantity(decId, decItem.quantity - 1);
                return;
            }

            var removeBtn = target.closest('[data-cart-remove]');
            if (removeBtn) {
                removeItem(removeBtn.getAttribute('data-cart-remove'));
                return;
            }

            if (target.closest('[data-cart-checkout]')) {
                checkout();
                return;
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeDrawer();
        });
    });
})();
