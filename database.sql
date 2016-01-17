DROP TABLE IF EXISTS `tbl_url`;
CREATE TABLE `tbl_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqid` varchar(32) NOT NULL,
  `blog_url` varchar(150) NOT NULL,
  `valid_post_url` varchar(200) NOT NULL,
  `xmlrpc_url` varchar(150) NOT NULL,
  `initial_ip` varchar(50) NOT NULL,
  `resolved_ip` varchar(50) NOT NULL,
  `status` enum('1','2','3','') NOT NULL DEFAULT '1' COMMENT '1 = processing, 2 = finish, 3 = error',
  `user_ip` varchar(50) NOT NULL COMMENT 'submitter ip address',
  `user_ua` text NOT NULL COMMENT 'submitter user agent',
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id` (`uniqid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
