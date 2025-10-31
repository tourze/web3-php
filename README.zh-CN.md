# Web3 PHP

[English](README.md) | [中文](README.zh-CN.md)

用于与以太坊区块链和 Web3 服务交互的 PHP 库。

## 简介

`tourze/web3-php` 是一个功能完整的 PHP 库，用于与以太坊区块链和 Web3 服务进行交互。它提供了简洁易用的 API，支持智能合约调用、交易发送、区块链数据查询等核心功能。

## 核心特性

- 完整的 Web3 RPC 接口支持
- 智能合约 ABI 编解码
- 支持 ERC20、ERC721 等标准代币
- 支持 HTTP 和 WebSocket 连接
- 批量请求支持
- 完整的数据格式化和验证
- 详细的错误处理机制
- 类型安全的 PHP 8+ 支持

## 安装

使用 Composer 安装：

```bash
composer require tourze/web3-php
```

## 快速开始

### 基础连接

```php
use Tourze\Web3PHP\Web3;

// 连接到本地节点
$web3 = new Web3('http://localhost:8545');

// 连接到 Infura
$web3 = new Web3('https://mainnet.infura.io/v3/YOUR_PROJECT_ID');
```

### 基本区块链操作

```php
// 获取最新区块号
$web3->eth->blockNumber(function ($err, $blockNumber) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    echo 'Block number: ' . $blockNumber . PHP_EOL;
});

// 获取账户余额
$web3->eth->getBalance('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3', function ($err, $balance) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    echo 'Balance: ' . $balance . PHP_EOL;
});
```

### 智能合约交互

```php
use Tourze\Web3PHP\Contract;

// ERC20 合约 ABI
$abi = json_decode(file_get_contents('erc20.abi.json'), true);

// 创建合约实例
$contract = new Contract($web3->provider, $abi);
$contract->at('0xCONTRACT_ADDRESS');

// 调用只读方法
$contract->call('balanceOf', '0xYOUR_ADDRESS', function ($err, $balance) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    echo 'Token balance: ' . $balance[0] . PHP_EOL;
});

// 发送交易方法
$contract->send('transfer', '0xTO_ADDRESS', 1000000000000000000, [
    'from' => '0xYOUR_ADDRESS',
    'gas' => '0x76c0',
    'gasPrice' => '0x9184e72a000',
], function ($err, $transactionHash) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    echo 'Transaction hash: ' . $transactionHash . PHP_EOL;
});
```

### 批量请求

```php
// 启用批量模式
$web3->batch(true);

// 添加多个请求
$web3->eth->blockNumber();
$web3->eth->getBalance('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');

// 执行批量请求
$web3->provider->execute(function ($err, $results) {
    if ($err !== null) {
        echo 'Error: ' . $err->getMessage();
        return;
    }
    echo 'Block number: ' . $results[0] . PHP_EOL;
    echo 'Balance: ' . $results[1] . PHP_EOL;
});
```

## 高级配置

### 自定义 Provider

```php
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

// 自定义请求管理器
$requestManager = new HttpRequestManager('http://localhost:8545', 30); // 30秒超时

// 自定义 Provider
$provider = new HttpProvider($requestManager);

// 使用自定义 Provider
$web3 = new Web3($provider);
```

### 工具函数

```php
use Tourze\Web3PHP\Utils;

// 转换单位: Ether 到 Wei
$wei = Utils::toWei('1', 'ether');
echo $wei; // 1000000000000000000

// 转换单位: Wei 到 Ether
$ether = Utils::fromWei('1000000000000000000', 'ether');
echo $ether; // 1

// 转换为十六进制
$hex = Utils::toHex('hello world');
echo $hex; // 0x68656c6c6f20776f726c64

// SHA3 哈希
$hash = Utils::sha3('hello world');
echo $hash;
```

### 地址验证

```php
use Tourze\Web3PHP\Utils;

// 验证地址格式
$isValid = Utils::isAddress('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');
echo $isValid ? 'Valid' : 'Invalid';

// 验证校验和地址
$isChecksumValid = Utils::isAddressChecksum('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');
echo $isChecksumValid ? 'Valid checksum' : 'Invalid checksum';
```

## 支持的方法

### Web3 方法
- `web3_clientVersion`
- `web3_sha3`

### Net 方法
- `net_version`
- `net_peerCount`
- `net_listening`

### Eth 方法
- `eth_protocolVersion`
- `eth_syncing`
- `eth_coinbase`
- `eth_mining`
- `eth_hashrate`
- `eth_gasPrice`
- `eth_accounts`
- `eth_blockNumber`
- `eth_getBalance`
- `eth_getStorageAt`
- `eth_getTransactionCount`
- `eth_getBlockTransactionCountByHash`
- `eth_getBlockTransactionCountByNumber`
- `eth_getUncleCountByBlockHash`
- `eth_getUncleCountByBlockNumber`
- `eth_getCode`
- `eth_sign`
- `eth_sendTransaction`
- `eth_sendRawTransaction`
- `eth_call`
- `eth_estimateGas`
- `eth_getBlockByHash`
- `eth_getBlockByNumber`
- `eth_getTransactionByHash`
- `eth_getTransactionByBlockHashAndIndex`
- `eth_getTransactionByBlockNumberAndIndex`
- `eth_getTransactionReceipt`
- `eth_getUncleByBlockHashAndIndex`
- `eth_getUncleByBlockNumberAndIndex`
- `eth_getCompilers`
- `eth_compileLLL`
- `eth_compileSolidity`
- `eth_compileSerpent`
- `eth_newFilter`
- `eth_newBlockFilter`
- `eth_newPendingTransactionFilter`
- `eth_uninstallFilter`
- `eth_getFilterChanges`
- `eth_getFilterLogs`
- `eth_getLogs`
- `eth_getWork`
- `eth_submitWork`
- `eth_submitHashrate`

### Personal 方法
- `personal_listAccounts`
- `personal_newAccount`
- `personal_unlockAccount`
- `personal_sendTransaction`

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/web3-php/tests
```

## 质量保证

- PHPStan Level 8 静态分析
- 测试覆盖率超过 90%
- 遵循 PSR-12 编码规范

## 贡献

欢迎提交 Issue 和 Pull Request！

## 许可证

MIT License