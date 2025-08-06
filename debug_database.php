<?php
// Bazani tekshirish uchun oddiy skript

// Laravel muhitini yuklash
require_once 'vendor/autoload.php';

// Laravel app ni boshlash
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Menu_composition modelini sinash
    $test = \App\Models\Menu_composition::first();
    echo "Menu_composition modeli ishlayapti.\n";
    
    // Ustunlarni tekshirish
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('menu_compositions');
    echo "Menu_compositions jadvali ustunlari:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    // Nutrition ustunlarni tekshirish
    $nutritionColumns = ['waste_free', 'proteins', 'fats', 'carbohydrates', 'kcal'];
    $missing = [];
    foreach ($nutritionColumns as $col) {
        if (!in_array($col, $columns)) {
            $missing[] = $col;
        }
    }
    
    if (empty($missing)) {
        echo "\nBarcha nutrition ustunlari mavjud!\n";
    } else {
        echo "\nEtishmayotgan ustunlar: " . implode(', ', $missing) . "\n";
        echo "\nSQL buyruqlarni bajarish kerak:\n";
        echo "ALTER TABLE `menu_compositions` ADD COLUMN `waste_free` DECIMAL(8,2) NULL AFTER `weight`;\n";
        echo "ALTER TABLE `menu_compositions` ADD COLUMN `proteins` DECIMAL(8,2) NULL AFTER `waste_free`;\n";
        echo "ALTER TABLE `menu_compositions` ADD COLUMN `fats` DECIMAL(8,2) NULL AFTER `proteins`;\n";
        echo "ALTER TABLE `menu_compositions` ADD COLUMN `carbohydrates` DECIMAL(8,2) NULL AFTER `fats`;\n";
        echo "ALTER TABLE `menu_compositions` ADD COLUMN `kcal` DECIMAL(8,2) NULL AFTER `carbohydrates`;\n";
    }
    
} catch (Exception $e) {
    echo "Xato: " . $e->getMessage() . "\n";
}
?> 