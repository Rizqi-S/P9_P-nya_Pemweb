<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bubble Chart Penjualan Peralatan Sekolah</title>

    <!-- Font Poppins dan library Chart.js -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --background: #f6f7fb;
            --surface: #ffffff;
            --text: #22243a;
            --muted: #6b7280;
            --border: #e5e7eb;
            --danger: #dc2626;
            --success: #15803d;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 28px 18px 48px;
            color: var(--text);
            background: var(--background);
        }

        .halaman { max-width: 1180px; margin: 0 auto; }
        header { text-align: center; margin: 8px 0 26px; }
        h1 { font-size: clamp(23px, 3vw, 32px); margin: 0 0 8px; }
        header p { margin: 0; color: var(--muted); font-size: 14px; }
        h2 { font-size: 19px; margin: 0 0 18px; }

        .kartu {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 5px 18px rgba(30, 41, 59, .04);
        }

        .container-grafik { position: relative; width: 100%; height: 440px; }
        .catatan { color: var(--muted); font-size: 12px; margin: 12px 0 0; }
        .ringkasan { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 22px; }
        .statistik { background: white; border: 1px solid var(--border); border-radius: 14px; padding: 16px 18px; }
        .statistik .label { color: var(--muted); font-size: 12px; margin-bottom: 5px; }
        .statistik .angka { font-weight: 700; font-size: 21px; overflow-wrap: anywhere; }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: 13px; font-weight: 500; }
        .field input {
            width: 100%; border: 1px solid #d1d5db; border-radius: 9px; padding: 11px 12px;
            font: inherit; font-size: 14px; outline: none; background: white;
        }
        .field input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, .12); }
        .aksi-form { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        button { font-family: inherit; cursor: pointer; border: 0; border-radius: 9px; padding: 10px 15px; font-size: 13px; font-weight: 600; }
        button:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-utama { background: var(--primary); color: white; }
        .btn-utama:hover:not(:disabled) { background: var(--primary-dark); }
        .btn-sekunder { background: #eef2ff; color: var(--primary-dark); }
        .btn-hapus { background: #fee2e2; color: var(--danger); }
        .btn-edit { background: #fef3c7; color: #92400e; }
        .pesan { min-height: 20px; margin: 12px 0 0; font-size: 13px; color: var(--success); }
        .pesan.error { color: var(--danger); }

        .bagian-tabel { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 790px; font-size: 13px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid var(--border); white-space: nowrap; }
        th { background: #f8fafc; color: #4b5563; font-weight: 600; }
        tbody tr:hover { background: #fafaff; }
        .aksi-tabel { display: flex; gap: 7px; }
        .aksi-tabel button { padding: 7px 10px; font-size: 12px; }
        .jumlah-data { font-size: 13px; color: var(--muted); margin: -8px 0 14px; }
        footer { text-align: center; font-size: 12px; color: var(--muted); margin-top: 26px; }

        @media (max-width: 700px) {
            body { padding: 18px 12px 32px; }
            .kartu { padding: 16px; }
            .ringkasan { grid-template-columns: 1fr; gap: 10px; }
            .form-grid { grid-template-columns: 1fr; }
            .container-grafik { height: 360px; }
        }
    </style>
</head>
<body>
<div class="halaman">
    <header>
        <h1>Bubble Chart Penjualan Peralatan Sekolah</h1>
        <p>Kelompok 3 · Visualisasi harga, keuntungan, dan jumlah produk terjual</p>
    </header>

    <section class="ringkasan" aria-label="Ringkasan data">
        <div class="statistik"><div class="label">Total Produk</div><div class="angka" id="totalProduk">0</div></div>
        <div class="statistik"><div class="label">Total Unit Terjual</div><div class="angka" id="totalTerjual">0</div></div>
        <div class="statistik"><div class="label">Total Keuntungan</div><div class="angka" id="totalKeuntungan">Rp0</div></div>
    </section>

    <section class="kartu">
        <h2>Grafik Penjualan Produk</h2>
        <div class="container-grafik"><canvas id="grafikPenjualan"></canvas></div>
        <p class="catatan">Sumbu X = harga produk · Sumbu Y = keuntungan · Ukuran bubble = jumlah unit terjual. Arahkan kursor ke bubble untuk melihat detail produk.</p>
    </section>

    <section class="kartu" id="bagianForm">
        <h2 id="judulForm">Tambah Data Produk</h2>
        <form id="formProduk" method="POST" action="{{ route('products.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodInput" value="POST">
            <div class="form-grid">
                <div class="field">
                    <label for="produk">Nama produk</label>
                    <input id="produk" name="produk" type="text" placeholder="Contoh: Buku Tulis" maxlength="80" value="{{ old('produk') }}" required>
                </div>
                <div class="field">
                    <label for="harga">Harga produk (Rp)</label>
                    <input id="harga" name="harga" type="number" min="0" step="1" placeholder="Contoh: 15000" value="{{ old('harga') }}" required>
                </div>
                <div class="field">
                    <label for="keuntungan">Keuntungan (Rp)</label>
                    <input id="keuntungan" name="keuntungan" type="number" min="0" step="1" placeholder="Contoh: 5000" value="{{ old('keuntungan') }}" required>
                </div>
                <div class="field">
                    <label for="terjual">Jumlah terjual (unit)</label>
                    <input id="terjual" name="terjual" type="number" min="0" step="1" placeholder="Contoh: 120" value="{{ old('terjual') }}" required>
                </div>
            </div>
            <div class="aksi-form">
                <button class="btn-utama" type="submit" id="tombolSimpan">+ Simpan Produk</button>
                <button class="btn-sekunder" type="button" id="tombolBatal" hidden>Batal Edit</button>
            </div>
            <p class="pesan {{ $errors->any() ? 'error' : '' }}" id="pesanForm" role="status" aria-live="polite">
                @if ($errors->any())
                    {{ $errors->first() }}
                @elseif (session('success'))
                    {{ session('success') }}
                @endif
            </p>
        </form>
    </section>

    <section class="kartu">
        <h2>Data Produk</h2>
        <p class="jumlah-data" id="labelJumlah">Menampilkan 0 produk</p>
        <div class="bagian-tabel">
            <table>
                <thead>
                    <tr>
                        <th>No.</th><th>Nama Produk</th><th>Harga</th><th>Keuntungan</th><th>Terjual</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="isiTabel"></tbody>
            </table>
        </div>
        <form id="formReset" method="POST" action="{{ route('products.reset') }}" class="aksi-form">
            @csrf
            <button class="btn-hapus" type="submit" id="tombolReset">Kembalikan 30 Data Awal</button>
        </form>
        <p class="catatan">Data disimpan di database MySQL. Tombol reset akan menghapus perubahan di database dan mengembalikan data contoh awal.</p>
    </section>

    <footer>Bubble Chart Penjualan Peralatan Sekolah · Laravel, Chart.js, dan database MySQL</footer>
</div>

<script>
    // Atur font bawaan grafik menjadi Poppins.
    Chart.defaults.font.family = 'Poppins, sans-serif';

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const rute = {
        store: @json(route('products.store')),
        reset: @json(route('products.reset')),
        base: @json(url('/products'))
    };

    const warnaIsi = [
        'rgba(66, 133, 244, 0.65)', 'rgba(52, 168, 83, 0.65)',
        'rgba(251, 188, 5, 0.70)', 'rgba(234, 67, 53, 0.65)',
        'rgba(171, 71, 188, 0.65)', 'rgba(0, 172, 193, 0.65)',
        'rgba(255, 112, 67, 0.65)', 'rgba(121, 85, 72, 0.65)',
        'rgba(63, 81, 181, 0.65)', 'rgba(0, 150, 136, 0.65)'
    ];
    const warnaGaris = ['#4285F4', '#34A853', '#FBBC05', '#EA4335', '#AB47BC', '#00ACC1', '#FF7043', '#795548', '#3F51B5', '#009688'];

    const elemen = {
        form: document.getElementById('formProduk'),
        methodInput: document.getElementById('methodInput'),
        formReset: document.getElementById('formReset'),
        produk: document.getElementById('produk'),
        harga: document.getElementById('harga'),
        keuntungan: document.getElementById('keuntungan'),
        terjual: document.getElementById('terjual'),
        judulForm: document.getElementById('judulForm'),
        tombolSimpan: document.getElementById('tombolSimpan'),
        tombolBatal: document.getElementById('tombolBatal'),
        tombolReset: document.getElementById('tombolReset'),
        pesan: document.getElementById('pesanForm'),
        isiTabel: document.getElementById('isiTabel'),
        labelJumlah: document.getElementById('labelJumlah')
    };

    let produkList = @json($products);
    let editId = null;

    function formatRupiah(nilai) {
        return 'Rp ' + Number(nilai).toLocaleString('id-ID');
    }

    // Konversi jumlah terjual menjadi radius bubble secara konsisten.
    function hitungRadius(jumlah) {
        const nilai = Math.max(0, Number(jumlah) || 0);
        return Math.min(22, Math.max(5, 5 + Math.sqrt(nilai) * 0.75));
    }

    function dataUntukGrafik() {
        return produkList.map(item => ({
            x: Number(item.harga),
            y: Number(item.keuntungan),
            r: hitungRadius(item.terjual),
            produk: item.produk,
            terjual: Number(item.terjual)
        }));
    }

    const grafik = new Chart(document.getElementById('grafikPenjualan'), {
        type: 'bubble',
        data: {
            datasets: [{
                label: 'Produk',
                data: dataUntukGrafik(),
                backgroundColor: context => warnaIsi[context.dataIndex % warnaIsi.length],
                borderColor: context => warnaGaris[context.dataIndex % warnaGaris.length],
                borderWidth: 1.5,
                hoverBorderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'nearest', intersect: true },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const produk = context.raw;
                            return [
                                'Produk: ' + produk.produk,
                                'Harga: ' + formatRupiah(produk.x),
                                'Keuntungan: ' + formatRupiah(produk.y),
                                'Jumlah terjual: ' + produk.terjual + ' unit'
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Harga Produk (Rp)' },
                    ticks: { callback: value => 'Rp ' + Number(value).toLocaleString('id-ID') },
                    grid: { color: 'rgba(148, 163, 184, 0.18)' }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Keuntungan (Rp)' },
                    ticks: { callback: value => 'Rp ' + Number(value).toLocaleString('id-ID') },
                    grid: { color: 'rgba(148, 163, 184, 0.18)' }
                }
            }
        }
    });

    function tampilkanPesan(teks, error = false) {
        elemen.pesan.textContent = teks;
        elemen.pesan.classList.toggle('error', error);
    }

    function perbaruiRingkasan() {
        const totalTerjual = produkList.reduce((total, item) => total + Number(item.terjual), 0);
        const totalKeuntungan = produkList.reduce((total, item) => total + Number(item.keuntungan), 0);
        document.getElementById('totalProduk').textContent = produkList.length.toLocaleString('id-ID');
        document.getElementById('totalTerjual').textContent = totalTerjual.toLocaleString('id-ID') + ' unit';
        document.getElementById('totalKeuntungan').textContent = formatRupiah(totalKeuntungan);
    }

    function buatSel(teks) {
        const td = document.createElement('td');
        td.textContent = teks;
        return td;
    }

    function tampilkanTabel() {
        elemen.isiTabel.replaceChildren();
        produkList.forEach((item, indeks) => {
            const baris = document.createElement('tr');
            baris.append(
                buatSel(indeks + 1),
                buatSel(item.produk),
                buatSel(formatRupiah(item.harga)),
                buatSel(formatRupiah(item.keuntungan)),
                buatSel(Number(item.terjual).toLocaleString('id-ID') + ' unit')
            );

            const tdAksi = document.createElement('td');
            const pembungkus = document.createElement('div');
            pembungkus.className = 'aksi-tabel';

            const tombolEdit = document.createElement('button');
            tombolEdit.type = 'button';
            tombolEdit.className = 'btn-edit';
            tombolEdit.textContent = 'Edit';
            tombolEdit.setAttribute('aria-label', 'Edit ' + item.produk);
            tombolEdit.addEventListener('click', () => mulaiEdit(item.id));

            const tombolHapus = document.createElement('button');
            tombolHapus.type = 'button';
            tombolHapus.className = 'btn-hapus';
            tombolHapus.textContent = 'Hapus';
            tombolHapus.setAttribute('aria-label', 'Hapus ' + item.produk);
            tombolHapus.addEventListener('click', () => hapusProduk(item.id));

            pembungkus.append(tombolEdit, tombolHapus);
            tdAksi.append(pembungkus);
            baris.append(tdAksi);
            elemen.isiTabel.append(baris);
        });
        elemen.labelJumlah.textContent = 'Menampilkan ' + produkList.length.toLocaleString('id-ID') + ' produk';
    }

    function perbaruiTampilan() {
        grafik.data.datasets[0].data = dataUntukGrafik();
        grafik.update();
        tampilkanTabel();
        perbaruiRingkasan();
    }

    function resetForm() {
        elemen.form.reset();
        editId = null;
        elemen.form.action = rute.store;
        elemen.methodInput.value = 'POST';
        elemen.judulForm.textContent = 'Tambah Data Produk';
        elemen.tombolSimpan.textContent = '+ Simpan Produk';
        elemen.tombolBatal.hidden = true;
    }

    function mulaiEdit(id) {
        const item = produkList.find(produk => produk.id === id);
        if (!item) return;
        editId = item.id;
        elemen.form.action = rute.base + '/' + item.id;
        elemen.methodInput.value = 'PUT';
        elemen.produk.value = item.produk;
        elemen.harga.value = item.harga;
        elemen.keuntungan.value = item.keuntungan;
        elemen.terjual.value = item.terjual;
        elemen.judulForm.textContent = 'Edit Data Produk';
        elemen.tombolSimpan.textContent = 'Simpan Perubahan';
        elemen.tombolBatal.hidden = false;
        tampilkanPesan('Ubah data pada formulir, lalu klik Simpan Perubahan.');
        document.getElementById('bagianForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        elemen.produk.focus({ preventScroll: true });
    }

    async function kirimRequest(url, method, payload = null) {
        const opsi = {
            method: method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        };

        if (payload !== null) {
            opsi.headers['Content-Type'] = 'application/json';
            opsi.body = JSON.stringify(payload);
        }

        const respons = await fetch(url, opsi);
        const data = await respons.json().catch(() => ({}));

        if (!respons.ok) {
            const pesanValidasi = data.errors ? Object.values(data.errors).flat()[0] : null;
            throw new Error(pesanValidasi || data.message || 'Terjadi kesalahan saat memproses permintaan.');
        }

        return data;
    }

    async function hapusProduk(id) {
        const item = produkList.find(produk => produk.id === id);
        if (!item) return;
        if (!confirm('Yakin ingin menghapus produk "' + item.produk + '"?')) return;

        try {
            const data = await kirimRequest(rute.base + '/' + item.id, 'DELETE');
            produkList = data.products;
            if (editId === item.id) resetForm();
            perbaruiTampilan();
            tampilkanPesan(data.message || 'Produk berhasil dihapus.');
        } catch (error) {
            tampilkanPesan(error.message, true);
        }
    }

    elemen.form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const nama = elemen.produk.value.trim();
        const harga = Number(elemen.harga.value);
        const keuntungan = Number(elemen.keuntungan.value);
        const terjual = Number(elemen.terjual.value);

        if (!nama) {
            tampilkanPesan('Nama produk wajib diisi.', true);
            elemen.produk.focus();
            return;
        }
        if (![harga, keuntungan, terjual].every(Number.isInteger) || harga < 0 || keuntungan < 0 || terjual < 0) {
            tampilkanPesan('Harga, keuntungan, dan jumlah terjual harus berupa bilangan bulat 0 atau lebih.', true);
            return;
        }

        const sedangEdit = editId !== null;
        const url = sedangEdit ? (rute.base + '/' + editId) : rute.store;
        const method = sedangEdit ? 'PUT' : 'POST';

        elemen.tombolSimpan.disabled = true;

        try {
            const data = await kirimRequest(url, method, {
                produk: nama,
                harga: harga,
                keuntungan: keuntungan,
                terjual: terjual
            });
            produkList = data.products;
            perbaruiTampilan();
            resetForm();
            tampilkanPesan(data.message || (sedangEdit ? 'Data produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.'));
        } catch (error) {
            tampilkanPesan(error.message, true);
        } finally {
            elemen.tombolSimpan.disabled = false;
        }
    });

    elemen.tombolBatal.addEventListener('click', function() {
        resetForm();
        tampilkanPesan('Edit dibatalkan.');
    });

    elemen.formReset.addEventListener('submit', async function(event) {
        event.preventDefault();
        const setuju = confirm('Kembalikan semua data ke 30 data awal? Perubahan, tambahan, dan penghapusan akan dibatalkan.');
        if (!setuju) return;

        elemen.tombolReset.disabled = true;

        try {
            const data = await kirimRequest(rute.reset, 'POST');
            produkList = data.products;
            resetForm();
            perbaruiTampilan();
            tampilkanPesan(data.message || 'Data awal berhasil dikembalikan.');
        } catch (error) {
            tampilkanPesan(error.message, true);
        } finally {
            elemen.tombolReset.disabled = false;
        }
    });

    // Tampilkan data saat halaman pertama kali dibuka.
    perbaruiTampilan();
</script>
</body>
</html>
