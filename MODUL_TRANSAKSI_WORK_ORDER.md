# Dokumentasi Modul Transaksi Work Order

## Deskripsi

Modul Transaksi Work Order adalah fitur untuk membuat dan mengelola work order baru dengan 3 layer input (Header, Detail Jasa, Detail Barang).

## Fitur Utama

### 1. **Form Input Work Order**

- **Header Information:**

  - Customer (searchable dropdown) **[Required]**
  - Kendaraan (searchable dropdown) **[Required]**
  - KM Awal & KM Akhir
  - Montir/Mekanik (dropdown) **[Required]**
  - Picker/Marketing (dropdown) **[Required]**
  - Keterangan

- **Detail Jasa:**

  - Tambah/hapus jasa dengan modal dialog
  - Auto-fill data jasa dari master
  - Perhitungan otomatis dengan discount
  - Kategori tarif standard

- **Detail Barang:**
  - Tambah/hapus barang dengan modal dialog
  - Auto-fill data barang dari master
  - Perhitungan otomatis dengan discount
  - Informasi merek dan jenis barang

### 2. **List Work Order**

- Pencarian berdasarkan NoOrder, Customer, NoPolisi
- Filter berdasarkan tanggal
- Pagination
- Status work order

### 3. **Auto Generate NoOrder**

- Format: `SP-YYMM?????` (YY=Tahun, MM=Bulan, ?????=Counter 5 digit)
- Counter otomatis per bulan

### 4. **Update Master Data**

- Saat simpan, otomatis update:
  - `FileKendaraan.KodeCustomer`
  - `FileCustomer.KodeKendaraan`
- Status default: `StatusOrder = 0` (Belum diproses)

## Struktur File

```
app/
├── models/
│   └── TransaksiWorkOrderModel.php      # Model untuk CRUD dan referensi data
├── controllers/
│   └── TransaksiWorkOrderController.php # Controller untuk routing dan logic
└── views/
    └── transaksiworkorder/
        └── index.php                    # View form dan list work order
```

## Routing

### Main Routes

- `GET /transaksi-work-order` - Halaman utama (form & list)
- `POST /transaksi-work-order/save` - Simpan work order baru

### AJAX Routes

- `GET /transaksi-work-order/search-customers` - Cari customer
- `GET /transaksi-work-order/search-vehicles` - Cari kendaraan
- `GET /transaksi-work-order/search-jasa` - Cari jasa
- `GET /transaksi-work-order/search-barang` - Cari barang
- `GET /transaksi-work-order/get-customer` - Get detail customer
- `GET /transaksi-work-order/get-vehicle` - Get detail kendaraan
- `GET /transaksi-work-order/get-jasa` - Get detail jasa
- `GET /transaksi-work-order/get-barang` - Get detail barang

## Database Tables

### Tabel Utama

1. **HeaderOrder** - Header work order
2. **DetailOrderJasa** - Detail jasa work order
3. **DetailOrderBarang** - Detail barang work order

### Tabel Referensi

1. **FileCustomer** - Master customer
2. **FileKendaraan** - Master kendaraan
3. **FileMontir** - Master montir/mekanik
4. **FilePicker** - Master picker/marketing
5. **FileJasa** - Master jasa
6. **FileBarang** - Master barang
7. **FileTarif** - Tarif jasa per kategori
8. **TabelKategoriJasa** - Kategori tarif jasa
9. **TabelMerek** - Merek barang
10. **TabelJenis** - Jenis barang

## Dependencies

### Frontend

- **Bootstrap 5** - Framework CSS
- **Select2 4.1.0** - Searchable dropdown
- **jQuery** - JavaScript library
- **Font Awesome** - Icons

### Backend

- **PHP 7.4+** - Server-side language
- **PDO** - Database connection
- **SQL Server** - Database

## Cara Menggunakan

### 1. Buat Work Order Baru

1.  Klik tombol "Work Order Baru"
2.  Pilih Customer (ketik untuk mencari)
3.  Pilih Kendaraan (ketik untuk mencari)
4.  Isi KM Awal & KM Akhir
5.  Pilih Montir dan Picker
6.  Tambah detail jasa dengan klik "Tambah Jasa"
7.  Tambah detail barang dengan klik "Tambah Barang"
8.  Klik "Simpan Work Order" (akan muncul konfirmasi)

### 2. Lihat Daftar Work Order

- Gunakan search box untuk mencari
- Filter berdasarkan tanggal
- Klik Reset untuk clear filter
- Pagination untuk navigasi halaman

### 3. Status Work Order

- **0** - Belum diproses (secondary/abu-abu)
- **1** - Sedang diproses (info/biru)
- **2** - Proses Selesai (warning/kuning)
- **3** - Faktur dibuat (primary/biru)
- **4** - Telah dibayar (success/hijau)
- **5** - Dibatalkan (danger/merah)

## Validasi

### Validasi Input

- Customer wajib diisi
- Kendaraan wajib diisi
- Montir (Mekanik) wajib diisi
- Picker (Marketing) wajib diisi
- Minimal 1 detail jasa atau barang
- Jumlah harus > 0

### Konfirmasi

- Konfirmasi sebelum menyimpan work order
- Konfirmasi sebelum menghapus detail jasa/barang
- Konfirmasi sebelum cancel input

## Fitur Responsive

- Mobile-friendly design
- Responsive table dengan horizontal scroll
- Hamburger menu untuk mobile
- Touch-friendly buttons dan inputs

## Error Handling

- Validasi client-side dengan JavaScript
- Validasi server-side dengan PHP
- Error logging ke file log
- User-friendly error messages
- Transaction rollback jika terjadi error

## Security

- Session-based authentication
- SQL injection protection (PDO prepared statements)
- XSS protection (htmlspecialchars)
- CSRF protection ready

## Performance

- Pagination untuk list besar
- AJAX untuk pencarian real-time
- Lazy loading untuk dropdown
- Optimized SQL queries dengan JOIN

## Future Enhancements

1. Edit work order yang sudah ada
2. Delete work order (soft delete)
3. Print/Export work order ke PDF
4. Email notification
5. WhatsApp integration
6. Barcode scanning untuk barang
7. Upload foto kendaraan
8. Signature mekanik & customer
9. Estimasi waktu pengerjaan
10. History log perubahan

## Support

Untuk pertanyaan atau issue, hubungi tim development.

---

**Version:** 1.0.0  
**Last Updated:** <?php echo date('Y-m-d'); ?>  
**Author:** Development Team
