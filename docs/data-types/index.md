---
layout: default
title: Data Types
nav_order: 20
has_children: false
has_toc: false
---

# Data Types
{: .no_toc }

- TOC
{:toc}

This section should give you an overview of all the basic types provided by the Philly framework.

## Support Types

### JsonCompatible
The `JsonCompatible` type extends the [`JsonSerializable`](https://www.php.net/manual/class.jsonserializable.php) interface to give users easy access to the json serialization.

It provides methods like `JsonCompatible::asJson(): string` and `JsonCompatible::jsonOptions(): int` which can be overridden in classes implementing the interface.

To make a `Storage` implement the `JsonCompatible` interface, you can use the trait `Philly\Support\JsonCompatible`.

### Storage
The `Storage` class provides a common baseline for all the types which hold data.
It also implements interfaces like [`Countable`](https://www.php.net/manual/class.countable.php) and [`JsonCompatible`](#jsoncompatible).

The implementation of the `Storage` class uses an `array` to store all the data.

## Storage Types
These types hold data. They expose different functions which are useful in specific contexts.

### Container
A `Container` can hold all kinds of data and extends the functionality of the `Storage` class.
It implements different interfaces like [`ArrayAccess`](https://www.php.net/manual/class.arrayaccess.php) or the [PSR-11 Container interface](https://www.php-fig.org/psr/psr-11/).

The `Container` also allows its children to filter the data (and keys) it accepts using the `Container::accepts($value)` and `Container::acceptsKey($key)` methods.

Many classes use the `Container` as a baseline and extend its functionality based on the use-case.

### BindingContainer
The `BindingContainer` extends the `Container` type and lets users bind builders or singletons to specific interfaces or names.

### Queue & Stack
The `Queue` and `Stack` classes are simple PHP implementations of common data types.
They extend the `Storage` class.

## ServiceProvider
The `ServiceProvider` class is an abstract class which provides a basis for all service providers used in the framework.
In the future, its implementations will include classes like `DatabaseServiceProvider`, `CacheServiceProvider` and others.

It basically has two methods, which can be overridden by implementations:

```php
/**
 * Interface ServiceProvider.
 */
interface ServiceProvider
{
    /**
     * Gets called from the service container upon service registration. Do every action and initialization your service
     * needs to function without accessing the service container.
     */
    public function onRegistered(): void;

    /**
     * Gets called after every service was registered.
     */
    public function onBooted(): void;

    [...]
}
```
