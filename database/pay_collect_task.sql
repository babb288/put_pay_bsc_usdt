-- 归集任务表
-- 用于存储归集任务信息

CREATE TABLE IF NOT EXISTS `pay_collect_task` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '商户用户名',
    `wallet_count` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '钱包总数量',
    `collect_amount` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '归集金额',
    `contract_fee` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '合约服务费',
    `platform_fee` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '平台手续费',
    `collect_hash` VARCHAR(66) DEFAULT NULL COMMENT '归集哈希（交易哈希）',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '归集状态：0-待处理，1-进行中，2-成功，3-失败',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`) COMMENT '商户用户名索引',
    KEY `idx_status` (`status`) COMMENT '归集状态索引',
    KEY `idx_collect_hash` (`collect_hash`) COMMENT '归集哈希索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='归集任务表';

