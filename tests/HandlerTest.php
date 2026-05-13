<?php

namespace Topotru\ConditionalFinal\Psalm\Tests;

use PHPUnit\Framework\TestCase;
use Override;

final class HandlerTest extends TestCase
{
    private string $psalmBin;
    private string $fixturesDir;

    #[Override]
    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/Fixtures';
        $this->psalmBin = dirname(__DIR__) . '/vendor/bin/psalm';

        if (!file_exists($this->psalmBin)) {
            $this->fail(sprintf('The Psalm binary was not found at the absolute path: %s', $this->psalmBin));
        }
    }

    public function testPluginIdentifiesArchitectureErrors(): void
    {
        $command = sprintf(
            'cd %s && %s --config=%s --no-cache --output-format=text',
            escapeshellarg($this->fixturesDir),
            escapeshellarg($this->psalmBin),
            escapeshellarg('psalm-test.xml')
        );

        $output = [];
        $resultCode = 0;
        exec($command . ' 2>&1', $output, $resultCode);

        $outputText = implode("\n", $output);

        $this->assertGreaterThan(
            0,
            $resultCode,
            'Psalm should exit with an error due to architecture violations. Output: ' . $outputText
        );

        $this->assertStringContainsString('ClassShouldBeFinal', $outputText);
        $this->assertStringContainsString('ForgotFinalClass', $outputText);
        $this->assertStringContainsString('ClassShouldNotBeFinal', $outputText);
        $this->assertStringContainsString('BadFinalClass', $outputText);
        $this->assertStringContainsString('AnotherBadFinalClass', $outputText);
    }
}
