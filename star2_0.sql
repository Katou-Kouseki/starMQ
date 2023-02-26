-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-02-23 21:24:21
-- 服务器版本： 5.7.39-log
-- PHP 版本： 7.4.33
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- 数据库： `star2_0`
--

-- --------------------------------------------------------
--
-- 表的结构 `star_admin`
--

CREATE TABLE `star_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `nickname` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '昵称',
  `username` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '用户名',
  `password` varchar(40) COLLATE utf8mb4_bin NOT NULL COMMENT '密码',
  `email` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '邮箱',
  `qq` bigint(10) DEFAULT '0' COMMENT 'QQ',
  `salt` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '密码盐',
  `token` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '登录令牌',
  `login_ip` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '登录IP',
  `login_time` bigint(10) DEFAULT '0' COMMENT '登录时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '管理员表';
--
-- 转存表中的数据 `star_admin`
--

INSERT INTO `star_admin` (
    `id`,
    `nickname`,
    `username`,
    `password`,
    `email`,
    `qq`,
    `salt`,
    `token`,
    `login_ip`,
    `login_time`
  )
VALUES (
    1,
    '深秋.',
    'admin',
    '45c28e4a98fa051b1f3fa37f2c0c1e84826c3460',
    'i@kain8.cn',
    1361582519,
    'starMQ-Pay_2.0_kaindev8-single-user',
    '',
    '',
    0
  );
-- --------------------------------------------------------
--
-- 表的结构 `star_code`
--

CREATE TABLE `star_code` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `url` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '二维码地址',
  `type` varchar(10) COLLATE utf8mb4_bin NOT NULL COMMENT '支付方式',
  `jk` int(1) DEFAULT '1' COMMENT '二维码监控类型; 1=app,0=PC',
  `time` bigint(10) NOT NULL COMMENT '添加时间',
  `status` int(1) DEFAULT '1' COMMENT '通道状态；1=启用,0=关闭'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '二维码表';
-- --------------------------------------------------------
--
-- 表的结构 `star_config`
--

CREATE TABLE `star_config` (
  `key` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '键',
  `val` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '值'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '设置表';
--
-- 转存表中的数据 `star_config`
--

INSERT INTO `star_config` (`key`, `val`)
VALUES ('app_heart', '0'),
  ('app_status', '0'),
  ('appid', '1000'),
  ('appkey', ''),
  ('beian', '湘ICP备888888888'),
  ('callback', '0'),
  ('close_time', '180'),
  ('desc', '2.0全新发布'),
  ('is_tips', '1'),
  ('pc_heart', '0'),
  ('pc_status', '0'),
  ('sitename', 'StarPay'),
  ('smtp_host', 'smtp.qq.com'),
  ('smtp_pass', ''),
  ('smtp_port', '465'),
  ('smtp_user', ''),
  ('tips', '客服QQ：1361582519，如支付后未跳转请联系客服!'),
  ('yuyin', '1');
-- --------------------------------------------------------
--
-- 表的结构 `star_log`
--

CREATE TABLE `star_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '操作IP',
  `time` bigint(10) NOT NULL COMMENT '操作时间',
  `event` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作事件',
  `status` int(10) DEFAULT '0' COMMENT '事件状态',
  `addres` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '地址'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '日志表';
-- --------------------------------------------------------
--
-- 表的结构 `star_order`
--

CREATE TABLE `star_order` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `create_time` bigint(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `pay_time` bigint(10) UNSIGNED NOT NULL COMMENT '支付时间',
  `out_trade_no` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '商户订单号',
  `trade_no` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '系统订单号',
  `name` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '商品名',
  `money` decimal(19, 2) NOT NULL COMMENT '订单金额',
  `really_money` decimal(19, 2) NOT NULL COMMENT '实付金额',
  `sitename` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '网站名称',
  `ip` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT 'IP',
  `return_url` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '同步通知地址',
  `notify_url` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '异步通知地址',
  `type` varchar(10) COLLATE utf8mb4_bin NOT NULL COMMENT '支付方式',
  `status` int(1) DEFAULT '0' COMMENT '支付状态'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '订单表';
--
-- 转储表的索引
--

--
-- 表的索引 `star_admin`
--
ALTER TABLE `star_admin`
ADD PRIMARY KEY (`id`);
--
-- 表的索引 `star_code`
--
ALTER TABLE `star_code`
ADD PRIMARY KEY (`id`);
--
-- 表的索引 `star_config`
--
ALTER TABLE `star_config`
ADD PRIMARY KEY (`key`);
--
-- 表的索引 `star_log`
--
ALTER TABLE `star_log`
ADD PRIMARY KEY (`id`);
--
-- 表的索引 `star_order`
--
ALTER TABLE `star_order`
ADD PRIMARY KEY (`id`);
--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `star_admin`
--
ALTER TABLE `star_admin`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  AUTO_INCREMENT = 2;
--
-- 使用表AUTO_INCREMENT `star_code`
--
ALTER TABLE `star_code`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `star_log`
--
ALTER TABLE `star_log`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
--
-- 使用表AUTO_INCREMENT `star_order`
--
ALTER TABLE `star_order`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;