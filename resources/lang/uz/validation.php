<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute qabul qilinishi kerak.',
    'accepted_if' => ':other :value bo\'lganda :attribute qabul qilinishi kerak.',
    'active_url' => ':attribute to\'g\'ri URL emas.',
    'after' => ':attribute :date dan keyingi sana bo\'lishi kerak.',
    'after_or_equal' => ':attribute :date dan keyin yoki teng sana bo\'lishi kerak.',
    'alpha' => ':attribute faqat harflardan iborat bo\'lishi kerak.',
    'alpha_dash' => ':attribute faqat harflar, raqamlar, chiziqcha va pastki chiziqdan iborat bo\'lishi kerak.',
    'alpha_num' => ':attribute faqat harflar va raqamlardan iborat bo\'lishi kerak.',
    'array' => ':attribute massiv bo\'lishi kerak.',
    'before' => ':attribute :date dan oldingi sana bo\'lishi kerak.',
    'before_or_equal' => ':attribute :date dan oldin yoki teng sana bo\'lishi kerak.',
    'between' => [
        'numeric' => ':attribute :min va :max oralig\'ida bo\'lishi kerak.',
        'file' => ':attribute :min va :max kilobayt oralig\'ida bo\'lishi kerak.',
        'string' => ':attribute :min va :max belgi oralig\'ida bo\'lishi kerak.',
        'array' => ':attribute :min va :max element oralig\'ida bo\'lishi kerak.',
    ],
    'boolean' => ':attribute maydoni true yoki false bo\'lishi kerak.',
    'confirmed' => ':attribute tasdiqlash mos kelmaydi.',
    'current_password' => 'Parol noto\'g\'ri.',
    'date' => ':attribute to\'g\'ri sana emas.',
    'date_equals' => ':attribute :date ga teng sana bo\'lishi kerak.',
    'date_format' => ':attribute :format formatiga mos kelmaydi.',
    'declined' => ':attribute rad etilishi kerak.',
    'declined_if' => ':other :value bo\'lganda :attribute rad etilishi kerak.',
    'different' => ':attribute va :other farqli bo\'lishi kerak.',
    'digits' => ':attribute :digits ta raqam bo\'lishi kerak.',
    'digits_between' => ':attribute :min va :max raqam oralig\'ida bo\'lishi kerak.',
    'dimensions' => ':attribute noto\'g\'ri rasm o\'lchamiga ega.',
    'distinct' => ':attribute maydoni takroriy qiymatga ega.',
    'email' => ':attribute to\'g\'ri elektron pochta manzili bo\'lishi kerak.',
    'ends_with' => ':attribute quyidagilardan biri bilan tugashi kerak: :values.',
    'exists' => 'Tanlangan :attribute noto\'g\'ri.',
    'file' => ':attribute fayl bo\'lishi kerak.',
    'filled' => ':attribute maydoni to\'ldirilishi kerak.',
    'gt' => [
        'numeric' => ':attribute :value dan katta bo\'lishi kerak.',
        'file' => ':attribute :value kilobaytdan katta bo\'lishi kerak.',
        'string' => ':attribute :value belgidan ko\'p bo\'lishi kerak.',
        'array' => ':attribute :value elementdan ko\'p bo\'lishi kerak.',
    ],
    'gte' => [
        'numeric' => ':attribute :value dan katta yoki teng bo\'lishi kerak.',
        'file' => ':attribute :value kilobaytdan katta yoki teng bo\'lishi kerak.',
        'string' => ':attribute :value belgidan ko\'p yoki teng bo\'lishi kerak.',
        'array' => ':attribute :value elementdan ko\'p yoki teng bo\'lishi kerak.',
    ],
    'image' => ':attribute rasm bo\'lishi kerak.',
    'in' => 'Tanlangan :attribute noto\'g\'ri.',
    'in_array' => ':attribute maydoni :other da mavjud emas.',
    'integer' => ':attribute butun son bo\'lishi kerak.',
    'ip' => ':attribute to\'g\'ri IP manzil bo\'lishi kerak.',
    'ipv4' => ':attribute to\'g\'ri IPv4 manzil bo\'lishi kerak.',
    'ipv6' => ':attribute to\'g\'ri IPv6 manzil bo\'lishi kerak.',
    'json' => ':attribute to\'g\'ri JSON satr bo\'lishi kerak.',
    'lt' => [
        'numeric' => ':attribute :value dan kichik bo\'lishi kerak.',
        'file' => ':attribute :value kilobaytdan kichik bo\'lishi kerak.',
        'string' => ':attribute :value belgidan kam bo\'lishi kerak.',
        'array' => ':attribute :value elementdan kam bo\'lishi kerak.',
    ],
    'lte' => [
        'numeric' => ':attribute :value dan kichik yoki teng bo\'lishi kerak.',
        'file' => ':attribute :value kilobaytdan kichik yoki teng bo\'lishi kerak.',
        'string' => ':attribute :value belgidan kam yoki teng bo\'lishi kerak.',
        'array' => ':attribute :value elementdan ko\'p bo\'lmasligi kerak.',
    ],
    'max' => [
        'numeric' => ':attribute :max dan katta bo\'lmasligi kerak.',
        'file' => ':attribute :max kilobaytdan katta bo\'lmasligi kerak.',
        'string' => ':attribute :max belgidan ko\'p bo\'lmasligi kerak.',
        'array' => ':attribute :max elementdan ko\'p bo\'lmasligi kerak.',
    ],
    'mimes' => ':attribute quyidagi turdagi fayl bo\'lishi kerak: :values.',
    'mimetypes' => ':attribute quyidagi turdagi fayl bo\'lishi kerak: :values.',
    'min' => [
        'numeric' => ':attribute kamida :min bo\'lishi kerak.',
        'file' => ':attribute kamida :min kilobayt bo\'lishi kerak.',
        'string' => ':attribute kamida :min belgi bo\'lishi kerak.',
        'array' => ':attribute kamida :min element bo\'lishi kerak.',
    ],
    'multiple_of' => ':attribute :value ga karrali bo\'lishi kerak.',
    'not_in' => 'Tanlangan :attribute noto\'g\'ri.',
    'not_regex' => ':attribute formati noto\'g\'ri.',
    'numeric' => ':attribute raqam bo\'lishi kerak.',
    'password' => 'Parol noto\'g\'ri.',
    'present' => ':attribute maydoni mavjud bo\'lishi kerak.',
    'prohibited' => ':attribute maydoni taqiqlanadi.',
    'prohibited_if' => ':other :value bo\'lganda :attribute maydoni taqiqlanadi.',
    'prohibited_unless' => ':other :values da bo\'lmaganda :attribute maydoni taqiqlanadi.',
    'prohibits' => ':attribute maydoni :other maydonini taqiqlaydi.',
    'regex' => ':attribute formati noto\'g\'ri.',
    'required' => ':attribute maydoni to\'ldirilishi shart.',
    'required_array_keys' => ':attribute maydoni quyidagi yozuvlar uchun to\'ldirilishi kerak: :values.',
    'required_if' => ':other :value bo\'lganda :attribute maydoni to\'ldirilishi kerak.',
    'required_if_accepted' => ':attribute maydoni qabul qilinganda to\'ldirilishi kerak.',
    'required_unless' => ':other :values da bo\'lmaganda :attribute maydoni to\'ldirilishi kerak.',
    'required_with' => ':values mavjud bo\'lganda :attribute maydoni to\'ldirilishi kerak.',
    'required_with_all' => ':values mavjud bo\'lganda :attribute maydoni to\'ldirilishi kerak.',
    'required_without' => ':values mavjud bo\'lmaganda :attribute maydoni to\'ldirilishi kerak.',
    'required_without_all' => ':values ning hech biri mavjud bo\'lmaganda :attribute maydoni to\'ldirilishi kerak.',
    'same' => ':attribute va :other mos kelishi kerak.',
    'size' => [
        'numeric' => ':attribute :size bo\'lishi kerak.',
        'file' => ':attribute :size kilobayt bo\'lishi kerak.',
        'string' => ':attribute :size belgi bo\'lishi kerak.',
        'array' => ':attribute :size element bo\'lishi kerak.',
    ],
    'starts_with' => ':attribute quyidagilardan biri bilan boshlanishi kerak: :values.',
    'string' => ':attribute satr bo\'lishi kerak.',
    'timezone' => ':attribute to\'g\'ri vaqt mintaqasi bo\'lishi kerak.',
    'unique' => ':attribute allaqachon olingan.',
    'uploaded' => ':attribute yuklanishida xatolik yuz berdi.',
    'url' => ':attribute formati noto\'g\'ri.',
    'uuid' => ':attribute to\'g\'ri UUID bo\'lishi kerak.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'elektron pochta',
        'password' => 'parol',
        'name' => 'ism',
        'username' => 'foydalanuvchi nomi',
        'first_name' => 'ism',
        'last_name' => 'familiya',
        'phone' => 'telefon',
        'address' => 'manzil',
        'city' => 'shahar',
        'country' => 'mamlakat',
        'postal_code' => 'pochta indeksi',
        'current_password' => 'joriy parol',
        'new_password' => 'yangi parol',
        'password_confirmation' => 'parol tasdiqlash',
    ],

]; 