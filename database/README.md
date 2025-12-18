# 数据库表结构说明

## user_wallet 表

用户钱包绑定表，用于存储用户与EVM地址的绑定关系。

### 字段说明

| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | INT UNSIGNED | 主键ID，自增 |
| username | VARCHAR(100) | 用户名，唯一 |
| bind_data | TEXT | 绑定数据，可存储JSON格式或其他自定义数据 |
| address | VARCHAR(42) | EVM地址，42位（0x + 40个十六进制字符），唯一 |
| balance | DECIMAL(36,18) | 代币余额，支持18位小数，足够存储大额代币 |
| bnb_balance | DECIMAL(36,18) | BNB余额，支持18位小数，BSC链原生代币余额 |
| callback_url | VARCHAR(500) | 回调地址，用于接收转账通知 |
| redirect_url | VARCHAR(500) | 跳转地址，支付完成后的跳转地址 |
| create_time | DATETIME | 创建时间，自动设置 |
| update_time | DATETIME | 更新时间，自动更新 |
| status | TINYINT UNSIGNED | 状态：1-正常，0-禁用 |
| is_authorized | TINYINT UNSIGNED | 是否授权：1-已授权，0-未授权 |

### 索引说明

- **主键索引**: `id`
- **唯一索引**: `address` (地址唯一)
- **唯一索引**: `username` (用户名唯一)
- **普通索引**: `create_time` (用于时间查询)
- **普通索引**: `status` (用于状态筛选)
- **普通索引**: `is_authorized` (用于授权状态筛选)

### 使用示例

#### 插入数据
```sql
INSERT INTO `user_wallet` (`username`, `bind_data`, `address`, `balance`, `bnb_balance`, `callback_url`, `redirect_url`)
VALUES (
    'user001',
    '{"order_id": "12345", "amount": "100.00"}',
    '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb',
    '0.000000000000000000',
    '0.000000000000000000',
    'https://example.com/callback',
    'https://example.com/success'
);
```

#### 查询用户钱包
```sql
SELECT * FROM `user_wallet` WHERE `username` = 'user001';
```

#### 根据地址查询
```sql
SELECT * FROM `user_wallet` WHERE `address` = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb';
```

#### 更新代币余额
```sql
UPDATE `user_wallet` 
SET `balance` = '100.500000000000000000' 
WHERE `address` = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb';
```

#### 更新BNB余额
```sql
UPDATE `user_wallet` 
SET `bnb_balance` = '1.500000000000000000' 
WHERE `address` = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb';
```

#### 同时更新代币和BNB余额
```sql
UPDATE `user_wallet` 
SET `balance` = '100.500000000000000000',
    `bnb_balance` = '1.500000000000000000'
WHERE `address` = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb';
```

#### 授权用户
```sql
UPDATE `user_wallet` 
SET `is_authorized` = 1 
WHERE `username` = 'user001';
```

#### 查询已授权的用户
```sql
SELECT * FROM `user_wallet` WHERE `is_authorized` = 1;
```

#### 查询未授权的用户
```sql
SELECT * FROM `user_wallet` WHERE `is_authorized` = 0;
```

### 注意事项

1. **地址格式**: EVM地址必须是42位（0x + 40个十六进制字符），不区分大小写
2. **余额精度**: 使用 DECIMAL(36,18) 可以存储最大 999999999999999999.999999999999999999 的数值
3. **余额字段**: 
   - `balance`: 用于存储ERC20代币余额（如USDT、USDC等）
   - `bnb_balance`: 用于存储BNB余额（BSC链原生代币）
4. **唯一性**: 地址和用户名都是唯一的，不能重复
5. **bind_data**: 建议存储JSON格式数据，便于扩展
6. **字符集**: 使用 utf8mb4 以支持完整的Unicode字符

