<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilter
{
    public $request;

    protected $builder;

    protected $delimiter = ',';

    /**
     * Разрешённые query-параметры фильтра.
     *
     * Для каждого параметра из списка вызывается метод с таким же именем,
     * значение параметра передаётся в него аргументом. Параметры, которых
     * нет в списке, игнорируются — так пользователь не может вызвать
     * служебные методы фильтра через query-строку.
     *
     * @var array<int, string>
     */
    protected array $allowed = [];

    /**
     * Служебные методы фильтра, вызов которых через query-строку запрещён.
     *
     * @var array<int, string>
     */
    protected array $reserved = ['apply', 'filters', 'paramToArray'];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function filters()
    {
        return $this->request->query();
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (! $this->isAllowed($name)) {
                continue;
            }

            $arguments = array_filter([$value]);

            if ($arguments === []) {
                continue;
            }

            call_user_func_array([$this, $name], $arguments);
        }

        return $this->applyExtra($this->builder);
    }

    /**
     * Хук для дополнительных условий, которые нельзя выразить методом
     * с именем query-параметра.
     */
    protected function applyExtra(Builder $builder): Builder
    {
        return $builder;
    }

    /**
     * Проверяет, что параметр разрешён и для него есть метод-фильтр.
     */
    protected function isAllowed(string $name): bool
    {
        if (! in_array($name, $this->allowed, true)) {
            return false;
        }

        return method_exists($this, $name) && ! in_array($name, $this->reserved, true);
    }

    /**
     * Есть ли в query-строке непустое значение параметра.
     */
    public function has(string $name): bool
    {
        $value = $this->request->query($name);

        if (is_null($value) || $value === '' || $value === []) {
            return false;
        }

        return true;
    }

    protected function paramToArray($param)
    {
        return explode($this->delimiter, $param);
    }
}
