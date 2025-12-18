-- 订单表
-- 用于存储订单信息

CREATE TABLE IF NOT EXISTS `pay_apply` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `uid` VARCHAR(50) DEFAULT NULL COMMENT '用户UID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `system_order` VARCHAR(100) NOT NULL COMMENT '系统订单号',
    `merchant_order` VARCHAR(100) DEFAULT NULL COMMENT '商户订单号',
    `network` VARCHAR(20) NOT NULL DEFAULT 'bsc' COMMENT '网络类型（bsc等）',
    `type` VARCHAR(20) NOT NULL DEFAULT 'usdt' COMMENT '类型（usdt）',
    `pay_address` VARCHAR(42) NOT NULL COMMENT '支付地址（接收方地址）',
    `address` VARCHAR(42) NOT NULL COMMENT '钱包地址（发送方地址）',
    `price` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '订单金额',
    `body` VARCHAR(200) DEFAULT NULL COMMENT '订单描述',
    `txid` VARCHAR(66) DEFAULT NULL COMMENT '交易哈希（txid）',
    `result` TEXT DEFAULT NULL COMMENT '结果信息（JSON格式或其他）',
    `status` TINYINT NOT NULL DEFAULT '0' COMMENT '订单状态：0-等待中，1-订单完成，-1-处理失败，2-等待通知，3-通知失败，4-未审订单，-2-已拒订单',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_system_order` (`system_order`) COMMENT '系统订单号唯一索引',
    KEY `idx_uid` (`uid`) COMMENT '用户UID索引',
    KEY `idx_username` (`username`) COMMENT '用户名索引',
    KEY `idx_merchant_order` (`merchant_order`) COMMENT '商户订单号索引',
    KEY `idx_status` (`status`) COMMENT '订单状态索引',
    KEY `idx_txid` (`txid`) COMMENT '交易哈希索引',
    KEY `idx_address` (`address`) COMMENT '钱包地址索引',
    KEY `idx_pay_address` (`pay_address`) COMMENT '支付地址索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引',
    KEY `idx_network_type` (`network`, `type`) COMMENT '网络和类型组合索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';

