# Struktur JavaScript - Clean Code Refactor

## Daftar File Module Helper

### 1. **api-helper.js**

Menangani semua AJAX calls untuk operasi API.

**Fungsi:**

- `ApiHelper.searchBarang(kode, config)` - Cari barang berdasarkan kode

**Contoh penggunaan:**

```javascript
ApiHelper.searchBarang(kode, window.barangMasukConfig)
    .done((response) => {
        // Handle success
    })
    .fail(() => {
        // Handle error
    });
```

---

### 2. **table-helper.js**

Mengelola operasi tabel (create row, delete row, update numbering, dll).

**Fungsi:**

- `TableHelper.createRowMasuk(barang, nomorTransaksi)` - Buat baris untuk barang masuk dengan serial
- `TableHelper.createRowKeluar(barang, nomorTransaksi)` - Buat baris untuk barang keluar dengan serial
- `TableHelper.createRowSimple(barang)` - Buat baris standar (tanpa serial)
- `TableHelper.deleteRow($row)` - Hapus baris
- `TableHelper.updateRowNumbers()` - Perbarui nomor urut
- `TableHelper.barangExists(barangId)` - Cek apakah barang sudah ada
- `TableHelper.getRowByBarangId(barangId)` - Dapatkan row dari ID barang
- `TableHelper.clearEmptyMessage()` - Hapus pesan kosong
- `TableHelper.showEmptyMessage()` - Tampilkan pesan kosong

**Contoh penggunaan:**

```javascript
// Buat baris baru
const $newRow = TableHelper.createRowMasuk(barang, nomorTransaksi);

// Hapus row
TableHelper.deleteRow($row);

// Update nomor
TableHelper.updateRowNumbers();
```

---

### 3. **serial-helper.js**

Mengelola operasi serial (tambah, hapus, hitung, validasi).

**Fungsi:**

- `SerialHelper.addRow(row, index)` - Tambah baris serial kosong
- `SerialHelper.addAutomatic(row, index, serial)` - Tambah serial otomatis dengan nilai default
- `SerialHelper.deleteRow($serialRow)` - Hapus baris serial
- `SerialHelper.calculateTotal(row)` - Hitung total serial dan sinkronkan dengan qty barang
- `SerialHelper.validate()` - Validasi semua serial sebelum submit
- `SerialHelper.serialExists(row, serialNumber)` - Cek apakah serial sudah ada

**Contoh penggunaan:**

```javascript
// Tambah serial kosong
SerialHelper.addRow(row, index);

// Tambah serial dengan nilai default (dari API)
SerialHelper.addAutomatic(row, index, serial);

// Hitung total serial
SerialHelper.calculateTotal(row);

// Validasi sebelum submit
if (!SerialHelper.validate()) {
    e.preventDefault();
}
```

---

### 4. **scanner-helper.js**

Mengelola fungsi scanner (fokus, reset, auto-focus, dll).

**Fungsi:**

- `ScannerHelper.focus(selector)` - Fokus ke input scanner
- `ScannerHelper.reset(selector)` - Kosongkan dan fokus ke input scanner
- `ScannerHelper.autoFocus(selector, interval)` - Auto-focus setiap N detik
- `ScannerHelper.getValue(selector)` - Dapatkan nilai input scanner
- `ScannerHelper.isValid(selector)` - Validasi input tidak kosong

**Contoh penggunaan:**

```javascript
// Initialize scanner
ScannerHelper.focus();
ScannerHelper.autoFocus(); // Fokus otomatis setiap 10 detik

// Reset scanner
ScannerHelper.reset();

// Dapatkan nilai
const kode = ScannerHelper.getValue();

// Validasi
if (!ScannerHelper.isValid()) {
    return;
}
```

---

## File-file Utama (Refactored)

### 1. **barang-masuk-scanner.js**

Proses masuk barang dengan scanner dan serial otomatis.

**Dependencies:**

- api-helper.js, table-helper.js, serial-helper.js, scanner-helper.js, helper.js

**Flow:**

1. Scan barcode → `scanBarcode()`
2. Cari barang via API → `ApiHelper.searchBarang()`
3. Tambah ke keranjang → `addToCart()`
4. Input qty dan serial → Event handlers
5. Validasi → `SerialHelper.validate()`

---

### 2. **barang-masuk.js**

Proses masuk barang standar (tanpa scanner).

**Dependencies:**

- api-helper.js, table-helper.js, helper.js

**Flow:**

1. Klik tombol atau Enter → `scanBarcode()`
2. Cari barang via API
3. Tambah ke keranjang → `addToCart()`
4. Auto-increment qty jika duplicate

---

### 3. **barang-keluar.js**

Proses keluar barang standar (tanpa scanner).

**Dependencies:**

- api-helper.js, table-helper.js, helper.js

**Flow:**

1. Klik tombol atau Enter → `searchBarang()`
2. Validasi stok > 0
3. Cek duplicate
4. Tambah ke keranjang → `addToCart()`
5. Validasi qty tidak melebihi stok

---

### 4. **barang-keluar-scanner.js**

Proses keluar barang dengan scanner dan serial otomatis.

**Dependencies:**

- api-helper.js, table-helper.js, serial-helper.js, scanner-helper.js, helper.js

**Flow:**

1. Scan barcode → `scanBarcode()`
2. Validasi serial tersedia
3. Buat baris dan tambah serial otomatis → `processBarang()`
4. Hitung total serial otomatis
5. Validasi → `SerialHelper.validate()`

---

## Cara Menggunakan

### Setup di Blade Template

Pastikan load file dalam urutan yang benar:

```blade
<!-- Module Helpers (load dulu) -->
<script src="{{ asset('js/modules/api-helper.js') }}"></script>
<script src="{{ asset('js/modules/table-helper.js') }}"></script>
<script src="{{ asset('js/modules/serial-helper.js') }}"></script>
<script src="{{ asset('js/modules/scanner-helper.js') }}"></script>

<!-- Helper umum -->
<script src="{{ asset('js/helper.js') }}"></script>

<!-- Main Script (load paling akhir) -->
<script src="{{ asset('js/barang-masuk-scanner.js') }}"></script>
```

---

## Keuntungan Clean Code

✅ **Modular** - Setiap helper punya satu responsiblitas  
✅ **Reusable** - Fungsi dapat digunakan di berbagai file  
✅ **Maintainable** - Mudah mencari dan memperbaiki bug  
✅ **Readable** - Code lebih jelas dan terstruktur  
✅ **DRY** - Tidak ada kode duplikat

---

## Catatan

- Pastikan `window.barangMasukConfig` dan `window.barangKeluarConfig` sudah di-setup di controller/view
- Pastikan file `helper.js` sudah berisi `showAlert()`, `resetInput()`, etc
- Semua AJAX calls menggunakan promise pattern `.done().fail()`
