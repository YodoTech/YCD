ALTER TABLE `lzh_members`
ADD COLUMN `user_role`  tinyint(3) UNSIGNED NOT NULL DEFAULT 1 AFTER `user_pass`;