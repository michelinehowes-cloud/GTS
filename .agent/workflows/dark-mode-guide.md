# 🌙 دليل الوضع الليلي (Dark Mode)

## ✅ التحديثات المطبقة

### 1. إضافة زر الوضع الليلي في الشريط العلوي
- تم نقل زر الوضع الليلي من الموقع الثابت إلى الشريط العلوي
- الزر موجود الآن بجانب أيقونة الإشعارات وقبل أيقونة المستخدم
- تصميم دائري أنيق مع تأثيرات تفاعلية

### 2. إصلاح المشاكل
- ✅ إصلاح عدم عمل الزر
- ✅ تحسين التكامل مع الـ navbar
- ✅ إضافة تأثيرات بصرية محسنة
- ✅ دعم كامل للوضع الليلي

---

## 🎨 المميزات

### التصميم
- زر دائري أنيق بحجم 40x40 بكسل
- أيقونة قمر 🌙 في الوضع الفاتح
- أيقونة شمس ☀️ في الوضع المظلم
- تأثيرات hover جذابة مع دوران خفيف
- انتقالات سلسة بين الأوضاع

### الوظائف
- **النقر**: تبديل بين الوضع الفاتح والمظلم
- **Keyboard Shortcut**: `Alt + D` أو `Cmd + D`
- **حفظ تلقائي**: يحفظ اختيارك في localStorage
- **دعم النظام**: يتبع إعدادات النظام تلقائياً

---

## 🚀 كيفية الاستخدام

### للمستخدمين:
1. **افتح أي صفحة** في التطبيق
2. **ابحث عن زر القمر** 🌙 في الشريط العلوي (بجانب الإشعارات)
3. **انقر على الزر** للتبديل بين الوضعين
4. **استمتع!** سيتم حفظ اختيارك تلقائياً

### اختصارات لوحة المفاتيح:
- **Windows/Linux**: `Alt + D`
- **Mac**: `Cmd + D`

---

## 🎯 الأنماط المطبقة

### الوضع الفاتح (Light Mode)
```css
- الخلفية: أبيض نقي
- النصوص: رمادي داكن
- الحدود: رمادي فاتح
- الأيقونة: قمر 🌙
```

### الوضع المظلم (Dark Mode)
```css
- الخلفية: أسود/رمادي داكن
- النصوص: أبيض/رمادي فاتح
- الحدود: ذهبي
- الأيقونة: شمس ☀️
```

---

## 🔧 للمطورين

### الملفات المعدلة:

1. **`resources/views/layouts/app.blade.php`**
   - إضافة زر Dark Mode في الـ navbar
   - الموقع: بعد قائمة الإشعارات

2. **`resources/js/dark-mode.js`**
   - تحديث دالة `insertToggleButton()`
   - إضافة دعم للزر الموجود في الـ DOM
   - منع إنشاء أزرار مكررة

3. **`resources/css/dark-mode-toggle.css`**
   - أنماط محسنة للزر
   - تأثيرات hover وactive
   - دعم responsive

4. **`resources/sass/app.scss`**
   - استيراد `dark-mode-toggle.css`

### كود الزر في Blade:

```blade
<!-- Dark Mode Toggle -->
<button id="darkModeToggle" class="btn btn-outline-secondary btn-sm me-3" 
        style="border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;"
        title="تبديل الوضع الليلي">
    <i class="fas fa-moon"></i>
</button>
```

### JavaScript API:

```javascript
// الحصول على الوضع الحالي
const currentTheme = window.darkModeManager.getCurrentTheme();

// تعيين وضع معين
window.darkModeManager.setTheme('dark'); // أو 'light'

// التبديل
window.darkModeManager.toggleTheme();

// إعادة التعيين لإعدادات النظام
window.darkModeManager.resetToSystem();
```

### الأحداث (Events):

```javascript
// عند تغيير الوضع
window.addEventListener('themeChanged', (e) => {
    console.log('Theme changed from', e.detail.oldTheme, 'to', e.detail.newTheme);
});

// عند بدء الانتقال
window.addEventListener('themeTransitionStart', (e) => {
    console.log('Transition started');
});

// عند انتهاء الانتقال
window.addEventListener('themeTransitionEnd', (e) => {
    console.log('Transition ended');
});
```

---

## 📱 التجاوب (Responsive)

### Desktop (> 768px)
- حجم الزر: 40x40 بكسل
- أيقونة: 1.1rem
- margin-right: 1rem

### Mobile (≤ 768px)
- حجم الزر: 36x36 بكسل
- أيقونة: 0.95rem
- margin-right: 0.5rem

---

## 🎨 التخصيص

### تغيير الألوان:

في `dark-mode-toggle.css`:

```css
/* للوضع الفاتح */
#darkModeToggle {
    border-color: #yourColor;
    color: #yourColor;
}

/* للوضع المظلم */
html[data-theme='dark'] #darkModeToggle {
    border-color: #yourDarkColor;
    color: #yourDarkColor;
}
```

### تغيير الأيقونات:

في `dark-mode.js` (دالة `updateButtonIcon`):

```javascript
const icons = {
    light: {
        icon: 'fa-moon',  // غير هذا
        label: 'التبديل إلى الوضع المظلم'
    },
    dark: {
        icon: 'fa-sun',   // غير هذا
        label: 'التبديل إلى الوضع الفاتح'
    }
};
```

---

## ⚙️ الإعدادات المتقدمة

### تعطيل الانتقالات:

```javascript
window.darkModeManager = new DarkModeManager({
    enableTransitions: false
});
```

### تعطيل اختصار لوحة المفاتيح:

```javascript
window.darkModeManager = new DarkModeManager({
    enableKeyboardShortcut: false
});
```

### تعطيل دعم إعدادات النظام:

```javascript
window.darkModeManager = new DarkModeManager({
    enableSystemPreference: false
});
```

---

## 🐛 استكشاف الأخطاء

### الزر لا يظهر؟
1. تأكد من تشغيل `npm run dev`
2. امسح الـ cache: `Ctrl + Shift + R`
3. تحقق من Console للأخطاء

### الزر لا يعمل؟
1. افتح Console (F12)
2. ابحث عن رسائل `[DarkModeManager]`
3. تأكد من عدم وجود أخطاء JavaScript

### الوضع لا يُحفظ؟
1. تحقق من localStorage في DevTools
2. تأكد من أن المتصفح يسمح بـ localStorage
3. جرب في وضع التصفح العادي (ليس Incognito)

---

## ✨ نصائح للاستخدام الأمثل

1. **استخدم الوضع المظلم ليلاً** لراحة العين
2. **استخدم الوضع الفاتح نهاراً** لوضوح أفضل
3. **جرب الاختصار `Alt + D`** للتبديل السريع
4. **اختيارك محفوظ** - لا حاجة لإعادة التعيين كل مرة

---

## 📞 الدعم

للمزيد من المعلومات أو الإبلاغ عن مشاكل:
- راجع `dark-mode.js` للتفاصيل التقنية
- راجع `dark-mode-toggle.css` للأنماط
- راجع `app.blade.php` لموقع الزر

---

**تم التطبيق بنجاح! 🌙✨**
