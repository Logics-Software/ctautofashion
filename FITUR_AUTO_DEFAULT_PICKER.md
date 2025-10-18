# 🎯 Fitur Auto Default Picker untuk TipeUser = 1

## 📋 Overview

Fitur ini secara otomatis mengisi field **Picker/Marketing** pada form "Transaksi Work Order" berdasarkan data dari table `TipeUser` jika user login memiliki `TipeUser = 1`.

---

## 🔧 Implementasi

### 1. **Database Requirement**

**Table:** `TipeUser`

**Struktur:**

```sql
TipeUser
├─ UserID (Primary Key)
├─ TipeUser (INT) -- Value: 0, 1, 2, 3, dst
├─ KodePicker (VARCHAR) -- KodePicker default untuk user ini
└─ ... (fields lainnya)
```

**Kondisi:**

- `TipeUser = 1` (Marketing/Picker)
- `KodePicker IS NOT NULL` dan `KodePicker != ''`

---

### 2. **Model Layer** ✅

**File:** `app/models/TransaksiWorkOrderModel.php`

**Method Baru:**

```php
public function getDefaultPickerByUser($userID) {
    $sql = "SELECT tu.KodePicker, tu.TipeUser, fp.NamaPicker
            FROM TipeUser tu
            LEFT JOIN FilePicker fp ON tu.KodePicker = fp.KodePicker
            WHERE tu.UserID = ? AND tu.TipeUser = 1
              AND tu.KodePicker IS NOT NULL
              AND tu.KodePicker != ''";

    // If found, return full picker details from FilePicker
    return $this->getPickerByCode($result['KodePicker']);
}
```

**Return:**

- Array dengan data picker lengkap jika ditemukan
- `null` jika tidak ditemukan atau kondisi tidak terpenuhi

---

### 3. **Controller Layer** ✅

**File:** `app/controllers/TransaksiWorkOrderController.php`

**Method:** `index()`

**Perubahan:**

```php
// Get user info from session
$userID = $_SESSION['user_id'] ?? null;
$tipeUser = isset($_SESSION['tipe_user']) ? (int)$_SESSION['tipe_user'] : null;

// Get default picker if TipeUser = 1
$defaultPicker = null;
if ($tipeUser === 1 && $userID) {
    $defaultPicker = $this->model->getDefaultPickerByUser($userID);
}

// Pass to view
$data = [
    // ... existing data ...
    'defaultPicker' => $defaultPicker
];
```

---

### 4. **View Layer** ✅

**File:** `app/views/transaksiworkorder/index.php`

**JavaScript Initialization:**

```javascript
// Initialize Choices.js for Picker
pickerChoice = new Choices('#selectPicker', { ... });

// Auto-fill default picker when form is opened (triggered by btnNewOrder click)
document.getElementById('btnNewOrder').addEventListener('click', function() {
    document.getElementById('formSection').style.display = 'block';
    document.getElementById('listSection').style.display = 'none';
    resetForm();

    // Auto-fill default picker if TipeUser = 1
    <?php if (isset($defaultPicker) && $defaultPicker): ?>
    setTimeout(function() {
        const defaultPickerData = {
            KodePicker: '<?php echo htmlspecialchars($defaultPicker['KodePicker']); ?>',
            NamaPicker: '<?php echo htmlspecialchars($defaultPicker['NamaPicker']); ?>',
            AlamatPicker: '<?php echo htmlspecialchars($defaultPicker['AlamatPicker'] ?? ''); ?>',
            NoTelepon: '<?php echo htmlspecialchars($defaultPicker['NoTelepon'] ?? ''); ?>'
        };

        pickerChoice.clearStore();
        pickerChoice.setChoices([{
            value: defaultPickerData.KodePicker,
            label: defaultPickerData.NamaPicker,
            selected: true,
            customProperties: defaultPickerData
        }], 'value', 'label', true);
    }, 100); // Delay to ensure form is visible and DOM is ready
    <?php endif; ?>
});
```

**Key Points:**

- Auto-fill is triggered **after** form section is visible (not on page load)
- Uses `setTimeout(100ms)` to ensure Choices.js can render properly
- Calls `clearStore()` before `setChoices()` to ensure clean state

---

## 🎯 User Flow

### **Scenario 1: User dengan TipeUser = 1 (Marketing)**

1. **Login** sebagai user dengan `TipeUser = 1`
2. **Klik** "Buat Work Order Baru"
3. **Form terbuka:**
   - ✅ Field Customer: Kosong (harus diisi manual)
   - ✅ Field Kendaraan: Kosong (harus diisi manual)
   - ✅ Field Montir: Kosong (harus diisi manual)
   - ✅ **Field Picker: Sudah terisi otomatis** dengan data dari `TipeUser.KodePicker`
4. User dapat:
   - Menggunakan picker default (biarkan saja)
   - Mengganti picker (klik X, pilih picker lain)
5. **Simpan** Work Order

---

### **Scenario 2: User dengan TipeUser ≠ 1 (Admin/Manager/Operator)**

1. **Login** sebagai user dengan `TipeUser = 0, 2, 3, dst`
2. **Klik** "Buat Work Order Baru"
3. **Form terbuka:**
   - Field Customer: Kosong
   - Field Kendaraan: Kosong
   - Field Montir: Kosong
   - **Field Picker: Kosong** (harus diisi manual)
4. **Simpan** Work Order

---

## 📊 Data Flow

```
┌─────────────────────┐
│   User Login        │
│   (Session Start)   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Check TipeUser     │
│  from Session       │
└──────────┬──────────┘
           │
           ▼
    TipeUser = 1?
           │
     ┌─────┴─────┐
     │           │
    Yes         No
     │           │
     ▼           ▼
┌─────────┐  ┌──────────┐
│ Query   │  │  Picker  │
│TipeUser │  │  Kosong  │
│ Table   │  └──────────┘
└────┬────┘
     │
     ▼
  KodePicker
  Not Empty?
     │
  ┌──┴──┐
  │     │
 Yes   No
  │     │
  ▼     ▼
┌─────┐ ┌────────┐
│Query│ │ Kosong │
│File │ └────────┘
│Picker│
└──┬──┘
   │
   ▼
┌──────────────┐
│ Auto-fill    │
│ Picker Field │
└──────────────┘
```

---

## 🧪 Testing Guide

### **Test 1: TipeUser = 1 dengan KodePicker Valid**

**Setup:**

```sql
-- Update TipeUser untuk test user
UPDATE TipeUser
SET TipeUser = 1, KodePicker = 'PICKER001'
WHERE UserID = 'testuser';

-- Pastikan picker exists di FilePicker
SELECT * FROM FilePicker WHERE KodePicker = 'PICKER001' AND Status = 1;
```

**Steps:**

1. Login sebagai `testuser`
2. Akses `/transaksi-work-order`
3. Klik "Buat Work Order Baru"
4. **Expected:** Field Picker sudah terisi dengan "PICKER001"
5. Check console log: `Default Picker auto-filled: PICKER001 - [Nama Picker]`

---

### **Test 2: TipeUser = 1 dengan KodePicker Kosong**

**Setup:**

```sql
UPDATE TipeUser
SET TipeUser = 1, KodePicker = NULL
WHERE UserID = 'testuser';
```

**Steps:**

1. Login sebagai `testuser`
2. Akses `/transaksi-work-order`
3. Klik "Buat Work Order Baru"
4. **Expected:** Field Picker kosong (harus diisi manual)

---

### **Test 3: TipeUser ≠ 1**

**Setup:**

```sql
UPDATE TipeUser
SET TipeUser = 0, KodePicker = 'PICKER001'
WHERE UserID = 'testuser';
```

**Steps:**

1. Login sebagai `testuser`
2. Akses `/transaksi-work-order`
3. Klik "Buat Work Order Baru"
4. **Expected:** Field Picker kosong (meskipun ada KodePicker di TipeUser)

---

### **Test 4: User Bisa Mengganti Default Picker**

**Steps:**

1. Login sebagai user TipeUser = 1
2. Form terbuka dengan picker default
3. Klik **X** pada field Picker untuk hapus
4. Ketik nama picker lain di search
5. Pilih picker baru
6. **Expected:** Picker berubah ke yang baru dipilih
7. Simpan Work Order
8. **Expected:** Work Order tersimpan dengan picker yang baru

---

## 🔍 Debugging

### **Check if Default Picker is Loaded**

**Browser Console:**

```javascript
// Should show: "Default Picker auto-filled: PICKER001 - Nama Picker"
console.log("Check browser console for auto-fill message");
```

**PHP Error Log:**

```php
// Should show in error log
[timestamp] Default Picker for UserID testuser: PICKER001 - Nama Picker
```

---

### **Common Issues**

#### Issue 1: "Picker tidak auto-fill meskipun TipeUser = 1"

**Diagnosa:**

```sql
-- Check TipeUser data
SELECT UserID, TipeUser, KodePicker
FROM TipeUser
WHERE UserID = 'your_user_id';

-- Check if KodePicker exists in FilePicker
SELECT * FROM FilePicker
WHERE KodePicker = (SELECT KodePicker FROM TipeUser WHERE UserID = 'your_user_id')
AND Status = 1;
```

**Possible Causes:**

- `KodePicker` adalah `NULL` atau empty string
- `KodePicker` tidak ada di table `FilePicker`
- `FilePicker.Status = 0` (tidak aktif)
- Session `tipe_user` tidak set atau salah

---

#### Issue 2: "Error di console saat load form"

**Check:**

```javascript
// Check if PHP data passed correctly
console.log('Has default picker?', <?php echo isset($defaultPicker) ? 'true' : 'false'; ?>);

// Check picker data structure
<?php if (isset($defaultPicker)): ?>
console.log('Default picker data:', <?php echo json_encode($defaultPicker); ?>);
<?php endif; ?>
```

---

## 📝 SQL Queries untuk Maintenance

### **Cek Semua User TipeUser = 1**

```sql
SELECT
    tu.UserID,
    tu.TipeUser,
    tu.KodePicker,
    fp.NamaPicker,
    fp.Status as PickerStatus
FROM TipeUser tu
LEFT JOIN FilePicker fp ON tu.KodePicker = fp.KodePicker
WHERE tu.TipeUser = 1
ORDER BY tu.UserID;
```

### **Set Default Picker untuk User**

```sql
-- Set default picker untuk user tertentu
UPDATE TipeUser
SET KodePicker = 'PICKER001'
WHERE UserID = 'user123' AND TipeUser = 1;
```

### **Remove Default Picker**

```sql
-- Remove default picker
UPDATE TipeUser
SET KodePicker = NULL
WHERE UserID = 'user123';
```

---

## 🎓 Best Practices

### DO ✅

- Pastikan `KodePicker` di `TipeUser` valid dan exists di `FilePicker`
- Set `FilePicker.Status = 1` (aktif)
- Test dengan berbagai user role (TipeUser 0, 1, 2, 3)
- Update `KodePicker` jika picker tidak aktif lagi

### DON'T ❌

- Set `KodePicker` untuk user yang bukan TipeUser = 1
- Set `KodePicker` ke picker yang tidak aktif (Status = 0)
- Hardcode `KodePicker` di view (use database value)

---

## 📊 Impact Analysis

### **Benefits:**

- ✅ Faster data entry untuk marketing (1 field less to fill)
- ✅ Mengurangi error (marketing tidak bisa salah pilih picker)
- ✅ Consistent data (setiap marketing punya picker default)
- ✅ Audit trail (tahu siapa marketing yang input work order)

### **No Impact On:**

- ✅ User dengan TipeUser ≠ 1 (tidak terpengaruh)
- ✅ Edit Work Order (picker bisa diubah)
- ✅ Work Order yang sudah ada (tidak berubah)

---

## ✅ Verification Checklist

Setelah implementasi, verify:

- [ ] User TipeUser = 1 → Picker auto-fill
- [ ] User TipeUser ≠ 1 → Picker kosong
- [ ] User bisa mengganti default picker
- [ ] Work Order tersimpan dengan picker yang benar
- [ ] Console log menampilkan "Default Picker auto-filled"
- [ ] Error log menampilkan picker info (jika ada)
- [ ] No JavaScript errors di console
- [ ] Form validation tetap jalan (picker required)

---

**Status:** ✅ IMPLEMENTED  
**Version:** 1.0  
**Last Updated:** 18 Oktober 2025
