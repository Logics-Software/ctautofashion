# ✅ Dompdf Sudah Terinstall!

## Status Instalasi: COMPLETE

Dompdf dan semua dependencies sudah berhasil diinstall pada:
**19 Oktober 2024**

### Dependencies yang Terinstall:

| Library          | Status       | Lokasi                            |
| ---------------- | ------------ | --------------------------------- |
| **Dompdf Core**  | ✅ Installed | `libs/dompdf/src/`                |
| **Cpdf**         | ✅ Installed | `libs/dompdf/lib/Cpdf.php`        |
| **HTML5 Parser** | ✅ Installed | `libs/dompdf/lib/html5-php/`      |
| **Font Library** | ✅ Installed | `libs/dompdf/lib/php-font-lib/`   |
| **SVG Library**  | ✅ Installed | `libs/dompdf/lib/php-svg-lib/`    |
| **CSS Parser**   | ✅ Installed | `libs/dompdf/lib/php-css-parser/` |
| **Autoloader**   | ✅ Created   | `libs/dompdf/autoload.inc.php`    |

---

# Instalasi Dompdf untuk Auto-Generate PDF (ARCHIVE)

## Cara 1: Download Manual (Recommended)

### Step 1: Download Dompdf

1. Download Dompdf dari GitHub: https://github.com/dompdf/dompdf/releases
2. Pilih versi terbaru (contoh: `v2.0.4`)
3. Download file ZIP: `dompdf-2.0.4.zip`

### Step 2: Extract ke folder libs

1. Extract file ZIP yang sudah didownload
2. Copy folder `dompdf` ke dalam folder `libs/` di project ini
3. Struktur folder harus seperti ini:
   ```
   ctautofashion/
   └── libs/
       └── dompdf/
           ├── autoload.inc.php
           ├── src/
           ├── lib/
           └── ...
   ```

### Step 3: Verifikasi

- Pastikan file `libs/dompdf/autoload.inc.php` ada
- Setelah ini, tombol "Print Work Order PDF" akan otomatis download PDF file

---

## Cara 2: Via Git Clone (Alternative)

Jika memiliki Git terinstall:

```bash
cd libs
git clone https://github.com/dompdf/dompdf.git
cd dompdf
git checkout v2.0.4
```

---

## Cara 3: Via Composer (Jika ada Composer)

```bash
composer require dompdf/dompdf
```

Lalu copy folder `vendor/dompdf/dompdf` ke `libs/dompdf`

---

## Catatan Penting

### Jika Dompdf TIDAK diinstall:

- Sistem akan menggunakan **fallback mode** (HTML print view)
- User perlu manual print/save as PDF dari browser

### Jika Dompdf SUDAH diinstall:

- Sistem akan **otomatis generate dan download file PDF**
- File PDF langsung ter-download dengan nama: `WorkOrder_[NoOrder]_[Timestamp].pdf`

---

## Troubleshooting

### Error: "Class 'Dompdf\Dompdf' not found"

**Solusi:** Pastikan path `libs/dompdf/autoload.inc.php` benar

### Error: Font issues

**Solusi:** Dompdf sudah include font default (DejaVu Sans)

### PDF tidak ter-generate

**Solusi:**

1. Check PHP memory limit (minimal 128MB)
2. Check folder permissions untuk write cache
3. Check error log di server

---

## Link Download Langsung

**Dompdf v2.0.4:**
https://github.com/dompdf/dompdf/archive/refs/tags/v2.0.4.zip

**Latest Release:**
https://github.com/dompdf/dompdf/releases/latest

---

## Setelah Install

1. Refresh halaman aplikasi
2. Buka modal detail Work Order
3. Klik tombol "Print Work Order PDF"
4. File PDF akan otomatis ter-download
