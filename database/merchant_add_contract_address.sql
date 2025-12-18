-- 为商户表添加合约地址字段
ALTER TABLE `pay_merchant` 
ADD COLUMN `contract_address` VARCHAR(42) DEFAULT NULL COMMENT '合约地址（42位，包含0x前缀）' 
AFTER `address`;

