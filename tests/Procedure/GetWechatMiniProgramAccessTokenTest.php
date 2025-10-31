<?php

namespace WechatMiniProgramBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatMiniProgramBundle\Procedure\GetWechatMiniProgramAccessToken;

/**
 * @internal
 */
#[CoversClass(GetWechatMiniProgramAccessToken::class)]
#[RunTestsInSeparateProcesses]
final class GetWechatMiniProgramAccessTokenTest extends AbstractProcedureTestCase
{
    protected const PROCEDURE_CLASS = GetWechatMiniProgramAccessToken::class;

    protected function onSetUp(): void
    {
        // 无需额外设置
    }

    public function testExecuteThrowsExceptionWhenAccountNotFound(): void
    {
        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $procedure->appId = 'wx_not_exists';

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到小程序');

        $procedure->execute();
    }

    public function testGetMockResult(): void
    {
        $result = GetWechatMiniProgramAccessToken::getMockResult();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertIsString($result['access_token']);
        $this->assertNotEmpty($result['access_token']);
    }

    public function testServiceInstantiation(): void
    {
        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $this->assertInstanceOf(GetWechatMiniProgramAccessToken::class, $procedure);
    }

    public function testHasRequiredProperties(): void
    {
        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasProperty('appId'));
        $this->assertTrue($reflection->hasProperty('refresh'));
    }

    public function testHasExecuteMethod(): void
    {
        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasMethod('execute'));

        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isPublic());
        $returnType = $method->getReturnType();
        if ($returnType instanceof \ReflectionNamedType) {
            $this->assertEquals('array', $returnType->getName());
        }
    }

    public function testHasMockResultMethod(): void
    {
        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $reflection = new \ReflectionClass($procedure);

        $this->assertTrue($reflection->hasMethod('getMockResult'));

        $method = $reflection->getMethod('getMockResult');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    public function testGenerateFormattedLogText(): void
    {
        // 创建真实的 JsonRpcRequest 对象
        $request = new JsonRpcRequest();

        // 使用反射设置私有属性
        $reflection = new \ReflectionClass($request);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($request, 'test-id');

        $methodProperty = $reflection->getProperty('method');
        $methodProperty->setAccessible(true);
        $methodProperty->setValue($request, 'test.method');

        $procedure = self::getService(GetWechatMiniProgramAccessToken::class);
        $result = $procedure->generateFormattedLogText($request);
        $this->assertSame('获取微信小程序AccessToken', $result);
    }
}
