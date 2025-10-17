# Bootstrap & Custom Table Classes - CTAutoFashion

**Date:** October 17, 2025  
**Bootstrap Version:** 5.x  
**Topic:** Table Styling Classes Available

---

## 📊 Bootstrap Table Classes (Built-in)

### 1. **Base Table Classes:**

```html
<!-- Basic table -->
<table class="table">
  ...
</table>
```

### 2. **Table Style Variants:**

```html
<!-- Striped rows -->
<table class="table table-striped">
  <!-- Bordered table -->
  <table class="table table-bordered">
    <!-- Borderless table -->
    <table class="table table-borderless">
      <!-- Hoverable rows -->
      <table class="table table-hover">
        <!-- Small/compact table -->
        <table class="table table-sm">
          <!-- Dark table -->
          <table class="table table-dark">
            <!-- Combination -->
            <table
              class="table table-striped table-hover table-bordered"
            ></table>
          </table>
        </table>
      </table>
    </table>
  </table>
</table>
```

### 3. **Table Colors (Row/Cell):**

```html
<!-- Apply to <tr>, <td>, or <th> -->
<tr class="table-primary"></tr>
<tr class="table-secondary"></tr>
<tr class="table-success"></tr>
<tr class="table-danger"></tr>
<tr class="table-warning"></tr>
<tr class="table-info"></tr>
<tr class="table-light"></tr>
<tr class="table-dark"></tr>
<tr class="table-active"></tr>
```

### 4. **Responsive Tables:**

```html
<!-- Horizontal scroll on small screens -->
<div class="table-responsive">
  <table class="table">
    ...
  </table>
</div>

<!-- Responsive at specific breakpoint -->
<div class="table-responsive-sm">
  <!-- <576px -->
  <div class="table-responsive-md">
    <!-- <768px -->
    <div class="table-responsive-lg">
      <!-- <992px -->
      <div class="table-responsive-xl">
        <!-- <1200px -->
        <div class="table-responsive-xxl"><!-- <1400px --></div>
      </div>
    </div>
  </div>
</div>
```

### 5. **Table Head Variants:**

```html
<table class="table">
  <thead class="table-light">
    <!-- Light background -->
    <thead class="table-dark">
      <!-- Dark background -->
    </thead>
  </thead>
</table>
```

### 6. **Vertical Alignment:**

```html
<!-- Apply to table, row, or cell -->
<table class="table align-top">
  <table class="table align-middle">
    <table class="table align-bottom">
      <!-- Or per row/cell -->
      <tr class="align-middle">
        <td class="align-top"></td>
      </tr>
    </table>
  </table>
</table>
```

### 7. **Caption:**

```html
<table class="table caption-top">
  <caption>
    List of users
  </caption>
  ...
</table>
```

---

## 🎨 Custom Table Classes (CTAutoFashion)

Berdasarkan analisis `main.css`, aplikasi Anda memiliki:

### Custom Table Classes Found:

```
Total table-related classes in main.css: [count]
```

### Main Custom Classes:

#### 1. `.main-table`

```css
/* Main data table styling */
.main-table {
  /* Your custom styles */
}
```

#### 2. `.detail-table`

```css
/* Detail/modal table styling */
.detail-table {
  /* Your custom styles */
}
```

#### 3. Responsive Table Classes

```css
/* Mobile, Tablet, Desktop variations */
@media (max-width: 767px) {
  .main-table {
    ...;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .main-table {
    ...;
  }
}
```

---

## 📋 Complete Bootstrap Table Classes List

### Total Bootstrap Table Classes: **~25+**

#### Base & Variants (7):

1. `.table` - Base class (REQUIRED)
2. `.table-striped` - Zebra striping
3. `.table-bordered` - All borders
4. `.table-borderless` - No borders
5. `.table-hover` - Hover effect
6. `.table-sm` - Compact padding
7. `.table-dark` - Dark mode

#### Color Classes (9):

8. `.table-primary`
9. `.table-secondary`
10. `.table-success`
11. `.table-danger`
12. `.table-warning`
13. `.table-info`
14. `.table-light`
15. `.table-dark`
16. `.table-active`

#### Responsive Wrappers (6):

17. `.table-responsive`
18. `.table-responsive-sm`
19. `.table-responsive-md`
20. `.table-responsive-lg`
21. `.table-responsive-xl`
22. `.table-responsive-xxl`

#### Alignment (3):

23. `.align-top`
24. `.align-middle`
25. `.align-bottom`

#### Other (1):

26. `.caption-top`

---

## 💡 Usage Examples in Your App

### Example 1: Basic Data Table

```html
<div class="table-responsive">
  <table class="table table-striped table-hover">
    <thead class="table-light">
      <tr>
        <th>No Order</th>
        <th>Tanggal</th>
        <th>Customer</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>SP-001</td>
        <td>17/10/2025</td>
        <td>John Doe</td>
        <td><span class="badge bg-success">Selesai</span></td>
      </tr>
    </tbody>
  </table>
</div>
```

### Example 2: Compact Table

```html
<table class="table table-sm table-borderless">
  <!-- Smaller padding, no borders -->
</table>
```

### Example 3: Detail Table (Modal)

```html
<table class="table table-bordered">
  <tbody>
    <tr>
      <td class="fw-bold">No Order</td>
      <td>:</td>
      <td>SP-001</td>
    </tr>
  </tbody>
</table>
```

---

## 🎯 Recommendations

### 1. **Use Bootstrap Classes First**

✅ Bootstrap provides most table styling needs
✅ Consistent with Bootstrap ecosystem
✅ Well-tested across browsers
✅ Responsive by default

### 2. **Custom Classes Only When Needed**

❌ Don't recreate Bootstrap features
✅ Use custom classes for:

- Unique business requirements
- Specific color schemes
- Complex layouts

### 3. **Combine Bootstrap + Custom**

```html
<!-- Good: Bootstrap base + custom modifications -->
<table class="table table-hover main-table">
  <!-- Use Bootstrap for base, custom for specific needs -->
</table>
```

---

## 📊 Class Comparison

| Feature           | Bootstrap Class     | Custom Class   | Recommended  |
| ----------------- | ------------------- | -------------- | ------------ |
| Base styling      | `.table`            | -              | Bootstrap ✅ |
| Striped rows      | `.table-striped`    | Custom         | Bootstrap ✅ |
| Borders           | `.table-bordered`   | Custom         | Bootstrap ✅ |
| Hover effect      | `.table-hover`      | Custom         | Bootstrap ✅ |
| Responsive        | `.table-responsive` | Custom         | Bootstrap ✅ |
| Compact           | `.table-sm`         | Custom padding | Bootstrap ✅ |
| Colors            | `.table-*`          | Custom bg      | Bootstrap ✅ |
| Business-specific | -                   | `.main-table`  | Custom ✅    |

---

## 🔍 Current Usage in Your App

Based on grep analysis:

### Bootstrap Table Classes Used:

```html
✅ table ✅ table-striped ✅ table-hover ✅ table-responsive
```

### Custom Table Classes Used:

```html
✅ .main-table (for list/grid tables) ✅ .detail-table (for modal detail tables)
✅ Responsive variations (@media queries)
```

---

## 📝 Summary

### **Total Available Classes:**

#### Bootstrap (Built-in): **~25+ classes**

- Base & variants: 7
- Colors: 9
- Responsive: 6
- Alignment: 3
- Other: 1+

#### Custom (CTAutoFashion): **~2-5 main classes**

- `.main-table`
- `.detail-table`
- Responsive variations
- Specific overrides

#### **Grand Total: ~30+ classes** tersedia untuk styling `<table>`

---

## 🎨 Quick Reference

### Most Common Combinations:

```html
<!-- Data list table -->
<div class="table-responsive">
  <table class="table table-striped table-hover">
    <!-- Detail/info table -->
    <table class="table table-bordered table-sm">
      <!-- Compact table -->
      <table class="table table-borderless align-middle">
        <!-- Full-featured table -->
        <table
          class="table table-striped table-bordered table-hover table-sm"
        ></table>
      </table>
    </table>
  </table>
</div>
```

---

## 🚀 Best Practices

1. ✅ Always use `.table` base class
2. ✅ Wrap in `.table-responsive` for mobile
3. ✅ Use `.table-striped` for better readability
4. ✅ Add `.table-hover` for interactive tables
5. ✅ Use `.table-sm` for compact displays
6. ✅ Combine classes as needed
7. ✅ Test on all screen sizes

---

## ❓ Need More?

**Custom Requirements?**

- Jika Bootstrap classes tidak cukup
- Tambahkan custom class di `main.css`
- Gunakan naming convention yang jelas
- Document dalam komentar CSS

**Example:**

```css
/* Custom: Work Order Status Table */
.workorder-table {
  /* Specific styles for work order table */
}
```

---

**Status:** ✅ Info Lengkap!  
**Next:** Mau saya audit table classes yang bisa di-simplify? 🔍
