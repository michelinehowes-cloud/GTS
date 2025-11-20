@echo off
echo اختبار اتصال قاعدة البيانات...
echo.

REM محاولة تشغيل PHP من المسارات الشائعة
IF EXIST "C:\php\php.exe" (
    echo تم العثور على PHP في C:\php\php.exe
    "C:\php\php.exe" test_database.php
    goto :end
)

IF EXIST "C:\xampp\php\php.exe" (
    echo تم العثور على PHP في C:\xampp\php\php.exe
    "C:\xampp\php\php.exe" test_database.php
    goto :end
)

IF EXIST "C:\wamp\bin\php\php.exe" (
    echo تم العثور على PHP في C:\wamp\bin\php\php.exe
    "C:\wamp\bin\php\php.exe" test_database.php
    goto :end
)

echo لم يتم العثور على PHP!
echo يرجى تثبيت PHP أو إضافته إلى متغيرات البيئة PATH
echo.

:end
pause
