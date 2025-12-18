# 智能合约使用说明

## TokenTransferWithOwnable.sol

带权限管理的代币转账合约，支持转账到合约地址和提取功能。

### 主要功能

1. **转账到合约地址**
   - `transferFromToContract()` - 单笔转账到合约
   - `batchTransferFromToContract()` - 批量转账到合约
   - 接收地址固定为当前合约地址

2. **转账到指定地址**
   - `transferFrom()` - 单笔转账到指定地址
   - `batchTransferFrom()` - 批量转账到指定地址

3. **提取功能（仅Owner）**
   - `withdrawToken()` - 提取代币
   - `withdrawBnb()` - 提取BNB

4. **查询功能**
   - `checkAllowance()` - 检查授权额度
   - `checkBalance()` - 检查账户余额
   - `getContractTokenBalance()` - 查询合约代币余额
   - `getContractBnbBalance()` - 查询合约BNB余额

### 使用方法

#### 1. 部署合约

```bash
# 使用Remix、Hardhat或Truffle部署
# 部署后获得合约地址
```

#### 2. 授权代币

在调用transferFrom之前，用户需要先授权：

```javascript
// 使用Web3.js或ethers.js
const tokenContract = new web3.eth.Contract(ERC20_ABI, TOKEN_ADDRESS);
await tokenContract.methods.approve(CONTRACT_ADDRESS, AMOUNT).send({from: USER_ADDRESS});
```

#### 3. 调用转账

```javascript
const transferContract = new web3.eth.Contract(TRANSFER_ABI, CONTRACT_ADDRESS);

// 方式1: 转账到合约地址（推荐）
await transferContract.methods.transferFromToContract(
    TOKEN_ADDRESS,  // 代币地址
    FROM_ADDRESS,   // 转出地址
    AMOUNT          // 数量（wei单位）
).send({from: CALLER_ADDRESS});

// 方式2: 批量转账到合约地址
await transferContract.methods.batchTransferFromToContract(
    TOKEN_ADDRESS,
    [FROM1, FROM2, FROM3],  // 转出地址数组
    [AMOUNT1, AMOUNT2, AMOUNT3]  // 数量数组
).send({from: CALLER_ADDRESS});

// 方式3: 转账到指定地址
await transferContract.methods.transferFrom(
    TOKEN_ADDRESS,  // 代币地址
    FROM_ADDRESS,   // 转出地址
    TO_ADDRESS,     // 接收地址
    AMOUNT          // 数量（wei单位）
).send({from: CALLER_ADDRESS});

// 方式4: 批量转账到指定地址
await transferContract.methods.batchTransferFrom(
    TOKEN_ADDRESS,
    [FROM1, FROM2, FROM3],  // 转出地址数组
    [TO1, TO2, TO3],        // 接收地址数组
    [AMOUNT1, AMOUNT2, AMOUNT3]  // 数量数组
).send({from: CALLER_ADDRESS});
```

#### 4. 提取代币和BNB（仅Owner）

```javascript
// 提取代币（amount为0表示提取全部）
await transferContract.methods.withdrawToken(
    TOKEN_ADDRESS,  // 代币地址
    TO_ADDRESS,     // 接收地址
    AMOUNT          // 数量（0表示全部）
).send({from: OWNER_ADDRESS});

// 提取BNB（amount为0表示提取全部）
await transferContract.methods.withdrawBnb(
    TO_ADDRESS,     // 接收地址
    AMOUNT          // 数量（0表示全部）
).send({from: OWNER_ADDRESS});
```

#### 5. 查询余额

```javascript
// 查询合约代币余额
const tokenBalance = await transferContract.methods.getContractTokenBalance(TOKEN_ADDRESS).call();

// 查询合约BNB余额
const bnbBalance = await transferContract.methods.getContractBnbBalance().call();
```

### 使用场景

- **代币收集**: 使用 `transferFromToContract` 将代币收集到合约中
- **批量处理**: 使用批量方法提高效率
- **资金管理**: Owner可以提取合约中的代币和BNB
- **企业级应用**: 需要权限控制和资金管理的场景

## 安全注意事项

1. **授权检查**
   - 合约会检查授权额度
   - 确保有足够的授权才能转账

2. **地址验证**
   - 所有地址都会进行非零验证
   - 防止意外转账到零地址

3. **数量验证**
   - 转账数量必须大于0

4. **数组长度验证**
   - 批量转账时确保数组长度一致

## Gas优化

- 使用`calldata`而不是`memory`存储数组参数
- 批量转账可以节省gas费用
- 事件记录使用`indexed`关键字提高查询效率

## 部署建议

1. **测试网测试**
   - 先在BSC Testnet或Ethereum Goerli测试
   - 验证所有功能正常

2. **代码审计**
   - 生产环境部署前进行安全审计

3. **权限管理**
   - 如果使用Ownable版本，妥善保管Owner私钥

## 示例：PHP调用

```php
use Web3\Web3;
use Web3\Providers\HttpProvider;

// 初始化Web3
$web3 = new Web3(new HttpProvider('https://bsc-dataseed.binance.org/'));

// 合约地址和ABI
$contractAddress = '0x...'; // 合约地址
$contractABI = [...]; // 合约ABI

// 创建合约实例
$contract = $web3->eth->contract($contractABI)->at($contractAddress);

// 调用转账到合约
$contract->transferFromToContract(
    '0x55d398326f99059fF775485246999027B3197955', // USDT地址
    '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb', // 转出地址
    '1000000000000000000', // 1 USDT (18位小数)
    function($err, $result) {
        if ($err !== null) {
            echo "错误: " . $err->getMessage();
            return;
        }
        echo "交易哈希: " . $result;
    }
);

// Owner提取代币
$contract->withdrawToken(
    '0x55d398326f99059fF775485246999027B3197955', // USDT地址
    '0xOwnerAddress...', // 接收地址
    '0', // 0表示提取全部
    function($err, $result) {
        // 处理结果
    }
);
```

## 常见问题

### Q: 为什么需要授权？
A: ERC20标准要求，transferFrom需要from地址先授权给spender（本合约）。

### Q: 批量转账失败会怎样？
A: 如果任何一笔转账失败，整个批量转账会回滚，所有转账都不会执行。

### Q: 可以取消授权吗？
A: 可以，用户调用代币合约的approve方法，设置授权为0即可。

### Q: 合约可以升级吗？
A: 当前版本不支持升级，如需升级功能，可以使用代理模式（Proxy Pattern）。

