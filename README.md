# NumberToText - Конвертер чисел в текстовое представление на русском языке

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net/releases/8.1)
[![Test Coverage](https://img.shields.io/badge/coverage-98%25-green)](https://github.com/your-vendor/ru-number-to-text)

PHP-библиотека для преобразования чисел в их текстовое представление на русском языке с учётом грамматических правил. Портированная реализация Python-библиотеки [seriyps/ru_number_to_text](https://github.com/seriyps/ru_number_to_text).

## Особенности
- Преобразование целых чисел (-999 млрд до +999 млрд)
- Обработка дробных чисел (денежный и неденежный режим)
- Автоматическое склонение единиц измерения:
  - Род (мужской/женский)
  - Формы единственного/множественного числа
- Поддержка отрицательных чисел
- Без внешних зависимостей

## Установка
```bash
composer require your-vendor/ru-number-to-text
```

## Базовое использование
### Преобразование целых чисел
```bash
use Serjazz\RuNumberToText\NumberToText;

$converter = new NumberToText();

echo $converter->num2text(42); // "сорок два"
echo $converter->num2text(1234); // "одна тысяча двести тридцать четыре"
echo $converter->num2text(-567); // "минус пятьсот шестьдесят семь"
```

### Работа с единицами измерения
```bash
// Рубль (мужской род)
$rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
echo $converter->num2text(101, $rubUnits); // "сто один рубль"
echo $converter->num2text(102, $rubUnits); // "сто два рубля"
echo $converter->num2text(105, $rubUnits); // "сто пять рублей"

// Копейка (женский род)
$kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];
echo $converter->num2text(101, $kopUnits); // "сто одна копейка"
echo $converter->num2text(102, $kopUnits); // "сто две копейки"
echo $converter->num2text(105, $kopUnits); // "сто пять копеек"
```

### Работа с дробными числами
```bash
// Денежный режим (рубли/копейки)
$rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
$kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];

echo $converter->decimal2text(123.45, 2, $rubUnits, $kopUnits, false); 
// "сто двадцать три рубля сорок пять копеек"

echo $converter->decimal2text(5.99, 2, $rubUnits, $kopUnits, false); 
// "пять рублей девяносто девять копеек"

// Неденежный режим (метры/сантиметры)
$meterUnits = [['метр', 'метра', 'метров'], 'm'];
$cmUnits = [['сантиметр', 'сантиметра', 'сантиметров'], 'm'];

echo $converter->decimal2text(3.75, 2, $meterUnits, $cmUnits); 
// "три метра семьдесят пять сантиметров"

echo $converter->decimal2text(5.50, 2, $meterUnits, $cmUnits); 
// "пять метров пятьдесят сантиметров"
```
## Документация методов

num2text(int $num, ?array $units = null): string

Преобразует целое число в текстовое представление.

### Параметры:

- $num: Целое число в диапазоне от -999 999 999 999 до 999 999 999 999
- $units: Массив конфигурации единиц измерения:

```bash
php
[
 ['форма_1', 'форма_2-4', 'форма_5-20'], // формы для 1, 2-4, 5-20
 'род' // 'm' для мужского, 'f' для женского
]
```

### Пример:
```bash
php
$itemUnits = [['штука', 'штуки', 'штук'], 'f'];
echo $converter->num2text(21, $itemUnits); // "двадцать одна штука"
decimal2text(float $value, int $places = 2, ?array $int_units = null, ?array $exp_units = null, bool $no_money = true): string
Преобразует дробное число в текстовое представление.
```

### Параметры:

- $value: Дробное число
- $places: Количество знаков после запятой (по умолчанию 2)
- $int_units: Единицы измерения для целой части
- $exp_units: Единицы измерения для дробной части
- $no_money: Режим обработки:
  - true (по умолчанию): Неденежный режим (0.50 → 50)
  - false: Денежный режим (фиксированная точность, 0.50 → 50 копеек)


### Примеры:
```bash
php
// Денежный режим
echo $converter->decimal2text(1.01, 2, $rubUnits, $kopUnits, false);
// "один рубль одна копейка"

// Неденежный режим
echo $converter->decimal2text(7.08, 2, $meterUnits, $cmUnits);
// "семь метров восемь сантиметров"
```

## Ограничения
- Максимальное поддерживаемое число: 999 999 999 999 (999 миллиардов)
- Минимальное число: -999 999 999 999
- Денежный режим всегда использует 2 знака для дробной части
- Триллионы пока не поддерживаются

## Тестирование

Библиотека имеет 98% покрытие тестами. Для запуска тестов:
```bash
composer test
```

## Лицензия
MIT License