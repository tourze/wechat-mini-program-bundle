<?php

namespace WechatMiniProgramBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatMiniProgramBundle\Enum\MiniProgramState;

/**
 * @internal
 */
#[CoversClass(MiniProgramState::class)]
final class MiniProgramStateTest extends AbstractEnumTestCase
{
    #[TestWith(['trial', '体验版', MiniProgramState::TRIAL])]
    #[TestWith(['developer', '开发版', MiniProgramState::DEVELOPER])]
    #[TestWith(['formal', '正式版', MiniProgramState::FORMAL])]
    public function testValueAndLabel(string $expectedValue, string $expectedLabel, MiniProgramState $enum): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testFromValidValue(): void
    {
        $this->assertSame(MiniProgramState::TRIAL, MiniProgramState::from('trial'));
        $this->assertSame(MiniProgramState::DEVELOPER, MiniProgramState::from('developer'));
        $this->assertSame(MiniProgramState::FORMAL, MiniProgramState::from('formal'));
    }

    public function testFromInvalidValueThrowsException(): void
    {
        $this->expectException(\ValueError::class);
        MiniProgramState::from('invalid');
    }

    public function testTryFromValidValue(): void
    {
        $this->assertSame(MiniProgramState::TRIAL, MiniProgramState::tryFrom('trial'));
        $this->assertSame(MiniProgramState::DEVELOPER, MiniProgramState::tryFrom('developer'));
        $this->assertSame(MiniProgramState::FORMAL, MiniProgramState::tryFrom('formal'));
    }

    public function testTryFromInvalidValue(): void
    {
        // @phpstan-ignore-next-line
        $this->assertNull(MiniProgramState::tryFrom('invalid'));
        // @phpstan-ignore-next-line
        $this->assertNull(MiniProgramState::tryFrom(''));
        // @phpstan-ignore-next-line
        $this->assertNull(MiniProgramState::tryFrom('unknown'));
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn ($case) => $case->value, MiniProgramState::cases());
        $this->assertSame($values, array_unique($values), 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), MiniProgramState::cases());
        $this->assertSame($labels, array_unique($labels), 'All enum labels must be unique');
    }

    public function testToArray(): void
    {
        $enum = MiniProgramState::TRIAL;
        $array = $enum->toArray();
        // @phpstan-ignore-next-line
        $this->assertIsArray($array, '返回的数据必须是数组类型');
        $this->assertNotEmpty($array);

        // 验证包含所有预期的键值对
        $expectedPairs = [
            'trial' => '体验版',
            'developer' => '开发版',
            'formal' => '正式版',
        ];

        foreach ($expectedPairs as $key => $label) {
            if (isset($array[$key])) {
                $this->assertEquals($label, $array[$key]);
            }
        }
    }
}
