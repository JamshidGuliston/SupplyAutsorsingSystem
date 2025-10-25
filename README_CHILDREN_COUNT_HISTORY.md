# Bolalar soni o'zgartirish tarixi tizimi

## Tavsif
Bu tizim `Nextday_namber` jadvalida har bir bog'cha uchun bolalar sonini o'zgartirish tarixini saqlaydi va har kuni ma'lumotlarni qayta shakllantiradi.

## Qo'shilgan funksiyalar

### 1. Yangi jadval: `children_count_history`
- `kingar_name_id` - Bog'cha ID
- `king_age_name_id` - Yosh guruhi ID  
- `old_children_count` - Eski bolalar soni
- `new_children_count` - Yangi bolalar soni
- `changed_by` - Kim o'zgartirgan (user ID)
- `changed_at` - Qachon o'zgartirilgan
- `change_reason` - O'zgartirish sababi

### 2. Yangi Model: `ChildrenCountHistory`
- Bog'cha, yosh guruhi va foydalanuvchi bilan bog'lanish
- Tarixni olish funksiyalari
- Oxirgi o'zgartirishni olish

### 3. ChefController yangi funksiyalar:
- `childrenCountHistory()` - Tarixni ko'rsatish
- `updateChildrenCount()` - Qo'lda o'zgartirish (admin uchun)
- `clearNextdayNumbers()` - Jadvalni tozalash
- `sendnumbers()` - Yangilangan (tarix saqlash bilan)

### 4. Yangi sahifa: `chef/children_count_history`
- Bolalar soni o'zgartirish tarixini ko'rsatish
- O'zgarishlar sonini ko'rsatish (o'sish/kamayish)
- Kim va qachon o'zgartirganini ko'rsatish

### 5. Console Command: `nextday:clear`
- Har kuni ertalab soat 6:00 da ishga tushadi
- Nextday_namber jadvalini tozalaydi
- Log yozadi

## O'rnatish

### 1. Migration ishga tushirish
```bash
php artisan migrate
```

**Eslatma:** Agar index nomi juda uzun xatolik chiqsa, avval mavjud jadvalni o'chiring:
```sql
DROP TABLE IF EXISTS children_count_history;
```
Keyin migration ni qayta ishga tushiring.

### 2. Console command ni test qilish
```bash
php artisan nextday:clear
```

### 3. Scheduler ni ishga tushirish
Laravel scheduler ishga tushirish uchun server da cron job qo'shish kerak:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Foydalanish

### 1. Oshpazlar uchun
- Har kuni ertalab bolalar sonini yuborish
- Tarix sahifasida o'zgarishlarni ko'rish
- Chef home sahifasida "Bolalar soni tarixi" tugmasi

### 2. Admin uchun
- Qo'lda bolalar sonini o'zgartirish
- Barcha o'zgarishlarni kuzatish
- Jadvalni qo'lda tozalash

## Xususiyatlar

### Avtomatik tozalash
- Har kuni ertalab soat 6:00 da Nextday_namber jadvali tozalanadi
- Tarix saqlanib qoladi
- Yangi kun uchun yangi ma'lumotlar kiritiladi

### Tarix saqlash
- Har bir o'zgartirish saqlanadi
- Kim, qachon, nima uchun o'zgartirgani yoziladi
- O'zgarishlar soni ko'rsatiladi

### Xavfsizlik
- Faqat autentifikatsiya qilingan foydalanuvchilar
- Chef middleware orqali cheklash
- Xatoliklar log ga yoziladi

## API Endpoints

```
GET  /chef/children-count-history     - Tarixni ko'rsatish
POST /chef/update-children-count     - Qo'lda o'zgartirish  
POST /chef/clear-nextday-numbers     - Jadvalni tozalash
```

## Log fayllar
- `storage/logs/laravel.log` da barcha o'zgarishlar yoziladi
- Console command natijalari log ga yoziladi
- Xatoliklar ham log ga yoziladi

## Real-time Notification Tizimi

### Yangi xususiyatlar:
1. **Notification jadvali** - `notifications` jadvali yaratildi
2. **Real-time xabarlar** - Oshpaz o'zgartirishlarida technologlarga avtomatik xabar
3. **Polling tizimi** - Har 30 soniyada yangi xabarlarni tekshirish
4. **Notification UI** - Technolog sahifasida bell icon bilan xabarlar

### Notification tizimi:
- Oshpaz bolalar sonini o'zgartirganda avtomatik notification yaratiladi
- Barcha technolog foydalanuvchilariga xabar yuboriladi
- Real-time yangilanish (30 soniya interval)
- O'qilgan/o'qilmagan holatni kuzatish
- Barcha xabarlarni o'qilgan deb belgilash

### API Endpoints:
```
GET  /technolog/notifications                    - Notificationlarni olish
POST /technolog/notifications/{id}/read         - Notification ni o'qilgan deb belgilash
POST /technolog/notifications/read-all          - Barcha notificationlarni o'qilgan deb belgilash
```
