# Powergirls.az Idareetme Te limati (AZ)

Bu fayl saytin quraşdirilmasi, admin panelinin idare olunmasi, mehsul/kateqoriya idareetmesi, kampaniya ve hediyye cekilisi parametrləri ve diger vacib melumatlari izah edir.

## 1) Qurasdirma (XAMPP)

1. XAMPP Control Panel-de `Apache` ve `MySQL` basladin.
2. Bu proyekt `d:\xampp\htdocs\powergirls` qovlugunda olmalidir.
3. Brauzerde `http://localhost/powergirls` acin.

## 2) Verilenler Bazasi (MySQL)

### Konfiqurasiya
- Fayl: `includes/config.php`
- DB melumatlari:
  - `DB_HOST`: localhost
  - `DB_NAME`: powergirls
  - `DB_USER`: root
  - `DB_PASS`: (bos ola biler)

### DB yaratmaq ve dump import
PowerShell (proyekt qovlugunda):
```
D:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS powergirls CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Get-Content -Path "d:\xampp\htdocs\powergirls\sql\dump.sql" | D:\xampp\mysql\bin\mysql.exe -u root powergirls
```

### DB adini deyismek
`includes/config.php` icinde `DB_NAME` deyerini yeni baza adina uygun yazin.

## 3) Admin Panel

### Giris
URL: `http://localhost/powergirls/admin/login.php`

Default admin:
- Email: `powergirls@admin.local`
- Sifre: `powergirls@12345`

### Sifre yenilemek (DB ile)
MySQL:
```
UPDATE powergirls.admins
SET email='powergirls@admin.local',
    password_hash='(yeni hash)'
WHERE id=1;
```
Parol hash yaratmaq ucun:
```
D:\xampp\php\php.exe -r "echo password_hash('YeniSifre', PASSWORD_DEFAULT);"
```

## 4) Ayarlar (Settings)

Admin panel -> Settings bolmesinden:
- Instagram URL
- WhatsApp nomre
- Sayt basligi
- Topbar metnleri
- Hero metinleri
- Gift, Campaign, Giveaway metinleri
- OG image

Bu melumatlar DB `settings` cedvelinde saxlanir.

## 5) Kateqoriyalar

Admin panel -> Categories:
- Kateqoriya elave etme
- Adlarin (AZ/RU/EN) deyisdirilmesi
- Siralama
- Silmek (eger o kateqoriyada mehsul yoxdursa)

Slug eyni olmalidir (unikal).

## 6) Mehsullar

Admin panel -> Products:
- Mehsul elave etme
- Edit, silme
- Qiymet, endirim faizi
- Status (active)
- Badge: `is_new`, `is_bestseller`

Mehsul resimleri `uploads/products/` qovluguna yuklenir.

## 7) Kampaniya ve Hediyye cekilisi

### Kampaniya
Admin panel -> Campaigns:
- Status `ON/OFF`
- Title, text, image
- Bitme tarixi

### Hediyye cekilisi
Admin panel -> Giveaway:
- Status `ON/OFF`
- Terms ve winner text

Esas sehifede bu bloklar `settings` deyerlerine gore gosterilir.

## 8) Sifarisler

Admin panel -> Orders:
- Yeni sifarisler
- Status deyisdirme
- Detallar (ad, telefon, seher, unvan)

## 9) Sevdiklerim (Favorites)

Istifadeci mehsullari `Sevdiklerim`-e elave eder.
Sevdiklerim sehifesinde:
- WhatsApp ile gonder
- Instagram ile gonder (mesaj kopyalanir, Instagram acilir)

## 10) Axtaris

`shop.php` sehifesinde axtaris inputu var.
Axtaris title ve description sahalarinda isleyir.

## 11) Fayl Strukturu (qisa)

- `index.php` – ana sehife
- `shop.php` – mehsul siyahisi + filter + axtaris
- `product.php` – mehsul detali
- `admin/` – admin panel
- `includes/` – config, db, helperler
- `assets/` – CSS/JS/images
- `uploads/` – yuklenen sekiller
- `sql/dump.sql` – DB strukturu ve demo data

---
Suallar ucun: admin panelden Settings bolmesine bax ve DB `settings` cedvelini yoxla SAOLLLLLLLLLL.
