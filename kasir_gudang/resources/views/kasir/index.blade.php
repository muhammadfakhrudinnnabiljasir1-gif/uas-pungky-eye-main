@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Bagian Kiri: Katalog Produk (2/3 Lebar pada Layar Besar) -->
    <div class="lg:col-span-7 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">🛍️ Pilih Produk</h2>
        
        <!-- Input Pencarian -->
        <div class="relative mb-6">
            <input type="text" id="cariProduk" placeholder="Cari nama atau kode barang..." class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 pl-10 focus:outline-none focus:border-blue-500 transition">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        
        <!-- Daftar Katalog -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 h-[500px] overflow-y-auto pr-2" id="daftarProduk">
            @foreach($produk as $item)
            <div class="border-2 border-gray-100 rounded-xl p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition duration-200 produk-item flex flex-col justify-between" onclick="tambahKeKeranjang({{ $item->id }}, '{{ $item->nama_produk }}', {{ $item->harga }})">
                <div>
                    <div class="font-bold text-gray-800 leading-tight mb-1">{{ $item->nama_produk }}</div>
                    <div class="text-xs text-gray-400 mb-2">{{ $item->kode_barang }}</div>
                </div>
                <div>
                    <div class="text-blue-600 font-bold mb-1">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
                    <div class="text-xs text-white bg-green-500 inline-block px-2 py-0.5 rounded-full">Stok: {{ $item->stok }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Bagian Kanan: Keranjang dan Pembayaran (1/3 Lebar pada Layar Besar) -->
    <div class="lg:col-span-5 bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex flex-col h-[600px]">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">🧾 Keranjang Belanja</h2>
        
        <form action="{{ route('kasir.store') }}" method="POST" id="formTransaksi" class="flex-grow flex flex-col">
            @csrf
            
            <!-- Isi Keranjang -->
            <div class="flex-grow overflow-y-auto border border-gray-200 rounded-lg mb-4 p-2 bg-gray-50">
                <table class="w-full text-left">
                    <thead class="sticky top-0 bg-gray-50">
                        <tr class="text-xs text-gray-500 uppercase border-b">
                            <th class="py-2 px-1 w-1/2">Produk</th>
                            <th class="py-2 px-1 text-center w-1/4">Jml</th>
                            <th class="py-2 px-1 text-right w-1/4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="isiKeranjang" class="text-sm">
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-400">Keranjang kosong.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Harga -->
            <div class="mb-4 bg-gray-100 p-4 rounded-lg">
                <div class="flex justify-between font-bold text-2xl text-gray-800">
                    <span>Total:</span>
                    <span id="labelTotalHarga">Rp 0</span>
                </div>
            </div>

            <!-- Input Pembayaran -->
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-bold mb-2">Uang Tunai (Rp)</label>
                <input type="number" name="uang_bayar" id="uangBayar" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-green-500 font-bold text-xl text-gray-800 text-right" required min="0" onkeyup="hitungKembalian()" placeholder="0">
            </div>
            
            <!-- Kembalian -->
            <div class="mb-6 flex justify-between font-bold text-xl text-gray-700">
                <span>Kembali:</span> 
                <span id="labelKembalian" class="text-gray-400">Rp 0</span>
            </div>

            <!-- Hidden Inputs for form submission -->
            <div id="keranjangInputs"></div>

            <button type="submit" id="btnProses" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-4 rounded-lg text-xl transition shadow-lg flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Proses Pembayaran
            </button>
        </form>
    </div>
</div>

<script>
    let keranjang = [];

    // Format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    // Menambah produk ke keranjang
    function tambahKeKeranjang(id, nama, harga) {
        let itemIndex = keranjang.findIndex(item => item.id_produk === id);
        
        if (itemIndex > -1) {
            keranjang[itemIndex].jumlah += 1;
        } else {
            keranjang.push({ id_produk: id, nama: nama, harga: harga, jumlah: 1 });
        }
        renderKeranjang();
    }

    // Mengubah jumlah
    function ubahJumlah(index, aksi) {
        if (aksi === 'tambah') {
            keranjang[index].jumlah += 1;
        } else if (aksi === 'kurang') {
            keranjang[index].jumlah -= 1;
            if (keranjang[index].jumlah <= 0) {
                keranjang.splice(index, 1);
            }
        }
        renderKeranjang();
    }

    // Render tampilan keranjang
    function renderKeranjang() {
        let tbody = document.getElementById('isiKeranjang');
        let inputsContainer = document.getElementById('keranjangInputs');
        tbody.innerHTML = '';
        inputsContainer.innerHTML = '';
        
        let totalHarga = 0;

        if (keranjang.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-gray-400">Keranjang kosong.</td></tr>`;
            document.getElementById('labelTotalHarga').innerText = 'Rp 0';
            hitungKembalian(0);
            return;
        }

        keranjang.forEach((item, index) => {
            let subtotal = item.harga * item.jumlah;
            totalHarga += subtotal;

            let tr = document.createElement('tr');
            tr.className = "border-b border-gray-200 last:border-0";
            tr.innerHTML = `
                <td class="py-3 px-1 font-semibold text-gray-700">${item.nama}</td>
                <td class="py-3 px-1 text-center whitespace-nowrap">
                    <button type="button" class="px-2 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200 transition" onclick="ubahJumlah(${index}, 'kurang')">-</button>
                    <span class="px-2 font-bold">${item.jumlah}</span>
                    <button type="button" class="px-2 py-1 bg-green-100 text-green-600 rounded hover:bg-green-200 transition" onclick="ubahJumlah(${index}, 'tambah')">+</button>
                </td>
                <td class="py-3 px-1 text-right font-semibold text-gray-800">Rp ${formatRupiah(subtotal)}</td>
            `;
            tbody.appendChild(tr);

            inputsContainer.innerHTML += `
                <input type="hidden" name="keranjang[${index}][id_produk]" value="${item.id_produk}">
                <input type="hidden" name="keranjang[${index}][jumlah]" value="${item.jumlah}">
            `;
        });

        document.getElementById('labelTotalHarga').innerText = 'Rp ' + formatRupiah(totalHarga);
        hitungKembalian(totalHarga);
    }

    // Hitung kembalian
    function hitungKembalian(total = null) {
        if (total === null) {
            total = keranjang.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        }
        
        let bayar = parseInt(document.getElementById('uangBayar').value) || 0;
        let kembalian = bayar - total;
        let label = document.getElementById('labelKembalian');
        let btnProses = document.getElementById('btnProses');
        
        if (keranjang.length === 0) {
            label.innerText = 'Rp 0';
            label.className = "text-gray-400";
            btnProses.disabled = true;
            return;
        }

        if (kembalian < 0) {
            label.innerText = 'Uang Kurang!';
            label.className = "text-red-600";
            btnProses.disabled = true;
        } else {
            label.innerText = 'Rp ' + formatRupiah(kembalian);
            label.className = "text-green-600";
            btnProses.disabled = false;
        }
    }

    // Live Search
    document.getElementById('cariProduk').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let items = document.querySelectorAll('.produk-item');
        
        items.forEach(item => {
            let text = item.innerText.toLowerCase();
            if (text.includes(value)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Inisialisasi awal
    hitungKembalian(0);
</script>
@endsection
