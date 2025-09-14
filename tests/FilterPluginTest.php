<?php

declare(strict_types=1);

use Hampom\BariKata\Plugins\CollectionPlugin;
use Hampom\BariKata\TypedCollection;
use Hampom\BariKataFunctional\FilterPlugin;
use PHPUnit\Framework\TestCase;

final class FilterPluginTest extends TestCase
{
    public function testApplyWithCallableFiltersItems(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3, 4]);
        $plugin = new FilterPlugin();

        $result = $plugin->apply($collection, fn(int $x) => ($x % 2) === 0);

        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame([2, 4], iterator_to_array($result));
        $this->assertSame('int', $result->type);
    }

    public function testApplyUsesDefaultCallbackWhenProvided(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3, 4]);
        $plugin = new FilterPlugin(fn(int $x) => $x > 2);

        $result = $plugin->apply($collection);

        $this->assertSame([3, 4], iterator_to_array($result));
    }

    public function testApplyThrowsIfNoCallbackProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new FilterPlugin();
        $plugin->apply($collection);
    }

    public function testApplyThrowsIfNonCallablePassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new FilterPlugin();
        $plugin->apply($collection, 'not_callable');
    }

    public function testApplyPreservesTypeAndPlugins(): void
    {
        // プラグインのモックを作成
        $dummyPlugin = $this->createMock(CollectionPlugin::class);
        $dummyPlugin->method('getName')->willReturn('dummy');

        // TypedCollection にプラグインを登録した状態で作成
        $collection = new TypedCollection('int', [1, 2, 3], [$dummyPlugin]);

        $plugin = new FilterPlugin(fn(int $x) => $x > 1);
        $result = $plugin->apply($collection);

        $this->assertInstanceOf(TypedCollection::class, $result);
        $this->assertSame('int', $result->type, 'Type should be preserved');
        $this->assertCount(1, $result->getPlugins(), 'Plugins should be preserved');
        $this->assertSame([2, 3], iterator_to_array($result));
    }
}
