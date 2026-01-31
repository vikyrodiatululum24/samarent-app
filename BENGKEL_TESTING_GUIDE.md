# Testing BengkelResource

## Test Manual via Browser

1. **Akses halaman Bengkel**
   - Buka: `http://localhost/samarent-app/admin/bengkels`
   - Pastikan icon wrench-screwdriver muncul di sidebar

2. **Test Create Bengkel**
   - Klik tombol "Create"
   - **Section Informasi Bengkel:**
     - Nama: "Bengkel Jaya Motor"
     - Keterangan: "Bengkel spesialis transmisi"
   - **Section Alamat Lengkap:**
     - Pilih Provinsi: cari "Jawa Barat"
     - Pilih Kab/Kota: cari "Bandung"
     - Pilih Kecamatan: pilih salah satu
     - Pilih Desa: pilih salah satu
     - Alamat: "Jl. Sudirman No. 123, RT 02/RW 05"
     - Google Maps: "https://maps.google.com/..."
   - **Section Kontak Bengkel:**
     - Expand section (klik untuk membuka)
     - Klik "Tambah Kontak"
     - Nama: "Pak Budi"
     - No Telp: "081234567890"
     - Klik "Tambah Kontak" lagi untuk kontak kedua
     - Nama: "Pak Andi"
     - No Telp: "081234567891"
   - Klik "Create"
   - Periksa data tersimpan dengan benar (termasuk 2 kontak)

3. **Test Cascading Dropdown**
   - Ubah provinsi → pastikan kab/kota, kecamatan, desa ter-reset
   - Ubah kab/kota → pastikan kecamatan, desa ter-reset
   - Dropdown kab/kota disabled saat provinsi belum dipilih
   - Dropdown kecamatan disabled saat kab/kota belum dipilih
   - Dropdown desa disabled saat kecamatan belum dipilih

4. **Test Table**
   - Pastikan kolom nama, provinsi, kab/kota, alamat muncul
   - Test search di kolom nama
   - Test filter provinsi
   - Test tabs: Semua, Dengan Google Maps, Tanpa Google Maps
   - Badge jumlah kontak harus 0 untuk bengkel baru
   - Klik icon Maps → harus ada button "Buka Maps"

5. **Test View/Detail**
   - Klik icon "View" pada salah satu bengkel
   - Pastikan semua data muncul dengan rapi
   - Section Informasi Bengkel, Alamat Lengkap, Kontak Bengkel, Informasi Sistem
   - Icon untuk setiap field alamat
   - Expand section "Kontak Bengkel" untuk melihat daftar kontak
   - Nomor telepon harus copyable (ada icon copy)

6. **Test Kontak Bengkel di Form Create/Edit**
   - Section "Kontak Bengkel" collapsed by default
   - Klik untuk expand
   - Klik "Tambah Kontak" → muncul form nama & no_telp
   - Klik "Tambah Kontak" lagi → muncul form kedua
   - Bisa hapus kontak dengan icon delete
   - Setiap kontak bisa di-collapse/expand
   - Label item menampilkan nama kontak
   - Default 0 item (tidak ada kontak default)

7. **Test Edit**
   - Edit bengkel yang sudah ada
   - Pastikan dropdown terisi dengan nilai yang benar
   - Pastikan kontak yang sudah ada muncul di repeater
   - Tambah kontak baru
   - Edit kontak yang sudah ada
   - Hapus salah satu kontak
   - Ubah alamat ke wilayah lain
   - Pastikan cascading tetap berfungsi
   - Save dan cek perubahan tersimpan

8. **Test Delete**
   - Hapus bengkel test
   - Pastikan terhapus dari list

## Test Query Database

```sql
-- Cek data bengkel
SELECT * FROM bengkels ORDER BY created_at DESC LIMIT 5;

-- Cek relasi kontak
SELECT b.nama AS bengkel, k.nama AS kontak, k.no_telp
FROM bengkels b
LEFT JOIN kontak_bengkels k ON k.bengkel_id = b.id
ORDER BY b.created_at DESC;

-- Cek data wilayah
SELECT * FROM wilayah WHERE level = 'provinsi' LIMIT 5;
SELECT * FROM wilayah WHERE level = 'kabupaten' AND parent_id = 12 LIMIT 5; -- parent_id = ID Jawa Barat
```

## Expected Results

✅ Form menyimpan NAMA wilayah (string), bukan ID
✅ Cascading dropdown bekerja dengan baik
✅ Kontak bengkel bisa ditambah langsung di form create/edit
✅ Repeater kontak dengan button "Tambah Kontak"
✅ Data kontak ter-relasi dan tersimpan dengan benar
✅ Semua field searchable dan sortable
✅ Filter dan tabs berfungsi
✅ Google Maps link bisa diklik
✅ Tampilan responsive dan user-friendly
✅ Section kontak collapsed by default untuk UI yang bersih
