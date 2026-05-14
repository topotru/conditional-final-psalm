<?php

declare(strict_types=1);

namespace Topotru\Psalm\ConditionalFinal;

use Override;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterClassLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeAnalysisEvent;
use Psalm\Storage\AttributeStorage;
use Psalm\Storage\ClassLikeStorage;
use Topotru\Psalm\ConditionalFinal\Issue\ClassShouldBeFinal;
use Topotru\Psalm\ConditionalFinal\Issue\ClassShouldNotBeFinal;

use function ltrim;
use function str_contains;

final class Handler implements AfterClassLikeAnalysisInterface
{
    /**
     * @var string[]
     */
    public static array $forbiddenAttributes = [];

    #[Override]
    public static function afterStatementAnalysis(AfterClassLikeAnalysisEvent $event): null
    {
        $storage = $event->getClasslikeStorage();

        if (self::isSkipped($storage)) {
            return null;
        }

        $location = $storage->location;
        if (null === $location) {
            return null;
        }

        $forbiddenAttribute = self::getForbiddenAttribute($storage->attributes);

        self::checkEntityShouldNotBeFinal(
            $forbiddenAttribute,
            $storage,
            $location
        );

        self::checkClassShouldBeFinal(
            $forbiddenAttribute,
            $storage,
            $location
        );

        return null;
    }

    private static function isSkipped(ClassLikeStorage $storage): bool
    {
        return
            $storage->is_interface ||
            $storage->is_enum ||
            $storage->abstract ||
            str_contains($storage->name, '@') ||
            str_contains($storage->name, '{');
    }

    private static function checkClassShouldBeFinal(
        ?string $forbiddenAttribute,
        ClassLikeStorage $storage,
        CodeLocation $location
    ): void {
        if (null === $forbiddenAttribute && !$storage->final) {
            IssueBuffer::accepts(
                new ClassShouldBeFinal($storage->name, $location),
                $storage->suppressed_issues
            );
        }
    }

    private static function checkEntityShouldNotBeFinal(
        ?string $forbiddenAttribute,
        ClassLikeStorage $storage,
        CodeLocation $location
    ): void {
        if (null !== $forbiddenAttribute && $storage->final) {
            IssueBuffer::accepts(
                new ClassShouldNotBeFinal($storage->name, $forbiddenAttribute, $location),
                $storage->suppressed_issues
            );
        }
    }

    /**
     * @param list<AttributeStorage> $attributes
     */
    private static function getForbiddenAttribute(array $attributes): ?string
    {
        foreach ($attributes as $attribute) {
            $fqName = $attribute->fq_class_name;

            $presentAttr = ltrim($fqName, '\\');

            foreach (self::$forbiddenAttributes as $forbiddenAttr) {
                $normalizedForbidden = ltrim($forbiddenAttr, '\\');

                if ($presentAttr === $normalizedForbidden) {
                    return $presentAttr;
                }
            }
        }

        return null;
    }
}
