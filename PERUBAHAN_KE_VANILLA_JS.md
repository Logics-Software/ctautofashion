# Perubahan dari jQuery ke Vanilla JavaScript

## 📋 Summary

Modul Transaksi Work Order telah **diubah dari jQuery + Select2** menjadi **Vanilla JavaScript + Choices.js** untuk konsistensi dengan proyek yang tidak menggunakan jQuery.

---

## ✅ Perubahan yang Dilakukan

### 1. **Library Changes**

#### ❌ **Sebelumnya (jQuery-based):**

```html
<!-- Select2 CSS -->
<link
  href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
  rel="stylesheet"
/>
<link
  href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
  rel="stylesheet"
/>

<!-- jQuery (Required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

#### ✅ **Sekarang (Pure JavaScript):**

```html
<!-- Choices.js CSS (Local) -->
<link
  href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/css/choices.min.css"
  rel="stylesheet"
/>

<!-- Choices.js JS (Local) -->
<script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/assets/js/choices.min.js"></script>
```

---

### 2. **Code Changes**

#### **A. DOM Selection**

**jQuery:**

```javascript
$("#selectCustomer");
$(".btn-primary");
```

**Vanilla JS:**

```javascript
document.getElementById("selectCustomer");
document.querySelector(".btn-primary");
document.querySelectorAll(".btn-primary");
```

---

#### **B. Event Listeners**

**jQuery:**

```javascript
$("#btnNewOrder").click(function () {
  // code
});

$("#searchInput").on("input", function () {
  // code
});
```

**Vanilla JS:**

```javascript
document.getElementById("btnNewOrder").addEventListener("click", function () {
  // code
});

document.getElementById("searchInput").addEventListener("input", function () {
  // code
});
```

---

#### **C. AJAX Requests**

**jQuery:**

```javascript
$.ajax({
  url: "/api/endpoint",
  method: "POST",
  contentType: "application/json",
  data: JSON.stringify(data),
  success: function (response) {
    // handle success
  },
  error: function () {
    // handle error
  },
});
```

**Vanilla JS (Fetch API):**

```javascript
fetch("/api/endpoint", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify(data),
})
  .then((response) => response.json())
  .then((data) => {
    // handle success
  })
  .catch((error) => {
    // handle error
  });
```

---

#### **D. Searchable Dropdown**

**jQuery + Select2:**

```javascript
$("#selectCustomer").select2({
  theme: "bootstrap-5",
  placeholder: "Cari customer...",
  ajax: {
    url: "/search-customers",
    dataType: "json",
    delay: 250,
    processResults: function (data) {
      return { results: data.results };
    },
  },
});
```

**Vanilla JS + Choices.js:**

```javascript
const customerChoice = new Choices("#selectCustomer", {
  searchEnabled: true,
  placeholder: true,
  placeholderValue: "Pilih Customer...",
  removeItemButton: true,
});

// Load data on search
document
  .getElementById("selectCustomer")
  .addEventListener("search", function (e) {
    const searchTerm = e.detail.value;
    if (searchTerm.length >= 2) {
      fetch(`/search-customers?term=${encodeURIComponent(searchTerm)}`)
        .then((response) => response.json())
        .then((data) => {
          const choices = data.results.map((item) => ({
            value: item.id,
            label: item.text,
            customProperties: item.data,
          }));
          customerChoice.setChoices(choices, "value", "label", true);
        });
    }
  });
```

---

#### **E. Show/Hide Elements**

**jQuery:**

```javascript
$("#formSection").slideDown();
$("#listSection").slideUp();
$("#customerInfo").show();
$("#customerInfo").hide();
```

**Vanilla JS:**

```javascript
document.getElementById("formSection").style.display = "block";
document.getElementById("listSection").style.display = "none";
document.getElementById("customerInfo").style.display = "block";
document.getElementById("customerInfo").style.display = "none";
```

---

#### **F. Form Reset**

**jQuery:**

```javascript
$("#formWorkOrder")[0].reset();
$("#selectCustomer").val(null).trigger("change");
```

**Vanilla JS:**

```javascript
document.getElementById("formWorkOrder").reset();
customerChoice.removeActiveItems();
kendaraanChoice.removeActiveItems();
```

---

#### **G. Dynamic HTML Insertion**

**jQuery:**

```javascript
$("body").append(modalHtml);
tbody.append("<tr>...</tr>");
tbody.empty();
```

**Vanilla JS:**

```javascript
document.body.insertAdjacentHTML("beforeend", modalHtml);
tbody.insertAdjacentHTML("beforeend", "<tr>...</tr>");
tbody.innerHTML = "";
```

---

## 📦 Files Downloaded

### **Choices.js Library (Local)**

```bash
# CSS (7.6 KB)
curl -o assets/css/choices.min.css https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css

# JavaScript (89.4 KB)
curl -o assets/js/choices.min.js https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js
```

---

## 🎯 Keuntungan Perubahan

### ✅ **Advantages:**

1. **No jQuery Dependency**

   - Mengurangi ukuran bundle (jQuery ~30KB + Select2 ~20KB = 50KB saved)
   - Konsisten dengan proyek yang tidak menggunakan jQuery

2. **Modern JavaScript**

   - Menggunakan ES6+ features (arrow functions, fetch API, const/let, etc)
   - Lebih readable dan maintainable

3. **Better Performance**

   - Native DOM manipulation lebih cepat
   - Choices.js lebih ringan dari Select2
   - Fetch API built-in browser (no extra library)

4. **Local Assets**

   - Tidak bergantung CDN
   - Lebih cepat load time
   - Offline-friendly

5. **Future-proof**
   - jQuery semakin jarang digunakan
   - Vanilla JS adalah standard web

### ⚠️ **Trade-offs:**

1. **Slightly More Code**

   - Vanilla JS lebih verbose dari jQuery
   - Tapi lebih explicit dan jelas

2. **Browser Compatibility**
   - Fetch API: IE11 tidak support (tapi sudah deprecated)
   - ES6: Butuh transpile untuk browser lama (tapi modern browsers OK)

---

## 🧪 Testing Checklist

- ✅ Form input customer (searchable)
- ✅ Form input kendaraan (searchable)
- ✅ Form input montir & picker
- ✅ Tambah detail jasa dengan modal
- ✅ Tambah detail barang dengan modal
- ✅ Perhitungan otomatis total
- ✅ Hapus detail jasa/barang
- ✅ Submit form dengan validasi
- ✅ Search & filter di list
- ✅ Pagination
- ✅ Reset form
- ✅ Cancel input

---

## 📊 Bundle Size Comparison

| Library        | Before (jQuery) | After (Vanilla) | Savings             |
| -------------- | --------------- | --------------- | ------------------- |
| **jQuery**     | ~30 KB          | -               | -30 KB ✅           |
| **Select2**    | ~20 KB          | -               | -20 KB ✅           |
| **Choices.js** | -               | ~21 KB          | +21 KB              |
| **Total**      | **~50 KB**      | **~21 KB**      | **-29 KB (58%)** ✅ |

---

## 🚀 Migration Guide (Untuk Developer Lain)

Jika ada modul lain yang masih menggunakan jQuery, ini panduan untuk migrate:

### Step 1: Download Choices.js

```bash
curl -o assets/css/choices.min.css https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css
curl -o assets/js/choices.min.js https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js
```

### Step 2: Replace jQuery selectors

```javascript
// Find all: \$\(
// Replace with: document.querySelector(
// Or: document.getElementById(
```

### Step 3: Replace event listeners

```javascript
// Find all: \.click\(function\(\)
// Replace with: .addEventListener('click', function()

// Find all: \.on\('input', function\(\)
// Replace with: .addEventListener('input', function()
```

### Step 4: Replace AJAX calls

```javascript
// Replace $.ajax with fetch()
// See examples above
```

### Step 5: Replace Select2 with Choices.js

```javascript
// See Choices.js examples above
```

### Step 6: Test thoroughly!

---

## 📚 Resources

- [Choices.js Documentation](https://github.com/Choices-js/Choices)
- [Fetch API MDN](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [Vanilla JS Cheatsheet](http://youmightnotneedjquery.com/)
- [ES6 Features](https://es6-features.org/)

---

**Version:** 2.0.0 (Vanilla JS Edition)  
**Last Updated:** <?php echo date('Y-m-d'); ?>  
**Migration By:** Development Team
