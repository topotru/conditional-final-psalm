# Conditional Final for Psalm

Smart `final`/`abstract` class enforcement with attributes-based exclusions for Psalm.

[![Latest Stable Version](https://shields.io)](https://packagist.org)
[![License](https://shields.io)](https://packagist.org)

Enforce `final` or `abstract` on your PHP classes without breaking your **Doctrine Entities** or other proxy-reliant classes.

This plugin replaces dumb token-based linters (like PHPCS) with smart, attributes-aware architectural control on top of the Psalm static analysis engine.

## The Problem

Standard linters (e.g., `SlevomatCodingStandard.Classes.RequireAbstractOrFinal`) force you to make every class `final`. However, **Doctrine Entities** (or MappedSuperclasses) **must not be final** because Doctrine needs to extend them to generate lazy-loading proxy classes at runtime.

If you accidentally make an Entity `final`, it usually works fine in `dev` environment but **crashes with a Fatal Error on production**. To avoid this, you are forced to litter your codebase with ugly comments:

```php
#[ORM\Entity]
// phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal
class User {} // Annoying and error-prone!
```

## The Solution

**Conditional Final** reverses the logic:
1. Every class **must** be `final` or `abstract` by default.
2. If a class has a forbidden attribute (like `#[ORM\Entity]`), it **must not** be `final` (protects your production).
3. Completely config-driven. No more inline ignore comments!
4. Zero-dependency core (does not require `doctrine/orm` to be installed).

## Installation

```bash
composer require --dev topotru/psalm-conditional-final
```

Enable the plugin in your `psalm.xml`:

```bash
vendor/bin/psalm-plugin enable topotru/psalm-conditional-final
```

## Configuration

By default, the plugin requires all classes to be `final` or `abstract` and has an empty exclusion list.

### Integration with Doctrine ORM

To enable the built-in preset for Doctrine (`#[Entity]` and `#[MappedSuperclass]`), simply add the `<useDoctrinePreset />` tag inside the plugin configuration in your `psalm.xml`:

```xml
<plugins>
    <pluginClass class="Topotru\Psalm\ConditionalFinal\Plugin">
        <useDoctrinePreset />
    </pluginClass>
</plugins>
```

### Custom Configurations

You can add any custom proxy or framework attributes (like API Platform or custom annotations) to the exclusion list manually inside the `forbiddenFinalAttributes` section:

```xml
<plugins>
    <pluginClass class="Topotru\Psalm\ConditionalFinal\Plugin">
        <useDoctrinePreset />
        <forbiddenFinalAttributes>
            <attribute>App\Attributes\CustomProxy</attribute>
            <attribute>ApiPlatform\Metadata\ApiResource</attribute>
        </forbiddenFinalAttributes>
    </pluginClass>
</plugins>
```

## Errors Handled

* `ClassShouldNotBeFinal` — Triggers when an entity/proxy class is accidentally marked as `final` (prevents production crashes).
* `ClassShouldBeFinal` — Triggers when a standard class (service, repository, etc.) misses the `final` keyword.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
