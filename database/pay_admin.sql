-- 支付管理后台管理员表
-- 用于存储管理员账号信息

CREATE TABLE IF NOT EXISTS `pay_admin` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码（加密存储）',
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态：1-正常，0-禁用',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`) COMMENT '用户名唯一索引',
    KEY `idx_status` (`status`) COMMENT '状态索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='支付管理后台管理员表';

-- 插入默认管理员账号
-- 用户名: xiaodao
-- 密码: panzer12 (已加密)
INSERT INTO `pay_admin` (`username`, `password`, `status`) 
VALUES ('xiaodao', '$2y$10$xH7abhYuIwsxCOZs/h.3fuLLaUhjfdKGcfp0vmAa196be9olc0I.W', 1)
ON DUPLICATE KEY UPDATE `username` = `username`;

