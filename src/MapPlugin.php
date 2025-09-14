<?php

declare(strict_types=1);

namespace Hampom\BariKataFunctional;

use Hampom\BariKata\Plugins\CollectionPlugin;
use Hampom\BariKata\TypedCollection;

/**
 * # MapPlugin
 *
 * ## Example
 *
 * ```
 * use Hampom\BariKata\TypedCollection;
 * use Hampom\BariKataFunctional\MapPlugin;
 *
 * $collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
 *     new MapPlugin()
 * ]);
 *
 * $results = $collection->map(fn(int $x) => $x * 2);
 *
 * var_dump(iterator_to_array($results)); // [2, 4, 6, 8, 10]
 * ```
 */
final class MapPlugin implements CollectionPlugin
{
    public function __construct(
        private $defaultCallback = null,
    ) {}

    public function getName(): string
    {
        return 'map';
    }

    public function apply(TypedCollection $collection, mixed ...$args): mixed
    {
        $callback = $args[0] ?? $this->defaultCallback;
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('map() requires a callable as the first argment.');
        }

        $result = [];
        foreach ($collection as $key => $item) {
            $result[] = $callback($item, $key);
        }

        $first = $result[0] ?? null;
        $type = $first !== null ? get_debug_type($first) : 'mixed';

        return new TypedCollection($type, $result, $collection->getPlugins());
    }
}
