<?php

declare(strict_types=1);

namespace Topotru\Psalm\ConditionalFinal\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

use function sprintf;

final class ClassShouldBeFinal extends CodeIssue
{
    public function __construct(string $name, CodeLocation $location)
    {
        parent::__construct(
            sprintf(
                'Class %s should be final or abstract.',
                $name
            ),
            $location
        );
    }
}
