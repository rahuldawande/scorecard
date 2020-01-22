CREATE TABLE `game` (
  `game_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `game_home` varchar(255) NOT NULL,
  `game_away` varchar(255) NOT NULL,
  `game_start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `game`
  ADD KEY `game_start` (`game_start`) USING BTREE;

CREATE TABLE `game_score` (
  `game_id` int(11) NOT NULL,
  `score_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score_home` int(8) NOT NULL DEFAULT '0',
  `score_away` int(8) NOT NULL DEFAULT '0',
  `score_comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `game_score`
  ADD PRIMARY KEY (`game_id`,`score_time`);