<?php
namespace Serjazz\RuNumberToText\Tests;

use PHPUnit\Framework\TestCase;
use Serjazz\RuNumberToText\NumberToText;

class NumberToTextTest extends TestCase
{
    /**
     * @var NumberToText
     */
    private $converter;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->converter = new NumberToText();
    }

    // Базовые числа
    public function testBasicNumbers()
    {
        $this->assertEquals('ноль', $this->converter->num2text(0));
        $this->assertEquals('один', $this->converter->num2text(1));
        $this->assertEquals('два', $this->converter->num2text(2));
        $this->assertEquals('девять', $this->converter->num2text(9));
    }

    // Гендерные формы
    public function testGenderForms()
    {
        $this->assertEquals('одна тысяча', $this->converter->num2text(1000));
        $this->assertEquals('две тысячи', $this->converter->num2text(2000));
        $this->assertEquals('один миллион', $this->converter->num2text(1000000));
        $this->assertEquals('два миллиона', $this->converter->num2text(2000000));
    }

    // Числа 10-19
    public function testTeens()
    {
        $this->assertEquals('десять', $this->converter->num2text(10));
        $this->assertEquals('одиннадцать', $this->converter->num2text(11));
        $this->assertEquals('пятнадцать', $this->converter->num2text(15));
        $this->assertEquals('девятнадцать', $this->converter->num2text(19));
    }

    // Десятки
    public function testTens()
    {
        $this->assertEquals('двадцать', $this->converter->num2text(20));
        $this->assertEquals('тридцать', $this->converter->num2text(30));
        $this->assertEquals('девяносто', $this->converter->num2text(90));
    }

    // Сотни
    public function testHundreds()
    {
        $this->assertEquals('сто', $this->converter->num2text(100));
        $this->assertEquals('двести', $this->converter->num2text(200));
        $this->assertEquals('пятьсот', $this->converter->num2text(500));
        $this->assertEquals('девятьсот', $this->converter->num2text(900));
    }

    // Разряды чисел
    public function testNumberOrders()
    {
        $this->assertEquals('одна тысяча', $this->converter->num2text(1000));
        $this->assertEquals('две тысячи', $this->converter->num2text(2000));
        $this->assertEquals('пять тысяч', $this->converter->num2text(5000));

        $this->assertEquals('один миллион', $this->converter->num2text(1000000));
        $this->assertEquals('два миллиона', $this->converter->num2text(2000000));
        $this->assertEquals('пять миллионов', $this->converter->num2text(5000000));

        $this->assertEquals('один миллиард', $this->converter->num2text(1000000000));
        $this->assertEquals('два миллиарда', $this->converter->num2text(2000000000));
        $this->assertEquals('пять миллиардов', $this->converter->num2text(5000000000));
    }

    // Составные числа
    public function testCompositeNumbers()
    {
        $this->assertEquals('одна тысяча сто', $this->converter->num2text(1100));
        $this->assertEquals('две тысячи один', $this->converter->num2text(2001));
        $this->assertEquals('пять тысяч одиннадцать', $this->converter->num2text(5011));

        $this->assertEquals('один миллион две тысячи', $this->converter->num2text(1002000));
        $this->assertEquals('два миллиона двадцать тысяч', $this->converter->num2text(2020000));
        $this->assertEquals('пять миллионов триста тысяч шестьсот', $this->converter->num2text(5300600));

        $this->assertEquals('один миллиард два миллиона', $this->converter->num2text(1002000000));
        $this->assertEquals('два миллиарда тридцать миллионов', $this->converter->num2text(2030000000));

        $expected = 'один миллиард двести тридцать четыре миллиона ' .
            'пятьсот шестьдесят семь тысяч ' .
            'восемьсот девяносто один';
        $this->assertEquals($expected, $this->converter->num2text(1234567891));
    }

    // Основные единицы измерения
    public function testMainUnits()
    {
        // Мужской род (рубли)
        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $this->assertEquals('сто один рубль', $this->converter->num2text(101, $rubUnits));
        $this->assertEquals('сто два рубля', $this->converter->num2text(102, $rubUnits));
        $this->assertEquals('сто пять рублей', $this->converter->num2text(105, $rubUnits));
        $this->assertEquals('ноль рублей', $this->converter->num2text(0, $rubUnits));
        $this->assertEquals('три тысячи рублей', $this->converter->num2text(3000, $rubUnits));

        // Женский род (копейки)
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];
        $this->assertEquals('сто одна копейка', $this->converter->num2text(101, $kopUnits));
        $this->assertEquals('сто две копейки', $this->converter->num2text(102, $kopUnits));
        $this->assertEquals('сто пять копеек', $this->converter->num2text(105, $kopUnits));
        $this->assertEquals('ноль копеек', $this->converter->num2text(0, $kopUnits));

        // Нейтральные единицы
        $itemUnits = [['штука', 'штуки', 'штук'], 'f'];
        $this->assertEquals('двадцать одна штука', $this->converter->num2text(21, $itemUnits));
        $this->assertEquals('двадцать две штуки', $this->converter->num2text(22, $itemUnits));
        $this->assertEquals('двадцать пять штук', $this->converter->num2text(25, $itemUnits));
    }

    // Дробные числа
    public function testDecimalNumbers()
    {
        // Деньги (рубли/копейки)
        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];

        $this->assertEquals(
            'сто пять рублей двадцать пять копеек',
            $this->converter->decimal2text(105.245, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'сто один рубль двадцать шесть копеек',
            $this->converter->decimal2text(101.26, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'сто два рубля двадцать четыре копейки',
            $this->converter->decimal2text(102.2450, 4, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'сто одиннадцать рублей ноль копеек',
            $this->converter->decimal2text(111, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'три тысячи рублей ноль копеек',
            $this->converter->decimal2text(3000.00, 2, $rubUnits, $kopUnits, false)
        );

        // Другие единицы измерения
        $meterUnits = [['метр', 'метра', 'метров'], 'm'];
        $cmUnits = [['сантиметр', 'сантиметра', 'сантиметров'], 'm'];

        $this->assertEquals(
            'три метра семьдесят пять сантиметров',
            $this->converter->decimal2text(3.75, 2, $meterUnits, $cmUnits)
        );

        $this->assertEquals(
            'пять метров пятьдесят сантиметров',
            $this->converter->decimal2text(5.50, 2, $meterUnits, $cmUnits)
        );

        // Денежный формат с обработкой копеек
        $this->assertEquals(
            'пятнадцать тысяч рублей сорок шесть копеек',
            $this->converter->decimal2text(15000.46, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'один рубль одна копейка',
            $this->converter->decimal2text(1.01, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'два рубля две копейки',
            $this->converter->decimal2text(2.02, 2, $rubUnits, $kopUnits, false)
        );
    }

    // Отрицательные числа
    public function testNegativeNumbers()
    {
        $this->assertEquals(
            'минус двенадцать тысяч триста сорок пять',
            $this->converter->num2text(-12345)
        );

        $this->assertEquals(
            'минус сто двадцать три сорок пять',
            $this->converter->decimal2text(-123.45)
        );

        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];

        $this->assertEquals(
            'минус пятнадцать тысяч рублей сорок шесть копеек',
            $this->converter->decimal2text(-15000.46, 2, $rubUnits, $kopUnits, false)
        );
    }

    // Граничные значения и особые случаи
    public function testEdgeCases()
    {
        // Максимальное поддерживаемое число
        $expected = 'девятьсот девяносто девять миллиардов ' .
            'девятьсот девяносто девять миллионов ' .
            'девятьсот девяносто девять тысяч ' .
            'девятьсот девяносто девять';
        $this->assertEquals($expected, $this->converter->num2text(999999999999));

        // Минимальное отрицательное
        $expected = 'минус ' . $expected;
        $this->assertEquals($expected, $this->converter->num2text(-999999999999));

        // Дробная часть = 0
        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];
        $this->assertEquals(
            'сто рублей ноль копеек',
            $this->converter->decimal2text(100.00, 2, $rubUnits, $kopUnits, false)
        );

        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];

        // Денежный режим
        $this->assertEquals(
            'ноль рублей десять копеек',
            $this->converter->decimal2text(0.10, 2, $rubUnits, $kopUnits, false)
        );

        // Проверка дополнения нулями
        $this->assertEquals(
            'ноль рублей одна копейка',
            $this->converter->decimal2text(0.01, 2, $rubUnits, $kopUnits, false)
        );

        $this->assertEquals(
            'ноль рублей пять копеек',
            $this->converter->decimal2text(0.05, 2, $rubUnits, $kopUnits, false)
        );

        // Проверка обрезки до 2 знаков
        $this->assertEquals(
            'пять рублей сорок пять копеек',
            $this->converter->decimal2text(5.459, 3, $rubUnits, $kopUnits, false)
        );

        // Большая дробная часть
        $this->assertEquals(
            'пять рублей девяносто девять копеек',
            $this->converter->decimal2text(5.99, 2, $rubUnits, $kopUnits, false)
        );
    }

    public function testDecimalCornerCases()
    {
        $rubUnits = [['рубль', 'рубля', 'рублей'], 'm'];
        $kopUnits = [['копейка', 'копейки', 'копеек'], 'f'];

        // 0.10 в денежном режиме
        $this->assertEquals(
            'ноль рублей десять копеек',
            $this->converter->decimal2text(0.10, 2, $rubUnits, $kopUnits, false)
        );

        // 0.01 в денежном режиме
        $this->assertEquals(
            'ноль рублей одна копейка',
            $this->converter->decimal2text(0.01, 2, $rubUnits, $kopUnits, false)
        );

        // 1.50 в денежном режиме
        $this->assertEquals(
            'один рубль пятьдесят копеек',
            $this->converter->decimal2text(1.50, 2, $rubUnits, $kopUnits, false)
        );

        // 123.456 в денежном режиме с разной точностью
        $this->assertEquals(
            'сто двадцать три рубля сорок пять копеек',
            $this->converter->decimal2text(123.456, 3, $rubUnits, $kopUnits, false)
        );

        // 5.999 в денежном режиме
        $this->assertEquals(
            'пять рублей девяносто девять копеек',
            $this->converter->decimal2text(5.999, 3, $rubUnits, $kopUnits, false)
        );
    }
}