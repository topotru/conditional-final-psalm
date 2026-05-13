<?php

declare(strict_types=1);

namespace Topotru\ConditionalFinal\Psalm\Tests;

use Override;
use PHPUnit\Framework\TestCase;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use Topotru\ConditionalFinal\Psalm\Handler;
use Topotru\ConditionalFinal\Psalm\Plugin;

final class PluginTest extends TestCase
{
    #[Override]
    protected function tearDown(): void
    {
        Handler::$forbiddenAttributes = [];
    }

    public function testDefaultEmptyConfig(): void
    {
        $registration = $this->createMock(RegistrationInterface::class);
        $plugin = new Plugin();

        $plugin($registration, null);

        $this->assertEmpty(Handler::$forbiddenAttributes);
    }

    public function testDoctrinePresetOption(): void
    {
        $registration = $this->createMock(RegistrationInterface::class);
        $plugin = new Plugin(); // Используем новое имя класса вместо ArchitecturePlugin

        $xml = new SimpleXMLElement('<pluginClass><useDoctrinePreset /></pluginClass>');

        $plugin($registration, $xml);

        $this->assertContains('Doctrine\ORM\Mapping\Entity', Handler::$forbiddenAttributes);
        $this->assertContains('Doctrine\ORM\Mapping\MappedSuperclass', Handler::$forbiddenAttributes);
    }

    public function testCustomAttributesParsing(): void
    {
        $registration = $this->createMock(RegistrationInterface::class);
        $plugin = new Plugin();

        $xml = new SimpleXMLElement('
            <pluginClass>
                <forbiddenFinalAttributes>
                    <attribute>Topotru\ConditionalFinal\Psalm\Tests\Fixtures\CustomProxyRequired</attribute>
                </forbiddenFinalAttributes>
            </pluginClass>
        ');

        $plugin($registration, $xml);

        $this->assertContains(
            'Topotru\ConditionalFinal\Psalm\Tests\Fixtures\CustomProxyRequired',
            Handler::$forbiddenAttributes
        );
    }
}
