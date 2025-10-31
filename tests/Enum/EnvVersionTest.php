<?php

namespace WechatMiniProgramBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatMiniProgramBundle\Enum\EnvVersion;

/**
 * @internal
 */
#[CoversClass(EnvVersion::class)]
final class EnvVersionTest extends AbstractEnumTestCase
{
    #[TestWith(['release', '正式版', EnvVersion::RELEASE])]
    #[TestWith(['trial', '体验版', EnvVersion::TRIAL])]
    #[TestWith(['develop', '开发版', EnvVersion::DEVELOP])]
    public function testValueAndLabel(string $expectedValue, string $expectedLabel, EnvVersion $enum): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testFromValidValue(): void
    {
        $this->assertSame(EnvVersion::RELEASE, EnvVersion::from('release'));
        $this->assertSame(EnvVersion::TRIAL, EnvVersion::from('trial'));
        $this->assertSame(EnvVersion::DEVELOP, EnvVersion::from('develop'));
    }

    public function testFromInvalidValueThrowsException(): void
    {
        $this->expectException(\ValueError::class);
        EnvVersion::from('invalid');
    }

    public function testTryFromValidValue(): void
    {
        $this->assertSame(EnvVersion::RELEASE, EnvVersion::tryFrom('release'));
        $this->assertSame(EnvVersion::TRIAL, EnvVersion::tryFrom('trial'));
        $this->assertSame(EnvVersion::DEVELOP, EnvVersion::tryFrom('develop'));
    }

    public function testTryFromInvalidValue(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull(EnvVersion::tryFrom('invalid'));
        // @phpstan-ignore-next-line
        $this->assertNull(EnvVersion::tryFrom(''));
        // @phpstan-ignore-next-line
        $this->assertNull(EnvVersion::tryFrom('unknown'));
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn ($case) => $case->value, EnvVersion::cases());
        $this->assertSame($values, array_unique($values), 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), EnvVersion::cases());
        $this->assertSame($labels, array_unique($labels), 'All enum labels must be unique');
    }

    public function testToArray(): void
    {
        $enum = EnvVersion::RELEASE;
        $array = $enum->toArray();
        // @phpstan-ignore-next-line
        $this->assertIsArray($array, '返回的数据必须是数组类型');
        $this->assertNotEmpty($array);

        // 验证包含所有预期的键值对
        $expectedPairs = [
            'release' => '正式版',
            'trial' => '体验版',
            'develop' => '开发版',
        ];

        foreach ($expectedPairs as $key => $label) {
            if (isset($array[$key])) {
                $this->assertEquals($label, $array[$key]);
            }
        }
    }
}
