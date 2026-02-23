# Panduan Import/Export Data BMHP

## Export Excel
1. Buka halaman BMHP di menu Inventory
2. Klik tombol "Export Excel" (ikon download) di bagian atas tabel
3. File Excel akan otomatis diunduh dengan nama `bmhp.xlsx`
4. File berisi semua data BMHP dengan kolom:
   - Nama BMHP
   - Satuan
   - Stok Awal
   - Stok Sisa
   - Harga Satuan
   - Stok Minimum

## Import Excel
1. Siapkan file Excel dengan format yang sesuai:
   - **Kolom wajib**: `nama_bmhp` (atau `name`)
   - **Kolom opsional**: `satuan`, `stok_awal`, `stok_sisa`, `harga_satuan`, `stok_minimum`

2. Format kolom yang diterima:
   - `nama_bmhp`: Text, maksimal 255 karakter (wajib)
   - `satuan`: Text, maksimal 50 karakter
   - `stok_awal`: Angka, minimal 0
   - `stok_sisa`: Angka, minimal 0
   - `harga_satuan`: Angka, minimal 0
   - `stok_minimum`: Angka, minimal 0

3. Cara import:
   - Klik tombol "Import Excel" (ikon upload) di bagian atas tabel
   - Pilih file Excel (.xlsx atau .xls)
   - Klik "Import"
   - Tunggu proses selesai

4. Notifikasi akan muncul:
   - Hijau: Import berhasil
   - Merah: Import gagal dengan pesan error

## Contoh Format Excel

| nama_bmhp | satuan | stok_awal | stok_sisa | harga_satuan | stok_minimum |
|-----------|--------|-----------|-----------|--------------|--------------|
| Masker Bedah | Box | 100 | 85 | 50000 | 10 |
| Handsanitizer | Botol | 50 | 30 | 25000 | 5 |
| Sarung Tangan | Box | 200 | 150 | 75000 | 20 |

## Catatan Penting
- File Excel maksimal 10MB
- Pastikan format kolom sesuai dengan yang ditentukan
- Data duplikat akan ditambahkan sebagai data baru
- File akan otomatis dihapus setelah proses import
- Gunakan file export sebagai template untuk memastikan format yang benar
