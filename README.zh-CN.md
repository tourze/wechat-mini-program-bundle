# Wechat Mini Program Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg?style=flat-square)](https://php.net)
[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-mini-program-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-mini-program-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-mini-program-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-mini-program-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-mini-program-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/wechat-mini-program-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?style=flat-square)]
(https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)]
(https://codecov.io/gh/tourze/php-monorepo)

微信小程序核心模块，为您的应用程序集成微信小程序功能的 Symfony Bundle。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
- [使用方法](#使用方法)
- [控制台命令](#控制台命令)
- [服务](#服务)
- [实体](#实体)
- [异常](#异常)
- [高级使用](#高级使用)
- [错误处理](#错误处理)
- [安全](#安全)
- [依赖关系](#依赖关系)
- [贡献](#贡献)
- [许可证](#许可证)
- [文档](#文档)

## 功能特性

- 微信小程序 API 客户端
- 小程序账户管理
- 访问令牌自动刷新和缓存
- 启动选项处理
- 调试和维护控制台命令
- 小程序集成实体特性
- API 访问的 JSON-RPC 过程

## 安装

```bash
composer require tourze/wechat-mini-program-bundle
```

## 配置

在您的 `bundles.php` 中添加 Bundle：

```php
<?php

return [
    // ... 其他 bundles
    WechatMiniProgramBundle\WechatMiniProgramBundle::class => ['all' => true],
];
```

## 控制台命令

### 获取访问令牌

为所有有效的小程序账户检索访问令牌：

```bash
php bin/console wechat-mini-program:get-access-token
```

此命令设计为每 20 分钟运行一次的定时任务，用于预取访问令牌，减少前端等待时间。

### 查询 RID 信息

查询用于调试目的的 RID 信息：

```bash
php bin/console wechat-mini-program:open-api:query-rid <account_id> <rid>
```

### 重置 API 频率

使用 AppSecret 重置 API 调用频率：

```bash
php bin/console wechat-mini-program:reset-api-frequency [account_id]
```

## 使用方法

### 基本客户端使用

```php
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramBundle\Request\YourRequest;

// 注入客户端
public function __construct(
    private readonly Client $client,
) {}

// 发起 API 请求
$request = new YourRequest();
$response = $this->client->request($request);
```

### 账户管理

```php
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;

// 获取账户仓储
public function __construct(
    private readonly AccountRepository $accountRepository,
) {}

// 查找有效账户
$accounts = $this->accountRepository->findBy(['valid' => true]);
```

### 启动选项

在您的实体中使用 `LaunchOptionsAware` 特性来处理小程序启动选项：

```php
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;

class YourEntity
{
    use LaunchOptionsAware;
    
    // 您的实体属性和方法
}
```

## 服务

### Client

用于向微信小程序 API 发起请求的主要 API 客户端：

- 自动访问令牌处理
- 令牌过期时自动刷新
- API 错误异常处理
- 缓存支持

### Account Service

用于管理小程序账户的服务：

- 账户验证
- 访问令牌检索
- 账户配置管理

### Launch Option Helper

用于处理小程序启动选项的辅助服务：

- 查询参数解析
- 启动选项验证
- 路径解析和处理

## 实体

### Account

代表微信小程序账户，包含：

- 基本账户信息（名称、appId、appSecret）
- 令牌和加密密钥存储
- 时间戳和责任追踪
- IP 追踪功能

## 异常

- `AccountNotFoundException`：当找不到请求的账户时抛出
- `WechatApiException`：当微信 API 返回错误时抛出
- `DecryptException`：当解密操作失败时抛出

## 高级使用

### 自定义 API 请求

通过实现 `RequestInterface` 创建自定义 API 请求：

```php
use HttpClientBundle\Request\RequestInterface;
use WechatMiniProgramBundle\Request\WithAccountRequest;

class CustomRequest extends WithAccountRequest implements RequestInterface
{
    public function getPath(): string
    {
        return '/your/custom/endpoint';
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getBody(): array
    {
        return [
            'custom_param' => 'value',
        ];
    }
}
```

### 环境配置

为不同环境配置不同的 API 端点：

```bash
# 禁用特定的微信 API 域名
WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api.weixin.qq.com=true
WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api2.weixin.qq.com=true
```

## 错误处理

Bundle 提供了全面的错误处理：

```php
use WechatMiniProgramBundle\Exception\WechatApiException;
use WechatMiniProgramBundle\Exception\AccountNotFoundException;

try {
    $response = $this->client->request($request);
} catch (WechatApiException $e) {
    // 处理微信 API 错误
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
} catch (AccountNotFoundException $e) {
    // 处理缺失账户错误
}
```

## 安全

### 访问令牌安全

- 访问令牌会自动缓存和刷新
- 令牌使用 Symfony 缓存组件安全存储
- AppSecret 值应存储为环境变量

### 最佳实践

- 在进行 API 调用前始终验证账户凭据
- 对所有 API 通信使用 HTTPS
- 定期轮换 AppSecret 值
- 监控 API 调用频率以避免速率限制

### 报告安全问题

如果您发现安全漏洞，请发送邮件至 security@example.com。
所有安全漏洞都将得到及时处理。

## 依赖关系

此 Bundle 需要以下包：

- `symfony/framework-bundle: ^7.3` - Symfony 核心框架
- `doctrine/orm: ^3.0` - 对象关系映射
- `doctrine/doctrine-bundle: ^2.13` - Doctrine 集成
- `tourze/http-client-bundle: 0.1.*` - HTTP 客户端功能
- `tourze/json-rpc-core: 0.0.*` - JSON-RPC 支持
- `tourze/backtrace-helper: 0.1.*` - 调试追踪工具
- `easycorp/easyadmin-bundle: ^4` - 管理界面支持

完整的依赖列表请参见 `composer.json` 文件。

## 贡献

1. Fork 仓库
2. 创建您的功能分支 (`git checkout -b feature/amazing-feature`)
3. 提交您的更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 开启 Pull Request

## 许可证

此 Bundle 基于 MIT 许可证授权。详情请参阅 [LICENSE](LICENSE) 文件。

## 文档

有关微信小程序开发的更多信息，请参考[官方文档](https://developers.weixin.qq.com/miniprogram/dev/)。