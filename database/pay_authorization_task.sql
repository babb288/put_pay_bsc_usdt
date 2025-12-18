-- 授权任务表
-- 用于存储每个用户的授权任务统计信息

CREATE TABLE IF NOT EXISTS `pay_authorization_task` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `username` VARCHAR(100) NOT NULL COMMENT '用户名',
    `unauthorized_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '未授权钱包数量',
    `authorized_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '已授权钱包数量',
    `total_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '总钱包数量',
    `task_status` VARCHAR(20) NOT NULL DEFAULT '待处理' COMMENT '任务状态：已完成/进行中/待处理/未开始',
    `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`) COMMENT '用户名唯一索引',
    KEY `idx_task_status` (`task_status`) COMMENT '任务状态索引',
    KEY `idx_unauthorized_count` (`unauthorized_count`) COMMENT '未授权数量索引',
    KEY `idx_create_time` (`create_time`) COMMENT '创建时间索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='授权任务表';

