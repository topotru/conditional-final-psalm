<?php

declare(strict_types=1);

namespace Topotru\ConditionalFinal\Psalm\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

use function sprintf;

final class ClassShouldNotBeFinal extends CodeIssue
{
    public function __construct(string $name, string $attribute, CodeLocation $location)
    {
        parent::__construct(
            sprintf(
                'Class %s is marked by attribute "%s" and cannot be final.',
                $name,
                $attribute
            ),
            $location
        );
    }
}
