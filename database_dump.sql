SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `shoujo_activity_array` (
  `shoujo_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `number` int(11) NOT NULL,
  `first_value` varchar(300) NOT NULL,
  `second_value` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shoujo_activity_array` (`shoujo_id`, `activity_id`, `type`, `number`, `first_value`, `second_value`) VALUES
(4, 10, 0, 0, 'I will go $SUBMENU_NAME then!', ''),
(4, 10, 1, 1, '300', ''),
(4, 10, 0, 2, 'That was fun', ''),
(4, 11, 0, 0, 'I will play $SUBMENU_NAME then!', ''),
(4, 11, 1, 1, '300', ''),
(4, 11, 2, 2, 'weight', '-5'),
(4, 11, 2, 3, 'entertainment', '5'),
(4, 11, 0, 4, 'That was fun', ''),
(4, 12, 0, 0, 'I guess it is time for a shower', ''),
(4, 12, 1, 1, '300', ''),
(4, 12, 2, 2, 'hygiene', '10'),
(4, 13, 0, 0, 'Baths are fun! Thanks!', ''),
(4, 13, 1, 1, '600', ''),
(4, 13, 2, 2, 'hygiene', '15'),
(4, 13, 2, 3, 'entertainment', '10'),
(4, 14, 0, 0, 'I will do that, then', ''),
(4, 14, 1, 1, '60', ''),
(4, 14, 2, 2, 'hygiene', '2'),
(4, 15, 0, 0, 'Ugh.. please don''t look', ''),
(4, 15, 1, 1, '120', ''),
(4, 15, 2, 2, 'hygiene', '3'),
(4, 16, 0, 0, 'Thanks, I love you too, best friend!', ''),
(4, 16, 0, 1, 'test atat', ''),
(4, 16, 0, 2, 'asgasgasgsa', ''),
(4, 17, 0, 0, 'Time for a quick sleep', ''),
(4, 17, 1, 1, '3600', ''),
(4, 17, 2, 2, 'tiredness', '-15'),
(4, 18, 0, 0, 'It is time to sleep I guess. Today was fun!', ''),
(4, 18, 1, 1, '7200', ''),
(4, 18, 2, 2, 'tiredness', '-40'),
(4, 19, 0, 0, 'I love going shopping!', ''),
(4, 19, 1, 1, '60', ''),
(4, 19, 2, 2, 'entertainment', '10'),
(4, 20, 0, 0, 'Ugh.. I hate this', ''),
(4, 20, 1, 1, '60', ''),
(4, 20, 2, 2, 'hygiene', '30'),
(4, 20, 2, 3, 'entertainment', '-10'),
(4, 22, 1, 0, '30', ''),
(4, 22, 2, 1, 'entertainment', '1');

CREATE TABLE IF NOT EXISTS `shoujo_activity_friendly_name` (
  `shoujo_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `friendly_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `shoujo_activity_image` (
  `shoujo_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `is_face` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `shoujo_activity_image` (`shoujo_id`, `activity_id`, `number`, `is_face`, `name`) VALUES
(4, 10, 0, 1, 'happy'),
(4, 10, 1, 1, ''),
(4, 10, 2, 1, ''),
(4, 11, 0, 1, ''),
(4, 11, 1, 1, ''),
(4, 11, 2, 1, ''),
(4, 11, 3, 1, ''),
(4, 11, 4, 1, ''),
(4, 12, 0, 1, ''),
(4, 12, 1, 1, ''),
(4, 12, 2, 1, ''),
(4, 13, 0, 1, ''),
(4, 13, 1, 1, ''),
(4, 13, 2, 1, ''),
(4, 13, 3, 1, ''),
(4, 14, 0, 1, ''),
(4, 14, 1, 1, ''),
(4, 14, 2, 1, ''),
(4, 15, 0, 1, ''),
(4, 15, 1, 1, ''),
(4, 15, 2, 1, ''),
(4, 16, 0, 1, ''),
(4, 16, 1, 1, ''),
(4, 16, 2, 1, ''),
(4, 17, 0, 1, ''),
(4, 17, 1, 1, ''),
(4, 17, 2, 1, ''),
(4, 18, 0, 1, ''),
(4, 18, 1, 1, ''),
(4, 18, 2, 1, ''),
(4, 19, 0, 1, ''),
(4, 19, 1, 1, ''),
(4, 19, 2, 1, ''),
(4, 20, 0, 1, ''),
(4, 20, 1, 1, ''),
(4, 20, 2, 1, ''),
(4, 20, 3, 1, ''),
(4, 22, 0, 1, ''),
(4, 22, 1, 1, '');

CREATE TABLE IF NOT EXISTS `shoujo_activity_options` (
  `shoujo_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `links_to` int(11) NOT NULL,
  `number` tinyint(11) NOT NULL,
  `text` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shoujo_common_activities` (
  `shoujo_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `activity_number` int(11) NOT NULL,
  `unique` tinyint(1) NOT NULL,
  `time_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_finish` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shoujo_events` (
  `shoujo_id` int(11) NOT NULL,
  `activity_number` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(256) NOT NULL,
  `start_date` date NOT NULL,
  `finish_date` date NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shoujo_images` (
  `shoujo_id` int(11) NOT NULL,
  `is_face` tinyint(1) NOT NULL,
  `name` varchar(128) NOT NULL,
  `extension` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shoujo_images` (`shoujo_id`, `is_face`, `name`, `extension`) VALUES
(4, 1, 'happy', 'png'),
(4, 0, 'default', 'jpg'),
(4, 1, 'default', 'png');

CREATE TABLE IF NOT EXISTS `shoujo_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(25) NOT NULL,
  `name` varchar(25) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `shoujo_info` (`id`, `owner`, `name`, `description`) VALUES
(4, 'pacha', 'Sophie', 'Test description');

CREATE TABLE IF NOT EXISTS `shoujo_menu` (
  `shoujo_id` int(11) NOT NULL,
  `time` smallint(6) NOT NULL DEFAULT '15',
  `once_per_day` tinyint(1) NOT NULL DEFAULT '0',
  `parent` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `text` varchar(35) NOT NULL,
  `activity_id` int(11) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shoujo_menu` (`shoujo_id`, `time`, `once_per_day`, `parent`, `number`, `type`, `text`, `activity_id`) VALUES
(4, 1, 0, 6, 11, 1, 'yogurth', 8),
(4, 1, 0, 6, 12, 1, 'chocolate milk', 9),
(4, 1, 0, 6, 13, 1, 'toast', 10),
(4, 1, 0, 6, 14, 1, 'cereal', 11),
(4, 2, 0, 6, 16, 1, 'caesar salad', 13),
(4, 2, 0, 6, 17, 1, 'chow mein', 14),
(4, 2, 0, 6, 18, 1, 'chicken nuggets', 15),
(4, 15, 0, 6, 19, 1, 'cookies', 16),
(4, 15, 0, 6, 20, 1, 'soda', 17),
(4, 15, 0, 6, 21, 1, 'fruit', 18),
(4, 8, 0, 6, 22, 1, 'taco', 19),
(4, 8, 0, 6, 23, 1, 'pasta', 20),
(4, 8, 0, 6, 24, 1, 'bbq ribs', 21),
(4, 8, 0, 6, 25, 1, 'cheeseburger', 22),
(4, 15, 0, 0, 6, 0, 'eating', -1),
(4, 15, 0, 7, 52, 1, 'shower', 12),
(4, 15, 0, 7, 53, 1, 'bath', 13),
(4, 7, 0, 7, 54, 1, 'tooth brushing', 14),
(4, 15, 0, 7, 55, 1, 'bathroom', 15),
(4, 15, 0, 0, 7, 0, 'hygiene', -1),
(4, 12, 0, 27, 33, 1, 'popular platformer 2', 30),
(4, 12, 0, 27, 34, 1, 'casual fighting game', 31),
(4, 12, 0, 27, 35, 1, 'adventure FPS trilogy', 32),
(4, 12, 0, 27, 36, 1, 'casual sport game', 33),
(4, 12, 0, 26, 27, 0, 'casual console', -1),
(4, 12, 0, 28, 41, 1, 'warrior quest VIII', 38),
(4, 12, 0, 28, 42, 1, 'tactical espionage simulator 3', 39),
(4, 12, 0, 28, 43, 1, 'rehashed JRPG X', 40),
(4, 12, 0, 28, 71, 1, 'rolling prince', 68),
(4, 12, 0, 26, 28, 0, 'older gen console', -1),
(4, 12, 0, 26, 29, 0, 'current gen console', -1),
(4, 15, 0, 44, 74, 1, '/v/', 71),
(4, 15, 0, 44, 75, 1, '/g/', 72),
(4, 15, 0, 44, 76, 1, '/a/', 73),
(4, 15, 0, 44, 77, 1, '/pol/', 74),
(4, 15, 0, 30, 44, 0, '4chan', -1),
(4, 15, 0, 30, 45, 0, 'tumblr', -1),
(4, 15, 0, 30, 46, 1, 'reddit', 43),
(4, 3, 0, 30, 47, 1, 'news', 44),
(4, 12, 0, 30, 48, 1, 'mindless browsing', 45),
(4, 15, 0, 26, 30, 0, 'PC', -1),
(4, 3, 0, 32, 37, 1, 'catching monsters Y version', 34),
(4, 3, 0, 32, 38, 1, 'casual karting', 35),
(4, 3, 0, 32, 39, 1, 'anthropomorphic social simulator', 36),
(4, 3, 0, 32, 40, 1, 'platformer rehash 3d', 37),
(4, 3, 0, 26, 32, 0, 'handheld console', -1),
(4, 12, 0, 49, 50, 1, 'new popular platformer', 47),
(4, 12, 0, 49, 51, 1, 'catching monsters white', 48),
(4, 12, 0, 49, 78, 1, 'warrior quest V remake', -1),
(4, 12, 0, 49, 79, 1, 'catching monsters gold remake', -1),
(4, 12, 0, 26, 49, 0, 'old handheld', -1),
(4, 15, 0, 0, 26, 0, 'entertainment', -1),
(4, 12, 0, 56, 57, 1, 'sleep', 18),
(4, 3, 0, 56, 58, 1, 'nap', 17),
(4, 15, 0, 56, 59, 1, 'clean house', 20),
(4, 15, 0, 56, 60, 1, 'do nothing', 22),
(4, 7, 0, 56, 72, 1, 'buy groceries', 19),
(4, 15, 0, 0, 56, 0, 'misc', -1),
(4, 3, 0, 61, 62, 1, 'jogging', 10),
(4, 3, 0, 61, 63, 1, 'cycling', 10),
(4, 1, 0, 61, 64, 1, 'morning walk', 61),
(4, 3, 0, 65, 66, 1, 'tennis', 63),
(4, 3, 0, 65, 67, 1, 'volleyball', 64),
(4, 3, 0, 65, 68, 1, 'swimming', 10),
(4, 3, 0, 65, 69, 1, 'fencing', 10),
(4, 3, 0, 65, 70, 1, 'soccer', 11),
(4, 3, 0, 61, 65, 0, 'sports', -1),
(4, 3, 0, 0, 61, 0, 'fitness', -1);

CREATE TABLE IF NOT EXISTS `shoujo_privilege` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `privilege` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shoujo_status` (
  `shoujo_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) NOT NULL,
  `decrease_time` int(11) NOT NULL,
  `decrease_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shoujo_status` (`shoujo_id`, `name`, `is_active`, `is_visible`, `decrease_time`, `decrease_amount`) VALUES
(4, 'hygiene', 1, 1, 1, 5),
(4, 'entertainment', 1, 1, 1, 5),
(4, 'weight', 1, 1, 1, 5),
(4, 'tiredness', 1, 1, 1, 5);

CREATE TABLE IF NOT EXISTS `shoujo_variables` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `value` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shoujo_variables` (`id`, `name`, `value`) VALUES
(4, 'ASFGASGASG', 'sagsag'),
(4, 'ASGFAS', 'asgasgasg');

CREATE TABLE IF NOT EXISTS `shoujo_xml_buffer` (
  `shoujo_id` int(11) NOT NULL,
  `xml` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `shoujo_xml_buffer` (`shoujo_id`, `xml`) VALUES
(4, '<?xml version="1.0"?>\n<shoujo><information><name>Sophie</name><description>Test description</description><id>4</id></information><status_collection><name>hygiene</name><name>entertainment</name><name>weight</name><name>tiredness</name></status_collection><activities><activity_array id="0"/><activity_array id="1"/><activity_array id="2"/><activity_array id="3"/><activity_array id="4"/><activity_array id="5"/><activity_array id="6"/><activity_array id="7"/><activity_array id="8"/><activity_array id="9"/><activity_array id="10"><activity number="0"><face>happy</face><type>0</type><text>I will go $SUBMENU_NAME then!</text></activity><activity number="1"><type>1</type><time>300</time></activity><activity number="2"><type>0</type><text>That was fun</text></activity></activity_array><activity_array id="11"><activity number="0"><type>0</type><text>I will play $SUBMENU_NAME then!</text></activity><activity number="1"><type>1</type><time>300</time></activity><activity number="2"><type>2</type><status>weight</status><value>-5</value></activity><activity number="3"><type>2</type><status>entertainment</status><value>5</value></activity><activity number="4"><type>0</type><text>That was fun</text></activity></activity_array><activity_array id="12"><activity number="0"><type>0</type><text>I guess it is time for a shower</text></activity><activity number="1"><type>1</type><time>300</time></activity><activity number="2"><type>2</type><status>hygiene</status><value>10</value></activity></activity_array><activity_array id="13"><activity number="0"><type>0</type><text>Baths are fun! Thanks!</text></activity><activity number="1"><type>1</type><time>600</time></activity><activity number="2"><type>2</type><status>hygiene</status><value>15</value></activity><activity number="3"><type>2</type><status>entertainment</status><value>10</value></activity></activity_array><activity_array id="14"><activity number="0"><type>0</type><text>I will do that, then</text></activity><activity number="1"><type>1</type><time>60</time></activity><activity number="2"><type>2</type><status>hygiene</status><value>2</value></activity></activity_array><activity_array id="15"><activity number="0"><type>0</type><text>Ugh.. please don''t look</text></activity><activity number="1"><type>1</type><time>120</time></activity><activity number="2"><type>2</type><status>hygiene</status><value>3</value></activity></activity_array><activity_array id="16"><activity number="0"><type>0</type><text>Thanks, I love you too, best friend!</text></activity><activity number="1"><type>0</type><text>test atat</text></activity><activity number="2"><type>0</type><text>asgasgasgsa</text></activity></activity_array><activity_array id="17"><activity number="0"><type>0</type><text>Time for a quick sleep</text></activity><activity number="1"><type>1</type><time>3600</time></activity><activity number="2"><type>2</type><status>tiredness</status><value>-15</value></activity></activity_array><activity_array id="18"><activity number="0"><type>0</type><text>It is time to sleep I guess. Today was fun!</text></activity><activity number="1"><type>1</type><time>7200</time></activity><activity number="2"><type>2</type><status>tiredness</status><value>-40</value></activity></activity_array><activity_array id="19"><activity number="0"><type>0</type><text>I love going shopping!</text></activity><activity number="1"><type>1</type><time>60</time></activity><activity number="2"><type>2</type><status>entertainment</status><value>10</value></activity></activity_array><activity_array id="20"><activity number="0"><type>0</type><text>Ugh.. I hate this</text></activity><activity number="1"><type>1</type><time>60</time></activity><activity number="2"><type>2</type><status>hygiene</status><value>30</value></activity><activity number="3"><type>2</type><status>entertainment</status><value>-10</value></activity></activity_array><activity_array id="22"><activity number="0"><type>1</type><time>30</time></activity><activity number="1"><type>2</type><status>entertainment</status><value>1</value></activity></activity_array></activities><events/><options><option><text>daily activities</text><number>0</number><time>15</time><type>0</type><options><option><text>eating</text><number>6</number><time>15</time><type>0</type><options><option><text>yogurth</text><number>11</number><time>1</time><type>1</type><activity_id>8</activity_id></option><option><text>chocolate milk</text><number>12</number><time>1</time><type>1</type><activity_id>9</activity_id></option><option><text>toast</text><number>13</number><time>1</time><type>1</type><activity_id>10</activity_id></option><option><text>cereal</text><number>14</number><time>1</time><type>1</type><activity_id>11</activity_id></option><option><text>caesar salad</text><number>16</number><time>2</time><type>1</type><activity_id>13</activity_id></option><option><text>chow mein</text><number>17</number><time>2</time><type>1</type><activity_id>14</activity_id></option><option><text>chicken nuggets</text><number>18</number><time>2</time><type>1</type><activity_id>15</activity_id></option><option><text>cookies</text><number>19</number><time>15</time><type>1</type><activity_id>16</activity_id></option><option><text>soda</text><number>20</number><time>15</time><type>1</type><activity_id>17</activity_id></option><option><text>fruit</text><number>21</number><time>15</time><type>1</type><activity_id>18</activity_id></option><option><text>taco</text><number>22</number><time>8</time><type>1</type><activity_id>19</activity_id></option><option><text>pasta</text><number>23</number><time>8</time><type>1</type><activity_id>20</activity_id></option><option><text>bbq ribs</text><number>24</number><time>8</time><type>1</type><activity_id>21</activity_id></option><option><text>cheeseburger</text><number>25</number><time>8</time><type>1</type><activity_id>22</activity_id></option></options></option><option><text>hygiene</text><number>7</number><time>15</time><type>0</type><options><option><text>shower</text><number>52</number><time>15</time><type>1</type><activity_id>12</activity_id></option><option><text>bath</text><number>53</number><time>15</time><type>1</type><activity_id>13</activity_id></option><option><text>tooth brushing</text><number>54</number><time>7</time><type>1</type><activity_id>14</activity_id></option><option><text>bathroom</text><number>55</number><time>15</time><type>1</type><activity_id>15</activity_id></option></options></option><option><text>entertainment</text><number>26</number><time>15</time><type>0</type><options><option><text>casual console</text><number>27</number><time>12</time><type>0</type><options><option><text>popular platformer 2</text><number>33</number><time>12</time><type>1</type><activity_id>30</activity_id></option><option><text>casual fighting game</text><number>34</number><time>12</time><type>1</type><activity_id>31</activity_id></option><option><text>adventure FPS trilogy</text><number>35</number><time>12</time><type>1</type><activity_id>32</activity_id></option><option><text>casual sport game</text><number>36</number><time>12</time><type>1</type><activity_id>33</activity_id></option></options></option><option><text>older gen console</text><number>28</number><time>12</time><type>0</type><options><option><text>warrior quest VIII</text><number>41</number><time>12</time><type>1</type><activity_id>38</activity_id></option><option><text>tactical espionage simulator 3</text><number>42</number><time>12</time><type>1</type><activity_id>39</activity_id></option><option><text>rehashed JRPG X</text><number>43</number><time>12</time><type>1</type><activity_id>40</activity_id></option><option><text>rolling prince</text><number>71</number><time>12</time><type>1</type><activity_id>68</activity_id></option></options></option><option><text>current gen console</text><number>29</number><time>12</time><type>0</type><options/></option><option><text>PC</text><number>30</number><time>15</time><type>0</type><options><option><text>4chan</text><number>44</number><time>15</time><type>0</type><options><option><text>/v/</text><number>74</number><time>15</time><type>1</type><activity_id>71</activity_id></option><option><text>/g/</text><number>75</number><time>15</time><type>1</type><activity_id>72</activity_id></option><option><text>/a/</text><number>76</number><time>15</time><type>1</type><activity_id>73</activity_id></option><option><text>/pol/</text><number>77</number><time>15</time><type>1</type><activity_id>74</activity_id></option></options></option><option><text>tumblr</text><number>45</number><time>15</time><type>0</type><options/></option><option><text>reddit</text><number>46</number><time>15</time><type>1</type><activity_id>43</activity_id></option><option><text>news</text><number>47</number><time>3</time><type>1</type><activity_id>44</activity_id></option><option><text>mindless browsing</text><number>48</number><time>12</time><type>1</type><activity_id>45</activity_id></option></options></option><option><text>handheld console</text><number>32</number><time>3</time><type>0</type><options><option><text>catching monsters Y version</text><number>37</number><time>3</time><type>1</type><activity_id>34</activity_id></option><option><text>casual karting</text><number>38</number><time>3</time><type>1</type><activity_id>35</activity_id></option><option><text>anthropomorphic social simulator</text><number>39</number><time>3</time><type>1</type><activity_id>36</activity_id></option><option><text>platformer rehash 3d</text><number>40</number><time>3</time><type>1</type><activity_id>37</activity_id></option></options></option><option><text>old handheld</text><number>49</number><time>12</time><type>0</type><options><option><text>new popular platformer</text><number>50</number><time>12</time><type>1</type><activity_id>47</activity_id></option><option><text>catching monsters white</text><number>51</number><time>12</time><type>1</type><activity_id>48</activity_id></option><option><text>warrior quest V remake</text><number>78</number><time>12</time><type>1</type><activity_id>-1</activity_id></option><option><text>catching monsters gold remake</text><number>79</number><time>12</time><type>1</type><activity_id>-1</activity_id></option></options></option></options></option><option><text>misc</text><number>56</number><time>15</time><type>0</type><options><option><text>sleep</text><number>57</number><time>12</time><type>1</type><activity_id>18</activity_id></option><option><text>nap</text><number>58</number><time>3</time><type>1</type><activity_id>17</activity_id></option><option><text>clean house</text><number>59</number><time>15</time><type>1</type><activity_id>20</activity_id></option><option><text>do nothing</text><number>60</number><time>15</time><type>1</type><activity_id>22</activity_id></option><option><text>buy groceries</text><number>72</number><time>7</time><type>1</type><activity_id>19</activity_id></option></options></option><option><text>fitness</text><number>61</number><time>3</time><type>0</type><options><option><text>jogging</text><number>62</number><time>3</time><type>1</type><activity_id>10</activity_id></option><option><text>cycling</text><number>63</number><time>3</time><type>1</type><activity_id>10</activity_id></option><option><text>morning walk</text><number>64</number><time>1</time><type>1</type><activity_id>61</activity_id></option><option><text>sports</text><number>65</number><time>3</time><type>0</type><options><option><text>tennis</text><number>66</number><time>3</time><type>1</type><activity_id>63</activity_id></option><option><text>volleyball</text><number>67</number><time>3</time><type>1</type><activity_id>64</activity_id></option><option><text>swimming</text><number>68</number><time>3</time><type>1</type><activity_id>10</activity_id></option><option><text>fencing</text><number>69</number><time>3</time><type>1</type><activity_id>10</activity_id></option><option><text>soccer</text><number>70</number><time>3</time><type>1</type><activity_id>11</activity_id></option></options></option></options></option></options></option><option><text>special activities</text><number>1</number><time>15</time><type>0</type><options/></option><option><text>status</text><number>2</number><time>15</time><type>3</type></option><option><text>events</text><number>3</number><time>15</time><type>2</type></option></options><images><faces><face>happy</face><face>default</face></faces><backgrounds><background>default</background></backgrounds></images><variables><variable><name>ASFGASGASG</name><value>sagsag</value></variable><variable><name>ASGFAS</name><value>asgasgasg</value></variable></variables></shoujo>\n');

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(16) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(16) NOT NULL,
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`username`, `email`, `password`, `name`) VALUES
('diana', 'a@a.com', '47820b81b9761d42501572c96d0e0e96', 'diana'),
('pacha', 'asd', '3020c4c611e1ca90661ecd48f3cd7a4b', 'IM A NIGGER'),
('sophie', '', '3020c4c611e1ca90661ecd48f3cd7a4b', 'Fernando');

CREATE TABLE IF NOT EXISTS `user_manage_lock` (
  `shoujo_id` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `time_start` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_shoujo_common_activities_done` (
  `shoujo_id` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `option` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_shoujo_events` (
  `shoujo_id` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_shoujo_playing` (
  `username` varchar(16) NOT NULL,
  `shoujo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_shoujo_playing` (`username`, `shoujo_id`) VALUES
('pacha', 4),
('sophie', 4),
('diana', 4);

CREATE TABLE IF NOT EXISTS `user_shoujo_status` (
  `name` varchar(25) NOT NULL,
  `shoujo_id` int(11) NOT NULL,
  `status` varchar(25) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_shoujo_status` (`name`, `shoujo_id`, `status`, `value`) VALUES
('pacha', 3, 'cock desire', -71);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
