-- 商户列表表
-- 用于存储商户信息和配置

CREATE TABLE IF NOT EXISTS `pay_merchant` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `uid` VARCHAR(6) NOT NULL COMMENT '商户UID（6位数字）',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码（加密存储）',
    `address` VARCHAR(42) DEFAULT NULL COMMENT '钱包地址（42位，包含0x前缀）',
    `key` TEXT COMMENT '私钥（加密存储）',
    `fee_rate` DECIMAL(10, 6) UNSIGNED NOT NULL DEFAULT '0.000000' COMMENT '手续费率（例如：0.001000 表示 0.1%）',
    `ip_whitelist` TEXT COMMENT 'IP白名单（JSON格式，例如：["127.0.0.1","192.168.1.1"]）',
    `remark` TEXT COMMENT '备注信息',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：1-正常，0-禁用',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_uid` (`uid`) COMMENT '商户UID唯一索引',
    UNIQUE KEY `uk_username` (`username`) COMMENT '用户名唯一索引',
    KEY `idx_status` (`status`) COMMENT '状态索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商户列表表';

