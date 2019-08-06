-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 02, 2019 at 08:53 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `RHFILES`
--

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE `Category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`category_id`, `name`, `description`) VALUES
(1, 'Sport', 'Contains all sports related skills'),
(2, 'Creative', 'Contains all creative related skills'),
(3, 'Language', 'Contains all language related skills'),
(4, 'Academic', 'Contains all academic related skills'),
(5, 'Film & Media', 'Contains all Film related skills'),
(6, 'Cooking', 'Contains all cooking related skills'),
(7, 'Construction', 'Contains all Construction related skills'),
(8, 'Days Out', 'Contains all Days out'),
(9, 'Miscellaneous', 'Contains anything that does not fall into the above categories');

-- --------------------------------------------------------

--
-- Table structure for table `Initial_Skill`
--

CREATE TABLE `Initial_Skill` (
  `initial_skill_id` int(11) NOT NULL,
  `user_skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE `Location` (
  `location_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`location_id`, `name`) VALUES
(1, 'Founders'),
(2, 'Reid'),
(3, 'Englefield Green'),
(4, 'Williamson'),
(5, 'Tuke'),
(6, 'Butler'),
(7, 'George Eliot'),
(8, 'Another Galaxy (off campus)');

-- --------------------------------------------------------

--
-- Table structure for table `Offer_Skill`
--

CREATE TABLE `Offer_Skill` (
  `offer_skill_id` int(11) NOT NULL,
  `user_skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Profile`
--

CREATE TABLE `Profile` (
  `user_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT 'About me',
  `score` int(11) NOT NULL DEFAULT '0',
  `trade_count` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL,
  `profile_pic_added` tinyint(1) NOT NULL DEFAULT '0',
  `year` enum('First','Second','Third','Fourth','Masters','PhD') NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Profile`
--

INSERT INTO `Profile` (`user_id`, `description`, `score`, `trade_count`, `location_id`, `profile_pic_added`, `year`, `first_name`, `last_name`) VALUES
(281, 'Welcome to my page', 10, 2, 1, 0, 'First', 'Joe', 'Bloggs'),
(282, 'Welcome to my page', 10, 2, 1, 0, 'First', 'Youcef', 'Adoum'),
(283, 'Welcome to my page', 0, 0, 1, 0, 'First', 'Harry', 'Smith');

-- --------------------------------------------------------

--
-- Table structure for table `Skill`
--

CREATE TABLE `Skill` (
  `skill_id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  `value` int(11) NOT NULL DEFAULT '0',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Skill`
--

INSERT INTO `Skill` (`skill_id`, `name`, `description`, `value`, `date_added`, `category_id`) VALUES
(1, 'HOCKEY', 'Learn how to score more goals in a 1 hour hockey lesson...', 25, '2019-04-02 18:46:15', 1),
(2, 'FOOTBALL', 'Come play football for 30 minutes with me...', 5, '2019-04-02 18:46:39', 1),
(3, 'ART', 'I will show you how to paint the mona lisa in 2 hours...', 30, '2019-04-02 18:48:54', 2),
(4, 'ASTRONOMY', 'Learn all about space with me for 2 hours...', 35, '2019-04-02 18:49:43', 4),
(5, 'FINE DINING', 'Learn how to cook fine french cuisine...', 40, '2019-04-02 18:51:20', 6);

-- --------------------------------------------------------

--
-- Table structure for table `Skill_Proposal`
--

CREATE TABLE `Skill_Proposal` (
  `skill_proposal_id` int(11) NOT NULL,
  `initial_skill_id` int(11) NOT NULL,
  `offer_skill_id` int(11) NOT NULL,
  `status` enum('pending approval','rejected','inprogress','complete') NOT NULL DEFAULT 'pending approval',
  `proposal_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `proposed_date` varchar(160) DEFAULT NULL,
  `accepted_date` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Trade`
--

CREATE TABLE `Trade` (
  `trade_id` int(11) NOT NULL,
  `initial_skill_id` int(11) NOT NULL,
  `offer_skill_id` int(11) NOT NULL,
  `completed_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `user_id` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `email` varchar(320) NOT NULL,
  `password` varchar(72) NOT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT '1',
  `signup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mobile` varchar(11) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `auth_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`user_id`, `username`, `email`, `password`, `user_type`, `signup_date`, `mobile`, `confirmed`, `auth_code`) VALUES
(281, 'Joe_bloggs', 'rhfilestest@gmail.com', '$2y$10$aWKNaKXbyYxyrLG2KHyHKezrXrHeBuVmnW6KPybA7De2CHQgx4pLC', 1, '2019-04-02 14:03:33', '07354345345', 1, 'fbd33492a1845029c3e5'),
(282, 'youcef_a', 'hoosive@hotmail.co.uk', '$2y$10$Kz4HhE7bkXkIaEB6NMPIsO55VN7AT4U0b7FZPVwmYg/ig3udjpmYC', 1, '2019-04-02 14:23:14', '07345345235', 1, '959eca66d84f0cb21551'),
(283, 'h_smith', 'rhfilestest2@gmail.com', '$2y$10$U60bDKFyKXNrvLo5fWPOFeOZudM6i1tfO7Wn1zbotHCD.LWy/eKh.', 1, '2019-04-02 14:37:41', '07324234234', 1, '2c3d0ba536a70b13b6aa');

-- --------------------------------------------------------

--
-- Table structure for table `User_Badges`
--

CREATE TABLE `User_Badges` (
  `user_badge_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `User_Skill`
--

CREATE TABLE `User_Skill` (
  `user_skill_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `User_Skill`
--

INSERT INTO `User_Skill` (`user_skill_id`, `skill_id`, `user_id`, `date_added`) VALUES
(1, 1, 282, '2019-04-02 18:46:15'),
(2, 2, 282, '2019-04-02 18:46:39'),
(3, 3, 281, '2019-04-02 18:48:54'),
(4, 4, 281, '2019-04-02 18:49:43'),
(5, 5, 283, '2019-04-02 18:51:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `Initial_Skill`
--
ALTER TABLE `Initial_Skill`
  ADD PRIMARY KEY (`initial_skill_id`),
  ADD KEY `user_skill_id` (`user_skill_id`);

--
-- Indexes for table `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `Offer_Skill`
--
ALTER TABLE `Offer_Skill`
  ADD PRIMARY KEY (`offer_skill_id`),
  ADD KEY `user_skill_id` (`user_skill_id`);

--
-- Indexes for table `Profile`
--
ALTER TABLE `Profile`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `Skill`
--
ALTER TABLE `Skill`
  ADD PRIMARY KEY (`skill_id`),
  ADD KEY `Skill_ibfk_1` (`category_id`),
  ADD KEY `skill_name_index` (`name`);

--
-- Indexes for table `Skill_Proposal`
--
ALTER TABLE `Skill_Proposal`
  ADD PRIMARY KEY (`skill_proposal_id`),
  ADD KEY `initial_skill_id` (`initial_skill_id`),
  ADD KEY `offer_skill_id` (`offer_skill_id`);

--
-- Indexes for table `Trade`
--
ALTER TABLE `Trade`
  ADD PRIMARY KEY (`trade_id`),
  ADD KEY `initial_skill_id` (`initial_skill_id`),
  ADD KEY `offer_skill_id` (`offer_skill_id`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `User_Badges`
--
ALTER TABLE `User_Badges`
  ADD PRIMARY KEY (`user_badge_id`),
  ADD KEY `badge_id` (`badge_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `User_Skill`
--
ALTER TABLE `User_Skill`
  ADD PRIMARY KEY (`user_skill_id`),
  ADD KEY `skill_id` (`skill_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Initial_Skill`
--
ALTER TABLE `Initial_Skill`
  MODIFY `initial_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Location`
--
ALTER TABLE `Location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Offer_Skill`
--
ALTER TABLE `Offer_Skill`
  MODIFY `offer_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Skill`
--
ALTER TABLE `Skill`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Skill_Proposal`
--
ALTER TABLE `Skill_Proposal`
  MODIFY `skill_proposal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Trade`
--
ALTER TABLE `Trade`
  MODIFY `trade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `User_Badges`
--
ALTER TABLE `User_Badges`
  MODIFY `user_badge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `User_Skill`
--
ALTER TABLE `User_Skill`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Initial_Skill`
--
ALTER TABLE `Initial_Skill`
  ADD CONSTRAINT `Initial_Skill_ibfk_1` FOREIGN KEY (`user_skill_id`) REFERENCES `User_Skill` (`user_skill_id`);

--
-- Constraints for table `Offer_Skill`
--
ALTER TABLE `Offer_Skill`
  ADD CONSTRAINT `Offer_Skill_ibfk_1` FOREIGN KEY (`user_skill_id`) REFERENCES `User_Skill` (`user_skill_id`);

--
-- Constraints for table `Profile`
--
ALTER TABLE `Profile`
  ADD CONSTRAINT `Profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`),
  ADD CONSTRAINT `Profile_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `Location` (`location_id`);

--
-- Constraints for table `Skill`
--
ALTER TABLE `Skill`
  ADD CONSTRAINT `Skill_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `Category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Skill_Proposal`
--
ALTER TABLE `Skill_Proposal`
  ADD CONSTRAINT `Skill_Proposal_ibfk_1` FOREIGN KEY (`initial_skill_id`) REFERENCES `Initial_Skill` (`initial_skill_id`),
  ADD CONSTRAINT `Skill_Proposal_ibfk_2` FOREIGN KEY (`offer_skill_id`) REFERENCES `Offer_Skill` (`offer_skill_id`);

--
-- Constraints for table `Trade`
--
ALTER TABLE `Trade`
  ADD CONSTRAINT `Trade_ibfk_1` FOREIGN KEY (`initial_skill_id`) REFERENCES `Initial_Skill` (`initial_skill_id`),
  ADD CONSTRAINT `Trade_ibfk_2` FOREIGN KEY (`offer_skill_id`) REFERENCES `Offer_Skill` (`offer_skill_id`);

--
-- Constraints for table `User_Badges`
--
ALTER TABLE `User_Badges`
  ADD CONSTRAINT `User_Badges_ibfk_1` FOREIGN KEY (`badge_id`) REFERENCES `ZAP`.`Badge` (`badge_id`),
  ADD CONSTRAINT `User_Badges_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Constraints for table `User_Skill`
--
ALTER TABLE `User_Skill`
  ADD CONSTRAINT `User_Skill_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `Skill` (`skill_id`),
  ADD CONSTRAINT `User_Skill_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
