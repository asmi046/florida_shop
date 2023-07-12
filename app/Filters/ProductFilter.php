<?php

namespace App\Filters;

class ProductFilter extends QueryFilter {
    public function minprice($minprice) {
        if (!empty($minprice))
            $this->builder->where("price", '>', $minprice);
    }

    public function maxprice($maxprice) {
        if (!empty($maxprice))
            $this->builder->where("price", '<', $maxprice);
    }



    public function order($order) {
            if ($order == "Сначала дешевые") $this->builder->orderBy('price', 'asc');
            if ($order == "Сначала дорогие") $this->builder->orderBy('price', 'desc');
            if ($order == "В алфавитном порядке") $this->builder->orderBy('title', 'asc');
    }

    public function brand($brand) {
            if ($brand != "%") $this->builder->where("brand", $brand);
    }

    public function subcat($subcat) {
        if (!empty($subcat)) $this->builder->whereIn("sub_category", $subcat);
    }

    public function osobennosti($osobennosti) {

        foreach ($osobennosti as $osb)
        {
            if ($osb == "Скидка") $this->builder->where("old_price", 0);
            if ($osb == "Хит продаж") $this->builder->where("hit", 1);
            if ($osb == "Новинки") $this->builder->where("new", 1);
        }

    }
}
