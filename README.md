# CTAutoFashion Management System

Framework aplikasi MVC sederhana untuk sistem manajemen CTAutoFashion.

## Fitur

- ✅ Framework MVC sederhana
- ✅ Autentikasi user dengan database SQL Server
- ✅ Login case-insensitive (huruf besar/kecil)
- ✅ Dashboard dengan menu navigasi
- ✅ Bootstrap 5 untuk UI
- ✅ Font Awesome untuk icons
- ✅ CSS eksternal untuk styling
- ✅ Session management
- ✅ Responsive design

## Persyaratan Sistem

- PHP 7.4 atau lebih baru
- SQL Server dengan database "CSAUTOFASHION"
- Web server (Apache/Nginx) dengan mod_rewrite
- PDO SQL Server driver

## Konfigurasi Database

Database menggunakan SQL Server dengan konfigurasi:

- Server: localhost
- Username: sa
- Password: 051199
- Database: CSAUTOFASHION
- Table: \_FileUser

## Struktur Aplikasi

```
ctautofashion/
├── index.php                 # Entry point aplikasi
├── .htaccess                # URL rewriting
├── README.md                # Dokumentasi
├── app/
│   ├── config/
│   │   └── database.php     # Konfigurasi database
│   ├── controllers/
│   │   ├── AuthController.php
│   │   └── DashboardController.php
│   ├── models/
│   │   └── UserModel.php
│   └── views/
│       ├── layouts/
│       │   └── main.php
│       ├── auth/
│       │   └── login.php
│       └── dashboard/
│           └── index.php
└── assets/
    ├── css/
    │   ├── bootstrap.min.css
    │   ├── fontawesome.min.css
    │   └── custom.css
    ├── js/
    │   └── bootstrap.bundle.min.js
    └── fonts/
        └── fontawesome-webfont.woff2
```

## Cara Menjalankan

1. Pastikan database SQL Server sudah berjalan
2. Pastikan table `_FileUser` sudah ada dengan kolom `UserID` dan `PasswordOnline`
3. Akses aplikasi melalui browser: `http://localhost/ctautofashion`
4. Login menggunakan UserID dan PasswordOnline dari table `_FileUser`

## Routing

Aplikasi menggunakan routing sederhana:

- `/` - Redirect ke login atau dashboard
- `/login` - Halaman login
- `/dashboard` - Dashboard utama
- `/logout` - Logout user

## Keamanan

- Password hashing (siap untuk implementasi)
- Session management
- XSS protection
- SQL injection protection dengan PDO prepared statements
- Case-insensitive login untuk kemudahan penggunaan

## Pengembangan

Untuk menambahkan fitur baru:

1. Buat controller baru di `app/controllers/`
2. Buat model baru di `app/models/`
3. Buat view baru di `app/views/`
4. Tambahkan route di `index.php`

## Catatan

- Login menggunakan case-insensitive matching (huruf besar/kecil tidak berpengaruh)
- Semua CSS dan JavaScript disimpan secara lokal
- Framework ini sangat sederhana dan mudah dikembangkan
