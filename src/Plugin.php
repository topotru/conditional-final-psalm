<?php

declare(strict_types=1);

namespace Topotru\ConditionalFinal\Psalm;

use Override;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;

final class Plugin implements PluginEntryPointInterface
{
    #[Override]
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        Handler::$forbiddenAttributes = $this->buildForbidden($config);
        $registration->registerHooksFromClass(Handler::class);
    }

    /**
     * @return string[]
     */
    private function buildForbidden(?SimpleXMLElement $config = null): array
    {
        $forbidden = [];

        if (null !== $config) {

            if (isset($config['useDoctrinePreset']) && 'true' === (string) $config['useDoctrinePreset']) {
                $forbidden = [
                    'Doctrine\ORM\Mapping\Entity',
                    'Doctrine\ORM\Mapping\MappedSuperclass',
                ];
            }

            $element = $config->forbiddenFinalAttributes ?? null;
            if (null !== $element && null !== $element->attribute) {
                foreach ($element->attribute as $attr) {
                    $forbidden[] = (string) $attr;
                }
            }
        }

        return $forbidden;
    }
}
