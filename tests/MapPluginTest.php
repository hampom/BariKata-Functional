<?php

declare(strict_types=1);

use Hampom\BariKata\TypedCollection;
use Hampom\BariKataFunctional\MapPlugin;
use PHPUnit\Framework\TestCase;

final class MapPluginTest extends TestCase
{
    public function testApplyWithCallable(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3, 4, 5]);
        $plugin = new MapPlugin();

        $result = $plugin->apply($collection, fn($x) => $x * 2);

        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame([2, 4, 6, 8, 10], iterator_to_array($result));
    }

    public function testApplyWithDefaultCallback(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new MapPlugin(fn($x) => $x + 1);

        $result = $plugin->apply($collection);

        $this->assertSame([2, 3, 4], iterator_to_array($result));
    }

    public function testApplyThrowsIfNoCallableProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new MapPlugin();
        $plugin->apply($collection); // コールバック未指定 → 例外
    }

    public function testApplyPreservesPlugins(): void
    {
        $plugin = new MapPlugin(fn($x) => $x * 2);
        $collection = new TypedCollection('int', [1], [$plugin]);

        $result = $plugin->apply($collection);

        $this->assertCount(1, $result->getPlugins()); // プラグインが引き継がれている
    }

    public function testApplyChangesTypeAccordingToResult(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new MapPlugin();

        // int → string に変換
        $result = $plugin->apply($collection, fn($x) => (string) $x);

        $this->assertSame(['1', '2', '3'], iterator_to_array($result));
        $this->assertSame('string', $result->type);
    }

    public function testApplyOnEmptyCollectionReturnsEmptyTypedCollection(): void
    {
        $collection = new TypedCollection('int', []);
        $plugin = new MapPlugin(fn($x) => $x * 2);

        $result = $plugin->apply($collection);

        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame([], iterator_to_array($result));
        $this->assertSame('mixed', $result->type); // 要素がないので mixed
    }
}
