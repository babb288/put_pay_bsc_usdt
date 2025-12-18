-- 下发明细表
-- 用于记录每次代收钱包一键下发的详细信息

CREATE TABLE IF NOT EXISTS `pay_send_detail` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '商户用户名',
    `wallet_id` INT UNSIGNED NOT NULL COMMENT '代收钱包ID（pay_wallet.id）',
    `wallet_address` VARCHAR(42) NOT NULL COMMENT '代收钱包地址（0x开头，42位）',
    `to_address` VARCHAR(42) NOT NULL COMMENT '下发地址（商户下发地址）',
    `token_symbol` VARCHAR(20) NOT NULL DEFAULT 'USDT' COMMENT '代币符号，如USDT',
    `amount` DECIMAL(20, 8) UNSIGNED NOT NULL DEFAULT '0.00000000' COMMENT '下发金额',
    `txid` VARCHAR(66) DEFAULT NULL COMMENT '下发交易哈希（66位，0x前缀）',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：0-待发送，1-成功，2-失败',
    `remark` VARCHAR(255) DEFAULT NULL COMMENT '备注信息（失败原因等）',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`) COMMENT '商户用户名索引',
    KEY `idx_wallet_id` (`wallet_id`) COMMENT '钱包ID索引',
    KEY `idx_wallet_address` (`wallet_address`) COMMENT '代收钱包地址索引',
    KEY `idx_to_address` (`to_address`) COMMENT '下发地址索引',
    KEY `idx_txid` (`txid`) COMMENT '交易哈希索引',
    KEY `idx_status` (`status`) COMMENT '状态索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代收钱包下发明细表';


