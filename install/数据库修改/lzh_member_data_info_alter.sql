ALTER TABLE `lzh_member_data_info`
ADD COLUMN `deal_image`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '后台处理图片' AFTER `ext`;