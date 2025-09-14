<?php

declare(strict_types=1);

namespace Hampom\BariKataFunctional;

use Hampom\BariKata\Plugins\CollectionPlugin;
use Hampom\BariKata\TypedCollection;

/**
 * # FilterPlugin
 *
 * ## Example
 *
 * ```
 * use Hampom\BariKata\TypedCollection;
 * use Hampom\BariKataFunctional\FilterPlugin;
 *
 * $collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
 *     new FilterPlugin()
 * ]);
 *
 * $results = $collection->filter(fn(int $x) => $x % 2 === 0);
 *
 * var_dump(iterator_to_array($results)); // [2, 4]
 * ```
 */
final class FilterPlugin implements CollectionPlugin
{
    public function __construct(
        private $defaultCallback = null,
    ) {}

    public function getName(): string
    {
        return 'filter';
    }

    public function apply(TypedCollection $collection, ...$args): mixed
    {
        $callback = $args[0] ?? $this->defaultCallback;
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('filter() requires a callable as the first argment.');
        }

        $result = [];
        foreach ($collection as $item) {
            if ($callback($item)) {
                $result[] = $item;
            }
        }

        return new TypedCollection($collection->type, $result, $collection->getPlugins());
    }
}
