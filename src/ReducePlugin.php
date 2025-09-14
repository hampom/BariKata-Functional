<?php

declare(strict_types=1);

namespace Hampom\BariKataFunctional;

use Hampom\BariKata\Plugins\CollectionPlugin;
use Hampom\BariKata\TypedCollection;

/**
 * # ReducePlugin
 *
 * ## Example
 *
 * ```
 * use Hampom\BariKata\TypedCollection;
 * use Hampom\BariKataFunctional\ReducePlugin;
 *
 * $collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
 *     new ReducePlugin()
 * ]);
 *
 * $results = $collection->reduce(fn(int $carry, int $x) => $carry + $x, 0);
 *
 * var_dump($results); // 15
 * ```
 */
final class ReducePlugin implements CollectionPlugin
{
    public function __construct(
        private $defaultCallback = null,
        private mixed $initial = null,
    ) {}

    public function getName(): string
    {
        return 'reduce';
    }

    public function apply(TypedCollection $collection, ...$args): mixed
    {
        $callback = $args[0] ?? $this->defaultCallback;
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('reduce() requires a callable as the first argment.');
        }
        $initial = $args[1] ?? $this->initial;

        $acc = $initial;
        foreach ($collection as $item) {
            $acc = $callback($acc, $item);
        }

        return $acc;
    }
}
