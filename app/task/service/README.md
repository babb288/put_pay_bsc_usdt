# EVM Transfer日志扫描服务

这是一个使用 `web3p/web3.php` 库扫描EVM兼容链（如以太坊、BSC、Polygon等）上ERC20代币Transfer事件的服务类。

## 安装依赖

首先需要安装 `web3p/web3.php` 库：

```bash
composer require web3p/web3.php
```

或者手动添加到 `composer.json`：

```json
{
    "require": {
        "web3p/web3.php": "^1.0"
    }
}
```

然后运行：

```bash
composer install
```

## 功能特性

- ✅ 扫描指定区块范围的Transfer事件
- ✅ 扫描最新区块的Transfer事件
- ✅ 支持过滤特定合约地址
- ✅ 自动解析Transfer事件数据（from, to, value）
- ✅ 支持大数处理（使用BCMath）
- ✅ 代币数量格式化（支持自定义小数位数）
- ✅ 基于 web3p/web3.php 库，功能更强大

## 使用方法

### 1. 基本使用 - 扫描指定区块范围

```php
use app\task\service\EvmTransferScanner;

// 初始化扫描器（BSC主网）
$scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');

// 扫描区块 30000000 到 30000010 的所有Transfer事件
$transfers = $scanner->scanTransferLogs(30000000, 30000010);

foreach ($transfers as $transfer) {
    echo "合约: {$transfer['contract']}\n";
    echo "从: {$transfer['from']}\n";
    echo "到: {$transfer['to']}\n";
    echo "数量: {$transfer['value']}\n";
    echo "区块号: {$transfer['blockNumber']}\n";
    echo "交易哈希: {$transfer['transactionHash']}\n";
}
```

### 2. 扫描特定合约的Transfer事件

```php
// USDT合约地址（BSC）
$usdtContract = '0x55d398326f99059fF775485246999027B3197955';

$scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');

// 扫描最新10个区块中USDT的Transfer事件
$transfers = $scanner->scanLatestBlocks(10, $usdtContract);

foreach ($transfers as $transfer) {
    // 格式化代币数量（USDT是18位小数）
    $amount = $scanner->formatTokenAmount($transfer['value'], 18);
    echo "{$transfer['from']} -> {$transfer['to']}: {$amount} USDT\n";
}
```

### 3. 扫描最新区块

```php
$scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');

// 获取最新区块号
$latestBlock = $scanner->getLatestBlockNumber();
echo "最新区块号: {$latestBlock}\n";

// 扫描最新1个区块的所有Transfer事件
$transfers = $scanner->scanLatestBlocks(1);
```

### 4. 扫描以太坊主网

```php
// 使用以太坊主网RPC
$scanner = new EvmTransferScanner('https://eth.llamarpc.com');

$transfers = $scanner->scanLatestBlocks(5);
```

### 5. 高级操作 - 使用Web3实例

```php
$scanner = new EvmTransferScanner('https://bsc-dataseed.binance.org/');
$web3 = $scanner->getWeb3();

// 获取区块信息
$web3->eth->getBlockByNumber('latest', false, function ($err, $block) {
    if ($err !== null) {
        echo "错误: " . $err->getMessage() . "\n";
        return;
    }
    
    echo "区块哈希: {$block->hash}\n";
    echo "区块号: " . hexdec($block->number) . "\n";
});
```

## RPC节点地址

### BSC (Binance Smart Chain)
- 主网: `https://bsc-dataseed.binance.org/`
- 测试网: `https://data-seed-prebsc-1-s1.binance.org:8545/`

### 以太坊 (Ethereum)
- 主网: `https://eth.llamarpc.com` 或 `https://rpc.ankr.com/eth`
- Goerli测试网: `https://rpc.ankr.com/eth_goerli`

### Polygon
- 主网: `https://polygon-rpc.com/`
- Mumbai测试网: `https://rpc-mumbai.maticvigil.com/`

## Transfer事件数据结构

返回的每个Transfer事件包含以下字段：

```php
[
    'contract' => '0x...',           // 合约地址
    'from' => '0x...',               // 发送方地址
    'to' => '0x...',                 // 接收方地址
    'value' => '1000000000000000000', // 转账数量（原始值，wei单位）
    'valueHex' => '0x...',           // 十六进制格式的数量
    'blockNumber' => 30000000,        // 区块号
    'blockHash' => '0x...',          // 区块哈希
    'transactionHash' => '0x...',    // 交易哈希
    'transactionIndex' => 0,         // 交易在区块中的索引
    'logIndex' => 0,                 // 日志索引
    'removed' => false,              // 是否被移除
]
```

## 注意事项

1. **RPC限制**: 某些公共RPC节点可能有请求频率限制，建议使用自己的节点或付费RPC服务。

2. **区块范围**: 扫描大量区块可能会返回大量数据，注意内存使用。

3. **大数处理**: 代码使用BCMath扩展处理大数，如果没有安装BCMath扩展，大数可能会溢出。建议安装BCMath扩展：
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-bcmath
   
   # 或编译时启用
   --enable-bcmath
   ```

4. **错误处理**: 建议使用try-catch捕获异常。

5. **性能优化**: 对于频繁扫描，建议：
   - 使用WebSocket连接（需要额外实现）
   - 缓存区块数据
   - 使用队列异步处理

6. **web3p/web3.php**: 该库使用回调函数的方式处理异步操作，所有RPC调用都是异步的。

## 常见ERC20代币合约地址（BSC）

- USDT: `0x55d398326f99059fF775485246999027B3197955`
- USDC: `0x8AC76a51cc950d9822D68b83fE1Ad97B32Cd580d`
- BUSD: `0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56`
- ETH: `0x2170Ed0880ac9A755fd29B2688956BD959F933F8`

## 扩展功能

如果需要扫描其他事件（如Approval事件），可以扩展`EvmTransferScanner`类，添加新的扫描方法。

## 相关链接

- [web3p/web3.php GitHub](https://github.com/web3p/web3.php)
- [Web3.php 文档](https://github.com/web3p/web3.php)

