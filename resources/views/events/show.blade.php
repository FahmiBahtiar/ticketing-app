<x-layouts.app>
    <section class="max-w-7xl mx-auto py-12 px-6">
        <nav class="mb-6">
            <div class="breadcrumbs">
                <ul>
                    <li><a href="{{ route('home') }}" class="link link-neutral">Beranda</a></li>
                    <li><a href="#" class="link link-neutral">Event</a></li>
                    <li>{{ $event->judul }}</li>
                </ul>
            </div>
        </nav>

        <!-- Alert Sold Out Event -->
        @if($event->isSoldOut())
        <div class="alert alert-error shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold">Event SOLD OUT!</h3>
                <div class="text-sm">Maaf, semua tiket untuk event ini sudah habis terjual.</div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left / Main area -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow">
                    <figure>
                        <img src="{{ $event->gambar
    ? asset('images/events/' . $event->gambar)
    : 'https://img.daisyui.com/images/stock/photo-1606107557195-0e29a4b5b4aa.webp'
  }}" alt="{{ $event->judul }}" class="w-full h-96 object-cover" />
                    </figure>
                    <div class="card-body">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h1 class="text-3xl font-extrabold">{{ $event->judul }}</h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($event->tanggal_waktu)->locale('id')->translatedFormat('d F
                                    Y, H:i') }}
                                    • 📍
                                    {{ $event->lokasi }}
                                </p>

                                <div class="mt-3 flex gap-2 items-center">
                                    <span class="badge badge-primary">{{ $event->kategori?->nama ?? 'Tanpa Kategori'
                                        }}</span>
                                    <span class="badge">{{ $event->user?->name ?? 'Penyelenggara' }}</span>
                                </div>
                            </div>

                        </div>

                        <p class="mt-4 text-gray-700 leading-relaxed">{{ $event->deskripsi }}</p>

                        <div class="divider"></div>

                        <h3 class="text-xl font-bold">Pilih Tiket</h3>

                        <div class="mt-4 space-y-4">
                            @forelse($event->tikets as $tiket)
                            <div class="card card-side shadow-sm p-4 items-center 
                                {{ $tiket->stok == 0 ? 'opacity-60 bg-gray-50' : '' }}
                                {{ $tiket->stok > 0 && $tiket->stok < 5 ? 'border-2 border-orange-400' : '' }}">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold">{{ $tiket->tipe }}</h4>
                                        @if($tiket->stok == 0)
                                            <span class="badge badge-error text-white">SOLD OUT</span>
                                        @elseif($tiket->stok > 0 && $tiket->stok < 5)
                                            <span class="badge badge-warning text-white">⚡ Hampir Habis</span>
                                        @endif
                                    </div>
                                    <p class="text-sm {{ $tiket->stok > 0 && $tiket->stok < 5 ? 'text-orange-600 font-semibold' : 'text-gray-500' }}">
                                        Stok: <span id="stock-{{ $tiket->id }}">{{ $tiket->stok }}</span>
                                        @if($tiket->stok > 0 && $tiket->stok < 5)
                                            <span class="animate-pulse">🔥</span>
                                        @endif
                                    </p>
                                    <p class="text-sm mt-2">{{ $tiket->keterangan ?? '' }}</p>
                                </div>

                                <div class="w-44 text-right">
                                    <div class="text-lg font-bold">
                                        {{ $tiket->harga ? 'Rp ' . number_format($tiket->harga, 0, ',', '.') : 'Gratis'
                                        }}
                                    </div>

                                    @if($tiket->stok > 0)
                                    <div class="mt-3 flex items-center justify-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline" data-action="dec"
                                            data-id="{{ $tiket->id }}" aria-label="Kurangi satu">−</button>
                                        <input id="qty-{{ $tiket->id }}" type="number" min="0" max="{{ $tiket->stok }}"
                                            value="0" class="input input-bordered w-16 text-center"
                                            data-id="{{ $tiket->id }}" />
                                        <button type="button" class="btn btn-sm btn-outline" data-action="inc"
                                            data-id="{{ $tiket->id }}" aria-label="Tambah satu">+</button>
                                    </div>

                                    <div class="text-sm text-gray-500 mt-2">Subtotal: <span
                                            id="subtotal-{{ $tiket->id }}">Rp 0</span>
                                    </div>
                                    @else
                                    <div class="mt-3 text-sm text-error font-semibold">
                                        Tiket Habis
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="alert alert-info">Tiket belum tersedia untuk acara ini.</div>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right / Summary -->
            <aside class="lg:col-span-1">
                <div class="card sticky top-24 p-4 bg-base-100 shadow">
                    <h4 class="font-bold text-lg">Ringkasan Pembelian</h4>

                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-500"><span>Item</span><span
                                id="summaryItems">0</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold mt-1"><span>Total</span><span
                                id="summaryTotal">Rp
                                0</span></div>
                    </div>

                    <div class="divider"></div>

                    <div id="selectedList" class="space-y-2 text-sm text-gray-700">
                        <p class="text-gray-500">Belum ada tiket dipilih</p>
                    </div>

                    @auth
                    <button id="checkoutButton" 
                        class="btn btn-primary !bg-blue-900 text-white btn-block mt-6 disabled:opacity-50 disabled:cursor-not-allowed"
                        onclick="openCheckout()" 
                        title="Pilih tiket terlebih dahulu">
                        Checkout
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block mt-6 text-white">Login untuk
                        Checkout</a>
                    @endauth

                </div>
            </aside>
        </div>

        <!-- Checkout Modal -->
        <dialog id="checkout_modal" class="modal">
            <form method="dialog" class="modal-box">
                <h3 class="font-bold text-lg">Konfirmasi Pembelian</h3>
                <div class="mt-4 space-y-2 text-sm">
                    <div id="modalItems">
                        <p class="text-gray-500">Belum ada item.</p>
                    </div>

                    <div class="divider"></div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">Total</span>
                        <span class="font-bold text-lg" id="modalTotal">Rp 0</span>
                    </div>
                </div>

                <div class="modal-action">
                    <button class="btn">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 !bg-blue-900 text-white"
                        id="confirmCheckout">Konfirmasi</button>
                </div>
            </form>
        </dialog>

    </section>

    <script>
        (function () {
            // Helper to format Indonesian currency
            const formatRupiah = (value) => {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            }

            const tickets = {
                @foreach($event->tikets as $tiket)
                {{ $tiket->id }}: {
                    id: {{ $tiket->id }},
                    price: {{ $tiket->harga ?? 0 }},
                    stock: {{ $tiket->stok }},
                    tipe: "{{ e($tiket->tipe) }}"
                },
                @endforeach
            };

        const summaryItemsEl = document.getElementById('summaryItems');
        const summaryTotalEl = document.getElementById('summaryTotal');
        const selectedListEl = document.getElementById('selectedList');
        const checkoutButton = document.getElementById('checkoutButton');

        function updateSummary() {
            let totalQty = 0;
            let totalPrice = 0;
            let selectedHtml = '';

            Object.values(tickets).forEach(t => {
                const qtyInput = document.getElementById('qty-' + t.id);
                if (!qtyInput) return;
                const qty = Number(qtyInput.value || 0);
                if (qty > 0) {
                    totalQty += qty;
                    totalPrice += qty * t.price;
                    selectedHtml += `<div class="flex justify-between"><span>${t.tipe} x ${qty}</span><span>${formatRupiah(qty * t.price)}</span></div>`;
                }
            });

            summaryItemsEl.textContent = totalQty;
            summaryTotalEl.textContent = formatRupiah(totalPrice);
            selectedListEl.innerHTML = selectedHtml || '<p class="text-gray-500">Belum ada tiket dipilih</p>';
            
            // Update button state and tooltip
            const isDisabled = totalQty === 0;
            checkoutButton.disabled = isDisabled;
            checkoutButton.title = isDisabled 
                ? 'Pilih tiket terlebih dahulu' 
                : `Checkout ${totalQty} tiket (${formatRupiah(totalPrice)})`;
            
            console.log('Summary updated:', { totalQty, totalPrice, isDisabled });
        }

        // Wire up plus/minus buttons and manual input
        document.querySelectorAll('[data-action="inc"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const input = document.getElementById('qty-' + id)
                const info = tickets[id];
                if (!input || !info) return;
                let val = Number(input.value || 0);
                if (val < info.stock) val++;
                input.value = val;
                updateTicketSubtotal(id);
                updateSummary();
            });
        });

        document.querySelectorAll('[data-action="dec"]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                const input = document.getElementById('qty-' + id);
                if (!input) return;
                let val = Number(input.value || 0);
                if (val > 0) val--;
                input.value = val;
                updateTicketSubtotal(id);
                updateSummary();
            });
        });

        document.querySelectorAll('input[id^="qty-"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const el = e.currentTarget;
                const id = el.dataset.id;
                const info = tickets[id];
                let val = Number(el.value || 0);
                if (val < 0) val = 0;
                if (val > info.stock) val = info.stock;
                el.value = val;
                updateTicketSubtotal(id);
                updateSummary();
            });
        });

        function updateTicketSubtotal(id) {
            const t = tickets[id];
            const qtyInput = document.getElementById('qty-' + id);
            // Skip if input doesn't exist (sold out tickets)
            if (!qtyInput) return;
            
            const qty = Number(qtyInput.value || 0);
            const subtotalEl = document.getElementById('subtotal-' + id);
            if (subtotalEl) subtotalEl.textContent = formatRupiah(qty * t.price);
        }

        // Checkout modal
        window.openCheckout = function () {            
            const modal = document.getElementById('checkout_modal');
            // populate modal items
            const modalItems = document.getElementById('modalItems');
            const modalTotal = document.getElementById('modalTotal');

            let itemsHtml = '';
            let total = 0;
            let selectedCount = 0;
            
            Object.values(tickets).forEach(t => {
                const qtyInput = document.getElementById('qty-' + t.id);
                // Skip if input doesn't exist (sold out tickets)
                if (!qtyInput) return;
                
                const qty = Number(qtyInput.value || 0);
                if (qty > 0) {
                    selectedCount++;
                    itemsHtml += `<div class="flex justify-between"><span>${t.tipe} x ${qty}</span><span>${formatRupiah(qty * t.price)}</span></div>`;
                    total += qty * t.price;
                }
            });


            if (selectedCount === 0) {
                alert('Silakan pilih tiket terlebih dahulu!');
                return;
            }

            modalItems.innerHTML = itemsHtml || '<p class="text-gray-500">Belum ada item.</p>';
            modalTotal.textContent = formatRupiah(total);

            if (typeof modal.showModal === 'function') {
                modal.showModal();
            } else {
                // fallback for older browsers
                modal.classList.add('modal-open');
            }
        }

        //confirmCheckout
        document.getElementById('confirmCheckout').addEventListener('click', async () => {
            const btn = document.getElementById('confirmCheckout');
            btn.setAttribute('disabled', 'disabled');
            btn.textContent = 'Memproses...';

            // gather items
            const items = [];
            Object.values(tickets).forEach(t => {
                const qtyInput = document.getElementById('qty-' + t.id);
                // Skip if input doesn't exist (sold out tickets)
                if (!qtyInput) return;
                
                const qty = Number(qtyInput.value || 0);
                if (qty > 0) items.push({ tiket_id: t.id, jumlah: qty });
            });

            if (items.length === 0) {
                alert('Tidak ada tiket dipilih');
                btn.removeAttribute('disabled');
                btn.textContent = 'Konfirmasi (placeholder)';
                return;
            }

            try {
                const res = await fetch("{{ route('orders.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ event_id: {{ $event->id }}, items })
                });

                const data = await res.json();
                
                if (!res.ok) {
                    // Tampilkan pesan error dari server (termasuk sold out)
                    alert(data.message || 'Gagal membuat pesanan');
                    btn.removeAttribute('disabled');
                    btn.textContent = 'Konfirmasi';
                    return;
                }

                // redirect to orders list
                window.location.href = data.redirect || '{{ route('orders.index') }}';
            } catch (err) {
                console.log(err);
                alert('Terjadi kesalahan saat memproses pesanan: ' + err.message);
                btn.removeAttribute('disabled');
                btn.textContent = 'Konfirmasi';
            }
        });

        // init
        updateSummary();
    }) ();
    </script>
</x-layouts.app>