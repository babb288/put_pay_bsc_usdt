-- 授权明细表
-- 用于存储每个地址的授权流程详细信息

CREATE TABLE IF NOT EXISTS `pay_authorization_detail` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `address` VARCHAR(42) NOT NULL COMMENT '地址（42位，包含0x前缀）',
    `bnb_hash` VARCHAR(66) DEFAULT NULL COMMENT 'BNB转账交易哈希（66位，包含0x前缀）',
    `approve_hash` VARCHAR(66) DEFAULT NULL COMMENT '授权交易哈希（66位，包含0x前缀）',
    `is_bnb` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'BNB到账状态：1-已到账，0-未到账',
    `is_approve` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '授权状态：1-已授权，0-未授权',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`) COMMENT '用户名索引',
    KEY `idx_address` (`address`) COMMENT '地址索引',
    KEY `idx_bnb_hash` (`bnb_hash`) COMMENT 'BNB交易哈希索引',
    KEY `idx_approve_hash` (`approve_hash`) COMMENT '授权交易哈希索引',
    KEY `idx_is_bnb` (`is_bnb`) COMMENT 'BNB到账状态索引',
    KEY `idx_is_approve` (`is_approve`) COMMENT '授权状态索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='授权明细表';

