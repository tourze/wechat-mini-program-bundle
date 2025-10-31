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

A Symfony bundle for integrating WeChat Mini Program functionality into your application.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Console Commands](#console-commands)
- [Services](#services)
- [Entities](#entities)
- [Exceptions](#exceptions)
- [Advanced Usage](#advanced-usage)
- [Error Handling](#error-handling)
- [Security](#security)
- [Dependencies](#dependencies)
- [Contributing](#contributing)
- [License](#license)
- [Documentation](#documentation)

## Features

- WeChat Mini Program API client
- Account management for Mini Program accounts
- Access token automatic refresh and caching
- Launch options handling
- Console commands for debugging and maintenance
- Entity traits for Mini Program integration
- JSON-RPC procedures for API access

## Installation

```bash
composer require tourze/wechat-mini-program-bundle
```

## Configuration

Add the bundle to your `bundles.php`:

```php
<?php

return [
    // ... other bundles
    WechatMiniProgramBundle\WechatMiniProgramBundle::class => ['all' => true],
];
```

## Console Commands

### Get Access Token

Retrieve access tokens for all valid Mini Program accounts:

```bash
php bin/console wechat-mini-program:get-access-token
```

This command is designed to run as a cron job every 20 minutes to pre-fetch access tokens, reducing frontend wait times.

### Query RID Information

Query RID information for debugging purposes:

```bash
php bin/console wechat-mini-program:open-api:query-rid <account_id> <rid>
```

### Reset API Frequency

Reset API call frequency using AppSecret:

```bash
php bin/console wechat-mini-program:reset-api-frequency [account_id]
```

## Usage

### Basic Client Usage

```php
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramBundle\Request\YourRequest;

// Inject the client
public function __construct(
    private readonly Client $client,
) {}

// Make API requests
$request = new YourRequest();
$response = $this->client->request($request);
```

### Account Management

```php
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;

// Get account repository
public function __construct(
    private readonly AccountRepository $accountRepository,
) {}

// Find valid accounts
$accounts = $this->accountRepository->findBy(['valid' => true]);
```

### Launch Options

Use the `LaunchOptionsAware` trait in your entities to handle Mini Program launch options:

```php
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;

class YourEntity
{
    use LaunchOptionsAware;
    
    // Your entity properties and methods
}
```

## Services

### Client

The main API client for making requests to WeChat Mini Program APIs:

- Automatic access token handling
- Token refresh on expiration
- Exception handling for API errors
- Caching support

### Account Service

Service for managing Mini Program accounts:

- Account validation
- Access token retrieval
- Account configuration management

### Launch Option Helper

Helper service for processing Mini Program launch options:

- Query parameter parsing
- Launch option validation
- Path parsing and handling

## Entities

### Account

Represents a WeChat Mini Program account with:

- Basic account information (name, appId, appSecret)
- Token and encryption key storage
- Timestamps and blame tracking
- IP tracing capabilities

## Exceptions

- `AccountNotFoundException`: Thrown when a requested account cannot be found
- `WechatApiException`: Thrown when WeChat API returns an error
- `DecryptException`: Thrown when decryption operations fail

## Advanced Usage

### Custom API Requests

Create custom API requests by implementing the `RequestInterface`:

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

### Environment Configuration

Configure different API endpoints for different environments:

```bash
# Disable specific WeChat API domains
WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api.weixin.qq.com=true
WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api2.weixin.qq.com=true
```

## Error Handling

The bundle provides comprehensive error handling:

```php
use WechatMiniProgramBundle\Exception\WechatApiException;
use WechatMiniProgramBundle\Exception\AccountNotFoundException;

try {
    $response = $this->client->request($request);
} catch (WechatApiException $e) {
    // Handle WeChat API errors
    $errorCode = $e->getCode();
    $errorMessage = $e->getMessage();
} catch (AccountNotFoundException $e) {
    // Handle missing account errors
}
```

## Security

### Access Token Security

- Access tokens are automatically cached and refreshed
- Tokens are stored securely using Symfony's cache component
- AppSecret values should be stored as environment variables

### Best Practices

- Always validate account credentials before making API calls
- Use HTTPS for all API communications
- Regularly rotate AppSecret values
- Monitor API call frequency to avoid rate limiting

### Reporting Security Issues

If you discover a security vulnerability, please send an email to security@example.com.
All security vulnerabilities will be promptly addressed.

## Dependencies

This bundle requires the following packages:

- `symfony/framework-bundle: ^7.3` - Core Symfony framework
- `doctrine/orm: ^3.0` - Object-relational mapping
- `doctrine/doctrine-bundle: ^2.13` - Doctrine integration
- `tourze/http-client-bundle: 0.1.*` - HTTP client functionality
- `tourze/json-rpc-core: 0.0.*` - JSON-RPC support
- `tourze/backtrace-helper: 0.1.*` - Debug tracing utilities
- `easycorp/easyadmin-bundle: ^4` - Admin interface support

For a complete list of dependencies, see the `composer.json` file.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This bundle is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Documentation

For more information about WeChat Mini Program development, refer to the 
[official documentation](https://developers.weixin.qq.com/miniprogram/dev/).