<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilter implements FilterInterface
{
    /** @var array */
    private $queryParams = [];

    abstract protected function getCallbacks(): array;

    public function apply(Builder $builder, array $queryParams): Builder
    {
        $this->queryParams = $queryParams; // Сохраняем параметры в свойство
        $this->before($builder);

        foreach ($this->getCallbacks() as $name => $callback) {
            if (isset($this->queryParams[$name])) {
                call_user_func($callback, $builder, $this->queryParams[$name]);
            }
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     */
    protected function before(Builder $builder)
    {
        // Оставляем пустым, как было
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function getQueryParam(string $key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * @param string[] $keys
     *
     * @return AbstractFilter
     */
    protected function removeQueryParam(string ...$keys)
    {
        foreach ($keys as $key) {
            unset($this->queryParams[$key]);
        }

        return $this;
    }
}
