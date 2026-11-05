<?php

namespace App\Filters;

class ProductDimensions
{
    /**
     * Слаги измерений, участвующих в фильтрации каталога.
     *
     * Слаг измерения совпадает со слагом типа в справочнике
     * `product_option_types`, поэтому список значений берётся из базы,
     * а список параметров — здесь.
     *
     * @return array<int, string>
     */
    public static function slugs(): array
    {
        return [
            'composition',
            'packing',
            'color',
            'audience',
            'event',
        ];
    }

    /**
     * Подпись параметра в карточке товара.
     *
     * Отличается от клиентской подписи в фильтре: в админке рядом стоят
     * два разных источника состава — значения для фильтров и матрица
     * букета для страницы товара.
     */
    public static function adminTitle(string $title): string
    {
        return $title.' для фильтров';
    }
}
