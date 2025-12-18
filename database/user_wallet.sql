-- 用户钱包绑定表
-- 用于存储用户与EVM地址的绑定关系

CREATE TABLE IF NOT EXISTS `user_wallet` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `bind_data` TEXT COMMENT '绑定数据（JSON格式或其他自定义数据）',
    `address` VARCHAR(42) NOT NULL COMMENT 'EVM地址（42位，包含0x前缀）',
    `balance` DECIMAL(36, 18) UNSIGNED NOT NULL DEFAULT '0.000000000000000000' COMMENT '代币余额（支持18位小数）',
    `bnb_balance` DECIMAL(36, 18) UNSIGNED NOT NULL DEFAULT '0.000000000000000000' COMMENT 'BNB余额（支持18位小数）',
    `callback_url` VARCHAR(500) DEFAULT NULL COMMENT '回调地址',
    `redirect_url` VARCHAR(500) DEFAULT NULL COMMENT '跳转地址',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：1-正常，0-禁用',
    `is_authorized` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否授权：1-已授权，0-未授权',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_address` (`address`) COMMENT '地址唯一索引',
    UNIQUE KEY `uk_username` (`username`) COMMENT '用户名唯一索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引',
    KEY `idx_status` (`status`) COMMENT '状态索引',
    KEY `idx_is_authorized` (`is_authorized`) COMMENT '授权状态索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户钱包绑定表';

