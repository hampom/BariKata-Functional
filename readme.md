# BariKata-Functional

https://github.com/hampom/BariKata のプラグイン集です

## プラグイン

- FilterPlugin
- MapPlugin
- ReducePlugin

## インストール

Composer を使ってインストールできます

```
composer require hampom/barikata-functional
```

## 使い方

基本的な使い方の例をいくつか示します

### FilterPlugin
```php
$collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
    new FilterPlugin()
]);
 
$results = $collection->filter(fn(int $x) => $x % 2 === 0);
// -> [2, 4]
```

### MapPlugin
```php
$collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
    new MapPlugin()
]);

$results = $collection->map(fn(int $x) => $x * 2);
// -> [2, 4, 6, 8, 10]
```

### ReducePlugin
```php
$collection = new TypedCollection('int', [1, 2, 3, 4, 5], [
    new ReducePlugin()
]);

$results = $collection->reduce(fn(int $carry, int $x) => $carry + $x, 0);
// -> 15
```