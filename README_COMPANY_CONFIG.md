# Kompaniya ma'lumotlari konfiguratsiyasi

## Tushuntirish

Bu fayl autsorser kompaniyasining ma'lumotlarini konfiguratsiya qilish uchun yaratilgan.

## .env faylga qo'shish kerak bo'lgan o'zgaruvchilar

```env
# Autsorser kompaniya ma'lumotlari
AUTSORSER_COMPANY_NAME="NISHON INVEST MCHJ"
AUTSORSER_ADDRESS="г.Гулистан ул.Бирлашган №5/13"
AUTSORSER_BANK_ACCOUNT="20 208 000 000 726 393 001"
AUTSORSER_BANK="ДАТ \"Асака Банк\""
AUTSORSER_MFO="00373"
AUTSORSER_INN="304 658 134"
AUTSORSER_PHONE="(99) 603 55 53"

# Buyurtmachi standart ma'lumotlari
BUYURTMACHI_MFO="00014"
BUYURTMACHI_TREASURY_ACCOUNT="23402000300100001010"
BUYURTMACHI_TREASURY_INN="201122919"
BUYURTMACHI_BANK="Markaziy bank XKKM"

# Hisob-faktura standart ma'lumotlari
INVOICE_DEFAULT_NUMBER="57"
INVOICE_DEFAULT_DATE="06 сентября 2021г."
VAT_PERCENTAGE=12
```

## Foydalanish

Controllerda kompaniya ma'lumotlarini olish uchun:

```php
// Autsorser ma'lumotlari
$autorser = config('company.autorser');

// Buyurtmachi ma'lumotlari
$buyurtmachi = config('company.buyurtmachi');

// Hisob-faktura ma'lumotlari
$invoice_number = config('company.invoice.default_number');
$vat_percentage = config('company.invoice.vat_percentage');
```

## Afzalliklari

1. **Markazlashtirilgan boshqaruv** - barcha kompaniya ma'lumotlari bir joyda
2. **Xavfsizlik** - ma'lumotlar kodda emas, konfiguratsiya faylida
3. **Moslashuvchanlik** - har xil muhit uchun turli ma'lumotlar
4. **Oson yangilash** - ma'lumotlarni o'zgartirish uchun kodni o'zgartirish shart emas

## Eslatma

`.env` faylni `.gitignore` ga qo'shishni unutmang, chunki u xavfsizlik ma'lumotlarini o'z ichiga oladi. 