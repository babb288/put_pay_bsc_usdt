-- 如果表已存在，使用此SQL添加字段

-- 添加 is_authorized 字段
ALTER TABLE `user_wallet` 
ADD COLUMN `is_authorized` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否授权：1-已授权，0-未授权' 
AFTER `status`;

-- 添加授权状态索引
ALTER TABLE `user_wallet` 
ADD KEY `idx_is_authorized` (`is_authorized`) COMMENT '授权状态索引';

-- 添加 bnb_balance 字段（BNB余额）
ALTER TABLE `user_wallet` 
ADD COLUMN `bnb_balance` DECIMAL(36, 18) UNSIGNED NOT NULL DEFAULT '0.000000000000000000' COMMENT 'BNB余额（支持18位小数）' 
AFTER `balance`;

