# CSS Duplication Audit Report - CTAutoFashion

**Date:** October 17, 2025  
**Auditor:** AI Assistant  
**Status:** Analysis Complete

---

## 📊 Executive Summary

| Metric                      | Count                  | Action Needed                   |
| --------------------------- | ---------------------- | ------------------------------- |
| **Total CSS Lines**         | 1,914                  | ✅                              |
| **Duplicate Selectors**     | 19 found               | ⚠️ Consolidate                  |
| **Repeated font-size**      | 8 occurrences (0.9rem) | ⚠️ Create utility               |
| **Repeated padding**        | 7 occurrences (0.5rem) | ⚠️ Create utility               |
| **Repeated transform**      | 6 occurrences (scale)  | ⚠️ Standardize                  |
| **Modal width duplication** | 5 occurrences          | ✅ Already has .modal-lg-custom |

**Duplication Rate:** ~5-8%  
**Potential Savings:** ~50-100 lines

---

## 🔍 Detailed Findings

### 1. **Font-Size Duplication (8 occurrences)**

#### Found: `font-size: 0.9rem`

```
Line 60:   .login-header p
Line 89:   (unknown selector)
Line 1332: @media responsive
Line 1448: (table related)
Line 1510: .main-table (vertical-align)
Line 1524: .main-table (data-harga)
Line 1529: .main-table tbody td.data-stok .badge
Line 1759: #detailModal .table td, #detailModal .table th
```

**Recommendation:** Create utility class

```css
.text-secondary {
  font-size: 0.9rem;
}
```

---

### 2. **Padding Duplication (7 occurrences)**

#### Found: `padding: 0.5rem`

```
Line 1505: .main-table tbody td
Line 1758: #detailModal .table td, .table th
Plus 5 more variations
```

**Recommendation:** Create utility classes

```css
.p-sm {
  padding: 0.5rem;
}
.p-md {
  padding: 1rem;
}
```

---

### 3. **Transform Scale Duplication (6 occurrences)**

```
Line 400:  transform: scale(1.05);   /* Card hover */
Line 616:  transform: scale(1.05);   /* Repeat */
Line 898:  transform: scale(1.05);   /* Repeat */
Line 1470: transform: scale(1.1);    /* Sortable hover */
Line 1592: transform: scale(1.1);    /* Repeat */
Line 1780: transform: scale(1.2);    /* Modal close button */
```

**Recommendation:** Standardize to 2-3 values

```css
.hover-scale-sm {
  transform: scale(1.05);
} /* Cards, buttons */
.hover-scale-md {
  transform: scale(1.1);
} /* Interactive elements */
.hover-scale-lg {
  transform: scale(1.2);
} /* Modal close */
```

---

### 4. **Modal Dialog Width (5 occurrences)**

#### ✅ **ALREADY CONSOLIDATED** (Good!)

```css
Line 1629: #customerModal - max-width: 90%; width: 90%;
Line 1637: #customerModal (tablet) - max-width: 85%; width: 85%;
Line 1653: #customerModal.modal-lg - max-width: 90%; width: 90%;
Line 1786: #detailModal (tablet) - max-width: 90%; width: 90%;
Line 1910: .modal-lg-custom - max-width: 90%; width: 90%;
```

**Status:** ✅ `.modal-lg-custom` utility already exists!  
**Action:** Replace #customerModal and #detailModal with `.modal-lg-custom`

---

### 5. **Modal Close Button (Already Consolidated)** ✅

```css
#detailModal .btn-close,
#prosesModal .btn-close,
#selesaiModal .btn-close,
#batalModal .btn-close {
  font-size: 1rem;
  opacity: 1;
}
```

**Status:** ✅ GOOD! Already using multi-selector

---

### 6. **Media Query Duplication (3 tablet queries)**

```
@media (min-width: 768px) and (max-width: 1024px)
```

Found 3 separate declarations:

- Line ~1629: #customerModal responsive
- Line ~1786: #detailModal responsive
- Line ~1799: .main-table responsive

**Recommendation:** Consolidate into single media query block

---

### 7. **Duplicate Class Selectors**

#### Multiple declarations found:

```
.dropdown-item      - 2 declarations
.detail-table       - 2+ declarations
.alert             - Multiple contexts
.btn-login         - Multiple contexts
```

**Status:** Some are intentional (different contexts), others can be consolidated

---

## 🎯 Consolidation Opportunities

### Priority 1: Create Missing Utility Classes

```css
/* ========================================
   UTILITY CLASSES - Extended
   ======================================== */

/* Font Sizes */
.text-secondary {
  font-size: 0.9rem;
}
.text-small {
  font-size: 0.85rem;
}
.text-xs {
  font-size: 0.75rem;
}

/* Padding */
.p-xs {
  padding: 0.25rem;
}
.p-sm {
  padding: 0.5rem;
}
.p-md {
  padding: 1rem;
}

/* Transform Scale */
.hover-scale-sm {
  transform: scale(1.05);
}
.hover-scale-md {
  transform: scale(1.1);
}
.hover-scale-lg {
  transform: scale(1.2);
}

/* Transitions */
.transition-all {
  transition: all 0.2s ease;
}
.transition-fast {
  transition: all 0.15s ease;
}
```

**Estimated Savings:** 20-30 lines

---

### Priority 2: Consolidate Modal Styles

#### Before:

```css
#customerModal .modal-dialog {
  max-width: 90% !important;
  width: 90% !important;
}

#detailModal .modal-dialog {
  max-width: 90% !important;
  width: 90% !important;
}

.modal-lg-custom {
  max-width: 90%;
  width: 90%;
}
```

#### After:

```css
/* Apply .modal-lg-custom class in HTML */
.modal-lg-custom,
#customerModal .modal-dialog,
#detailModal .modal-dialog {
  max-width: 90%;
  width: 90%;
}
```

**Estimated Savings:** 10-15 lines

---

### Priority 3: Consolidate Media Queries

#### Before: 3 separate blocks

```css
@media (min-width: 768px) and (max-width: 1024px) {
  #customerModal {
    ...;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  #detailModal {
    ...;
  }
}

@media (min-width: 768px) and (max-width: 1024px) {
  .main-table {
    ...;
  }
}
```

#### After: 1 consolidated block

```css
@media (min-width: 768px) and (max-width: 1024px) {
  #customerModal {
    ...;
  }
  #detailModal {
    ...;
  }
  .main-table {
    ...;
  }
}
```

**Estimated Savings:** 6-10 lines (from repeated @media declarations)

---

### Priority 4: Standardize Transform Values

Replace inconsistent scale values:

```css
/* Instead of: scale(1.05), scale(1.1), scale(1.2) scattered */
/* Use consistent classes: */

.card:hover {
  transform: scale(1.05);
}
.btn:hover {
  transform: scale(1.05);
}
.sortable:hover {
  transform: scale(1.1);
}
.btn-close:hover {
  transform: scale(1.2);
}
```

Add to utility classes for reuse.

---

## 📈 Impact Analysis

### Before Consolidation:

```
- Font-size 0.9rem: 8 declarations
- Padding 0.5rem: 7 declarations
- Transform scale: 6 declarations (3 different values)
- Modal width: 5 separate declarations
- Media queries: 3 separate tablet blocks
- Total duplicated CSS: ~80-100 lines
```

### After Consolidation:

```
- Font-size: Use .text-secondary utility
- Padding: Use .p-sm utility
- Transform: Use .hover-scale-* utilities
- Modal width: Use .modal-lg-custom consistently
- Media queries: 1 consolidated tablet block
- Total saved: ~50-70 lines (40-50% reduction)
```

---

## ✅ Already Well-Organized

### Good Practices Found:

1. ✅ **Modal close buttons** - Already using multi-selector

   ```css
   #detailModal .btn-close,
   #prosesModal .btn-close,
   #selesaiModal .btn-close,
   #batalModal .btn-close {
     ...;
   }
   ```

2. ✅ **Utility classes exist** - cursor, display, vertical-align

3. ✅ **Responsive organized** - Clear breakpoints

4. ✅ **No `<style>` blocks** - All in main.css

---

## 🚀 Implementation Priority

### Phase 1: Quick Wins (Estimated 1 hour)

- [ ] Add missing utility classes (.text-secondary, .p-sm, etc.)
- [ ] Consolidate modal dialog widths
- [ ] Standardize transform scale values

### Phase 2: Structural (Estimated 2 hours)

- [ ] Merge duplicate media query blocks
- [ ] Replace repeated inline values with utilities
- [ ] Update HTML to use new utility classes

### Phase 3: Testing (Estimated 1 hour)

- [ ] Visual regression testing
- [ ] Responsive breakpoint testing
- [ ] Browser compatibility check

---

## 📊 Metrics

| Metric              | Current   | Target    | Improvement |
| ------------------- | --------- | --------- | ----------- |
| **Total Lines**     | 1,914     | ~1,850    | -64 lines   |
| **Duplication**     | ~80 lines | ~10 lines | -87.5%      |
| **Utility Classes** | 4         | 15+       | +275%       |
| **Reusability**     | Medium    | High      | ✅          |
| **Maintainability** | Good      | Excellent | ✅          |

---

## 🎯 Recommendations

### Immediate Actions:

1. ✅ Create extended utility classes
2. ✅ Consolidate modal widths
3. ✅ Merge media queries

### Long-term Strategy:

1. Use CSS variables for common values
2. Create design system with tokens
3. Document utility class usage
4. Establish naming conventions

---

## 📝 Conclusion

**Current State:** ✅ Generally well-organized, minimal duplication  
**Duplication Found:** ~5-8% (relatively low)  
**Action Required:** Minor consolidation for better maintainability  
**Priority:** Medium (not urgent, but beneficial)

**Overall Grade:** B+ (Would be A with consolidation)

---

**Next Step:** Implement Phase 1 quick wins? (Y/N)
