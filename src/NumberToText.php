<?php

namespace Serjazz\RuNumberToText;

/**
 * Class for translate numbers to text.
 * By idea of Sergey Prokhorov <me@seriyps.ru> https://github.com/seriyps/ru_number_to_text from python to php
 */
class NumberToText
{
    public array $units = [
        0 => 'ноль',
        1 => ['один', 'одна'],
        2 => ['два', 'две'],
        3 => 'три',
        4 => 'четыре',
        5 => 'пять',
        6 => 'шесть',
        7 => 'семь',
        8 => 'восемь',
        9 => 'девять'
    ];

    public array $teens = [
        'десять',
        'одиннадцать',
        'двенадцать',
        'тринадцать',
        'четырнадцать',
        'пятнадцать',
        'шестнадцать',
        'семнадцать',
        'восемнадцать',
        'девятнадцать'
    ];

    public array $tens = [];

    public array $hundreds = [
        'сто',
        'двести',
        'триста',
        'четыреста',
        'пятьсот',
        'шестьсот',
        'семьсот',
        'восемьсот',
        'девятьсот'
    ];

    public array $orders = [
        [['тысяча', 'тысячи', 'тысяч'], 'f'],
        [['миллион', 'миллиона', 'миллионов'], 'm'],
        [['миллиард', 'миллиарда', 'миллиардов'], 'm']
    ];

    public string $minus = 'минус';

    public function __construct()
    {
        $this->tens = [
            $this->teens,
            'двадцать',
            'тридцать',
            'сорок',
            'пятьдесят',
            'шестьдесят',
            'семьдесят',
            'восемьдесят',
            'девяносто'
        ];
    }

    /**
     * Работа с тысячами
     * @param int $rest
     * @param string $sex
     * @return array
     */
    private function thousand(int $rest, string $sex): array
    {
        $prev = 0;
        $plural = 2;
        $name = [];
        $use_teens = $rest % 100 >= 10 && $rest % 100 <= 19;

        if (!$use_teens) {
            $data = [[$this->units, 10], [$this->tens, 100], [$this->hundreds, 1000]];
        } else {
            $data = [[$this->teens, 10], [$this->hundreds, 1000]];
        }

        foreach ($data as [$names, $x]) {
            $cur = (int)((($rest - $prev) % $x) * 10 / $x);
            $prev = $rest % $x;

            if ($x == 10 && $use_teens) {
                $plural = 2;
                $name[] = $this->teens[$cur];
            } elseif ($cur == 0) {
                continue;
            } elseif ($x == 10) {
                $name_val = $names[$cur];
                if (is_array($name_val)) {
                    $name_val = $name_val[$sex === 'm' ? 0 : 1];
                }
                $name[] = $name_val;

                if ($cur >= 2 && $cur <= 4) {
                    $plural = 1;
                } elseif ($cur == 1) {
                    $plural = 0;
                } else {
                    $plural = 2;
                }
            } else {
                $name[] = $names[$cur - 1];
            }
        }

        return [$plural, $name];
    }

    /**
     * Преобразует целое число в текст.
     * @param $num - целое число для преобразования
     * @param array|null $main_units - массив конфигурации единиц измерения:
     * @return string
     */
    public function num2text($num, ?array $main_units = null): string
    {
        if ($main_units === null) {
            $main_units = [['', '', ''], 'm'];
        }

        $_orders = array_merge([$main_units], $this->orders);

        if ($num == 0) {
            return trim($this->units[0] . ' ' . $_orders[0][0][2]);
        }

        $rest = abs($num);
        $ord = 0;
        $name = [];

        while ($rest > 0) {
            $current_rest = $rest % 1000;
            [$plural, $nme] = $this->thousand($current_rest, $_orders[$ord][1]);
            if (!empty($nme) || $ord == 0) {
                $name[] = $_orders[$ord][0][$plural];
            }
            $name = array_merge($name, $nme);
            $rest = (int)($rest / 1000);
            $ord++;
        }

        if ($num < 0) {
            $name[] = $this->minus;
        }

        $name = array_reverse($name);
        return trim(implode(' ', $name));
    }

    /**
     * Преобразует дробное число в текст.
     * @param $value - дробное число или строка
     * @param int $places - количество знаков после запятой (по умолчанию 2)
     * @param array|null $int_units - единицы измерения для целой части
     * @param array|null $exp_units - единицы измерения для дробной части
     * @param bool $no_money - не денежные единицы если true (корректно формирует дробную часть при этом)
     * @return string
     */
    public function decimal2text($value, int $places = 2, ?array $int_units = null, ?array $exp_units = null, bool $no_money = true): string
    {
        if ($int_units === null) {
            $int_units = [['', '', ''], 'm'];
        }
        if ($exp_units === null) {
            $exp_units = [['', '', ''], 'm'];
        }

        $formatted = number_format((float)$value, $places, '.', '');
        [$integral, $exp] = explode('.', $formatted);

        if (!$no_money) {
            $exp = str_pad($exp, 2, '0', STR_PAD_RIGHT);
            $exp = substr($exp, 0, 2);
            $exp_value = (int)$exp;
        } else {
            // Убраны rtrim и избыточная обработка
            $exp_value = (int)$exp;
        }

        return $this->num2text((int)$integral, $int_units) . ' ' . $this->num2text($exp_value, $exp_units);
    }
}