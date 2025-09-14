<?php

declare(strict_types=1);

use Hampom\BariKata\TypedCollection;
use Hampom\BariKataFunctional\ReducePlugin;
use PHPUnit\Framework\TestCase;

final class ReducePluginTest extends TestCase
{
    public function testApplyWithCallableAndInitialValue(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3, 4, 5]);
        $plugin = new ReducePlugin();

        $sum = $plugin->apply($collection, fn($carry, $x) => $carry + $x, 0);

        $this->assertSame(15, $sum);
    }

    public function testApplyWithDefaultCallbackAndInitialValue(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new ReducePlugin(fn($carry, $x) => $carry + $x, 10);

        $result = $plugin->apply($collection);

        // 10 + 1 + 2 + 3 = 16
        $this->assertSame(16, $result);
    }

    public function testApplyThrowsIfNoCallbackProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new ReducePlugin();
        $plugin->apply($collection); // no callback → exception
    }

    public function testApplyThrowsIfNonCallablePassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new ReducePlugin();
        $plugin->apply($collection, 'not_callable');
    }

    public function testApplyWorksWithoutInitialValue(): void
    {
        $collection = new TypedCollection('int', [1, 2, 3]);
        $plugin = new ReducePlugin();

        // null + 1 + 2 + 3 = 6 (PHPではnull+intはintに変換される)
        $result = $plugin->apply($collection, fn($carry, $x) => $carry + $x);

        $this->assertSame(6, $result);
    }
}
