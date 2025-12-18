-- 钱包表
-- 用于存储用户钱包地址和余额信息

CREATE TABLE IF NOT EXISTS `pay_wallet` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `address` VARCHAR(42) NOT NULL COMMENT '钱包地址（42位，包含0x前缀）',
    `key` TEXT COMMENT '私钥（加密存储）',
    `type` ENUM('put', 'pay') NOT NULL COMMENT '类型：put-代收，pay-代付',
    `bnb_balance` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT 'BNB余额',
    `usdt_balance` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT 'USDT余额',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username_type` (`username`, `type`) COMMENT '用户名和类型组合唯一索引（同一用户每种类型只能有一个钱包）',
    KEY `idx_username` (`username`) COMMENT '用户名索引',
    KEY `idx_address` (`address`) COMMENT '钱包地址索引',
    KEY `idx_type` (`type`) COMMENT '类型索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包表';

