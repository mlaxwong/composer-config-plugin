<?php

declare(strict_types=1);

namespace Yiisoft\Composer\Config\Tests\Integration\Tests\Config;

use stdClass;
use Yiisoft\Composer\Config\Tests\Integration\Tests\Helper\LiterallyCallback;

abstract class ParamsConfigTest extends ConfigTest
{
    public function configProvider(): array
    {
        $objectWithClosures = new stdClass();
        $objectWithClosures->closure = function () {
            return 1;
        };
        $objectWithClosures->staticClosure = static function () {
            return 2;
        };
        $objectWithClosures->shortClosure = fn () => 3;
        $objectWithClosures->staticShortClosure = static fn () => 4;

        return [
            ['boolean parameter', true],
            ['string parameter', 'value of param 1'],
            ['NAN parameter', 'NAN'],
            ['float parameter', 1.0000001],
            ['int parameter', 123],
            ['long int parameter', 123_000],
            ['array parameter', [
                'changed value' => 'from root config',
                'first-vendor/first-package' => true,
                'first-vendor/second-package' => true,
                'second-vendor/first-package' => true,
                'second-vendor/second-package' => true,
                'third-vendor/first-package' => true,
                'third-vendor/second-package' => true,
                'third-vendor/third-package' => true,
                [[[[[]]]]],
            ]],
            ['array parameter with UnsetArrayValue', [
                'first-vendor/second-package' => true,
                'second-vendor/first-package' => true,
                'second-vendor/second-package' => true,
                'third-vendor/first-package' => true,
                'third-vendor/second-package' => true,
                'third-vendor/third-package' => true,
            ]],
            ['array parameter with ReplaceArrayValue', ['replace']],
            ['array parameter with RemoveArrayKeys', [
                'first-vendor/first-package',
                'first-vendor/second-package',
                'second-vendor/first-package',
                'second-vendor/second-package',
                'third-vendor/first-package',
                'third-vendor/second-package',
                'third-vendor/third-package',
                'root value',
            ]],
            ['array parameter with ReverseValues', [
                'root package' => 'root value',
                'third-vendor/third-package' => 'third-vendor/third-package',
                'third-vendor/second-package' => 'third-vendor/second-package',
                'third-vendor/first-package' => 'third-vendor/first-package',
                'second-vendor/second-package' => 'second-vendor/second-package',
                'second-vendor/first-package' => 'second-vendor/first-package',
                'first-vendor/second-package' => 'first-vendor/second-package',
                'first-vendor/first-package' => 'first-vendor/first-package',
            ]],
            [
                'callable parameter',
                new LiterallyCallback(function () {
                    return 'I am callable';
                }),
            ],
            [
                'static callable parameter',
                new LiterallyCallback(static function () {
                    return 'I am callable';
                }),
            ],
            ['short callable parameter', new LiterallyCallback(fn () => 'I am callable')],
            ['object parameter', new stdClass()],
            /**
             * Test for subpackages parameters
             */
            ['first-vendor/first-package', true],
            ['first-vendor/second-package', true],
            ['first-dev-vendor/first-package', true],
            ['first-dev-vendor/second-package', true],
            ['second-vendor/first-package', true],
            ['second-vendor/second-package', true],
            ['second-dev-vendor/first-package', true],
            ['second-dev-vendor/second-package', true],
            ['third-vendor/first-package', true],
            ['third-vendor/second-package', true],
            ['third-vendor/third-package', true],
            ['third-dev-vendor/first-package', true],
            ['third-dev-vendor/second-package', true],
            ['third-dev-vendor/third-package', true],
            ['constant_based_parameter', 'a constant value defined in config/constants.php'],
            ['constant_from_vendor', 'a constant value defined in first-dev-vendor/second-package'],
            ['env.raw', 'string'],
            ['env.raw.default_null', null],
            ['env.raw.default_string', 'default value'],
            ['env.raw.default_integer', 123],
            ['env.raw.default_object', new stdClass()],
            ['env.string', 'string'],
            ['env.number', '42'],
            ['env.text', 'Some text with several words'],
            ['env.params', [
                'first' => 'first substition',
                'deeper' => [
                    'second' => 'second substition',
                    'and' => [
                        'deepest' => 'deepest substition',
                    ],
                ],
            ]],
            ['parameters from .env', [
                'ENV_STRING' => 'string',
                'ENV_NUMBER' => '42',
                'ENV_TEXT' => 'Some text with several words',
            ]],
            ['parameters from .env through constants', [
                'ENV_STRING' => 'string',
                'ENV_NUMBER' => '42',
                'ENV_TEXT' => 'Some text with several words',
            ]],
            ['parameters from YAML', [
                'string value' => 'string',
                'boolean value' => true,
                'int value' => 42,
            ]],
            ['parameters from JSON', [
                'string value' => 'string',
                'boolean value' => true,
                'int value' => 42,
            ]],
            ['objectWithClosures', $objectWithClosures],
        ];
    }

    protected function getDefaultConfigName(): string
    {
        return 'params';
    }

    public function testConstants()
    {
        $values = $this->getConfigValue('parameters from .env through constants');
        foreach ($values as $k => $v) {
            $this->assertSame($v, constant($k));
        }
    }
}
