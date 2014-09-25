ALTER TABLE `lzh_prize_log`
ADD COLUMN `is_send`  tinyint(1) NULL DEFAULT 0 COMMENT '是否已寄出' AFTER `info`;
