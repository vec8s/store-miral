docker compose up -d
docker compose down
docker compose exec app php artisan 

docker compose exec app php artisan serve --host=0.0.0.0 --port=8000 #تشغيل سيرفر لارافيل مع تحديد الـ host

"-----------------------------------------------------------"
للتحقق من أن النماذج والجداول والعلاقات والـ CRUD تعمل بشكل صحيح:
أ. التحقق عبر Laravel Tinker

افتح التيرمينال داخل حاوية Docker:
PowerShell
docker compose exec app php artisan tinker

# 1. التاكد من الملفات التي تم تعديلها أو إضافتها
git status

# 2. إضافة جميع التعديلات والملفات الجديدة لمرحلة التجهيز
git add .

# 3. حفظ التغييرات مع كتابة رسالة توضيحية للمرحلة
git commit -m "feat: complete migrations, models, seeders and verify database fresh setup"

الحالة الأولى: ألغيت تعديلات ملف معين قبل أن تعمل git add

إذا قمت بتعديل ملف بالخطأ وأردت إعادته لآخر وضع كان عليه:
PowerShell

git checkout -- path/to/file.php
# مثال: git checkout -- database/seeders/ShippingMethodSeeder.php

⏪ الحالة الثانية: أردت إلغاء كل التعديلات غير المكتملة والعودة لآخر Commit

إذا قمت بكتابة كود جديد وتلخبطت الأمور وأردت تصفير التغييرات والعودة لنقطة الحفظ الأخيرة تماماً:
PowerShell

git reset --hard HEAD

⏪ الحالة الثالثة: أردت رؤية سجل التغييرات ونقاط الحفظ السابقة
PowerShell

git log --oneline

💡 نصيحة للعمل اليومي (Best Practice)

كلما أكملت كتابة Action جديد وتأكدت من عمله (مثلاً بعد الانتهاء من CalculateShippingCostAction غداً)، قم بتشغيل:
PowerShell

git add .
git commit -m "feat: add CalculateShippingCostAction for order shipping estimation"