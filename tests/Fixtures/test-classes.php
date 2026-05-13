<?php

declare(strict_types=1);

namespace Topotru\ConditionalFinal\Psalm\Tests\Fixtures;

use Attribute;

// Placeholder Test Attributes
#[Attribute(Attribute::TARGET_CLASS)]
class CustomProxyRequired
{
}

#[Attribute(Attribute::TARGET_CLASS)]
class CustomAnotherForbidden
{
}

// Error: Class must be final or abstract
class ForgotFinalClass
{
}

// OK: Abstract class
abstract class SampleAbstract
{
}

// OK: Normal class with final
final class SampleFinal
{
}

// OK: Class with attribute (non-final - normal)
#[CustomProxyRequired]
class GoodFlexibleClass
{
}

// Error: Finalized a class requiring proxying
#[CustomProxyRequired]
final class BadFinalClass
{
}

// Error: Class finalized with second forbidden attribute
#[CustomAnotherForbidden]
final class AnotherBadFinalClass
{
}
