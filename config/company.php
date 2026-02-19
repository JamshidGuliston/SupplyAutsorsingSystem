<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Autsorser kompaniya ma'lumotlari
    |--------------------------------------------------------------------------
    |
    | Bu fayl autsorser kompaniyasining asosiy ma'lumotlarini o'z ichiga oladi
    |
    */

    'autorser' => [
        'company_name' => env('COMPANY_NAME', 'NISHON INVEST MCHJ'),
        'company_director' => env('COMPANY_DIRECTOR', '_____________________________'),
        'address' => env('AUTSORSER_ADDRESS', 'г.Гулистан ул.Бирлашган №5/13'),
        'bank_account' => env('AUTSORSER_BANK_ACCOUNT', '2020 8000 0007 2639 3001'),
        'bank' => env('AUTSORSER_BANK', 'ДАТ "Асака Банк"'),
        'mfo' => env('AUTSORSER_MFO', '00373'),
        'inn' => env('AUTSORSER_INN', '111 111 111'),
        'phone' => env('AUTSORSER_PHONE', '(90) 000 00 09'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Buyurtmachi standart ma'lumotlari
    |--------------------------------------------------------------------------
    |
    | Bu fayl buyurtmachining standart ma'lumotlarini o'z ichiga oladi
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Hisob-faktura standart ma'lumotlari
    |--------------------------------------------------------------------------
    |
    | Bu fayl hisob-faktura uchun standart ma'lumotlarni o'z ichiga oladi
    |
    */

    'invoice' => [
        'default_number' => env('INVOICE_DEFAULT_NUMBER', '____________'),
        'default_date' => env('INVOICE_DEFAULT_DATE', '______________________'),
        'vat_percentage' => env('VAT_PERCENTAGE', 12),
    ],
]; 