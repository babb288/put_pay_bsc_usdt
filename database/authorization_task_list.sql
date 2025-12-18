-- 授权任务列表查询SQL
-- 统计每个用户的未授权钱包数量、已授权钱包数量和任务状态

SELECT 
    `username` AS `用户名`,
    COUNT(CASE WHEN `is_authorized` = 0 THEN 1 END) AS `未授权钱包数量`,
    COUNT(CASE WHEN `is_authorized` = 1 THEN 1 END) AS `已授权钱包数量`,
    CASE 
        WHEN COUNT(CASE WHEN `is_authorized` = 0 THEN 1 END) = 0 
             AND COUNT(CASE WHEN `is_authorized` = 1 THEN 1 END) > 0 
        THEN '已完成'
        WHEN COUNT(CASE WHEN `is_authorized` = 0 THEN 1 END) > 0 
             AND COUNT(CASE WHEN `is_authorized` = 1 THEN 1 END) > 0 
        THEN '进行中'
        WHEN COUNT(CASE WHEN `is_authorized` = 0 THEN 1 END) > 0 
             AND COUNT(CASE WHEN `is_authorized` = 1 THEN 1 END) = 0 
        THEN '待处理'
        ELSE '未开始'
    END AS `任务状态`,
    COUNT(*) AS `总钱包数量`,
    MIN(`create_time`) AS `最早创建时间`,
    MAX(`update_time`) AS `最后更新时间`
FROM `user_wallet`
WHERE `status` = 1  -- 只统计正常状态的钱包
GROUP BY `username`
ORDER BY 
    `未授权钱包数量` DESC,  -- 优先显示未授权数量多的
    `username` ASC;

-- 如果需要创建授权任务表（可选）
-- CREATE TABLE IF NOT EXISTS `authorization_task` (
--     `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
--     `username` VARCHAR(100) NOT NULL COMMENT '用户名',
--     `unauthorized_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '未授权钱包数量',
--     `authorized_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '已授权钱包数量',
--     `total_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '总钱包数量',
--     `task_status` VARCHAR(20) NOT NULL DEFAULT '待处理' COMMENT '任务状态：已完成/进行中/待处理/未开始',
--     `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
--     `update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
--     PRIMARY KEY (`id`),
--     UNIQUE KEY `uk_username` (`username`) COMMENT '用户名唯一索引',
--     KEY `idx_task_status` (`task_status`) COMMENT '任务状态索引',
--     KEY `idx_unauthorized_count` (`unauthorized_count`) COMMENT '未授权数量索引'
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='授权任务表';

