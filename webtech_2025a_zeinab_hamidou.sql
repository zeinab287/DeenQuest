-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 02:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webtech_2025a_zeinab_hamidou`
--

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `badge_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `xp_required` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badge`
--

INSERT INTO `badge` (`badge_id`, `name`, `description`, `icon`, `xp_required`) VALUES
(2, 'Dedicated Learner', 'Reached 300 XP. You are building a strong habit of seeking knowledge.', 'fa-book-reader', 300),
(3, 'Knowledge Champion', 'Achieved 600 XP. Your dedication is truly inspiring!', 'fa-scroll', 600),
(4, 'Novice Seeker', 'Begin your journey towards knowledge. (0 XP)', 'fa-seedling', 0),
(7, 'Quiz Master', 'Reached 1000 XP. You have proven your understanding.', 'fa-brain', 1000),
(8, 'DeenQuest Champion', 'Reached 2000 XP. The mark of true dedication!', 'fa-crown', 2000);

-- --------------------------------------------------------

--
-- Table structure for table `challenge`
--

CREATE TABLE `challenge` (
  `challenge_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `activity_type` enum('quest','game') NOT NULL,
  `activity_id` int(11) NOT NULL,
  `xp_reward` int(11) NOT NULL DEFAULT 100,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenge`
--

INSERT INTO `challenge` (`challenge_id`, `title`, `description`, `activity_type`, `activity_id`, `xp_reward`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
(2, 'Weekly XP Gift', 'Your chance to get bonus', 'quest', 2, 100, '2025-12-17', '2025-12-20', 1, '2025-12-17 21:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `difficulty_level` int(11) NOT NULL DEFAULT 1,
  `xp_reward` int(11) NOT NULL DEFAULT 50,
  `game_file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`game_id`, `title`, `description`, `type`, `subject`, `difficulty_level`, `xp_reward`, `game_file_path`, `created_at`) VALUES
(3, 'Match Allah\'s Names', 'Match Arabic names with English meanings', 'memory', 'Islamic Studies', 1, 50, '/DeenQuest/assets/games/memory_match.html', '2025-12-17 23:20:56'),
(4, 'Arabic Letters Quiz', 'Learn the names of Arabic letters', 'quiz', 'Arabic Language', 1, 75, '/DeenQuest/assets/games/arabic_letters_quiz.html', '2025-12-17 23:20:56'),
(5, 'Sort Five Pillars', 'Put the Five Pillars of Islam in order', 'sorting', 'Islamic Studies', 2, 60, '/DeenQuest/assets/games/five_pillars_sort.html', '2025-12-17 23:20:56'),
(6, 'Prophet names', 'Match a prophet to his book revealed to him', 'Match a prophet to His book', 'Quran', 1, 20, '/DeenQuest/assets/games/prophets_books.html', '2025-12-18 00:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `gamecompletion`
--

CREATE TABLE `gamecompletion` (
  `completion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `xp_awarded` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gamecompletion`
--

INSERT INTO `gamecompletion` (`completion_id`, `user_id`, `game_id`, `completed_at`, `xp_awarded`) VALUES
(1, 1, 5, '2025-12-18 09:04:46', 60);

-- --------------------------------------------------------

--
-- Table structure for table `gamedata`
--

CREATE TABLE `gamedata` (
  `data_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `item_1` varchar(255) NOT NULL,
  `item_2` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `xp_earned_in_quest` int(11) DEFAULT 0,
  `progress_stage` enum('active','quiz_completed','game_completed','reflection_done','completed') DEFAULT 'active',
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`progress_id`, `user_id`, `quest_id`, `xp_earned_in_quest`, `progress_stage`, `last_updated`) VALUES
(1, 1, 2, 150, 'quiz_completed', '2025-12-14 16:14:54');

-- --------------------------------------------------------

--
-- Table structure for table `quest`
--

CREATE TABLE `quest` (
  `quest_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) NOT NULL,
  `xp_reward` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quest`
--

INSERT INTO `quest` (`quest_id`, `title`, `subject`, `description`, `level`, `xp_reward`) VALUES
(2, 'Cleanliness is Half of Faith', 'Fiqh (Jurisprudence)', 'Learn about Taharah (purification), Wudu, and why physical cleanliness is connected to spiritual purity.', 1, 150),
(3, 'The Prophet\'s Kindness', 'Seerah (History)', 'Discover stories from the life of Prophet Muhammad (PBUH) that highlight his mercy and kindness to people and animals.', 2, 200);

-- --------------------------------------------------------

--
-- Table structure for table `quizquestion`
--

CREATE TABLE `quizquestion` (
  `question_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizquestion`
--

INSERT INTO `quizquestion` (`question_id`, `quest_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(3, 2, 'What is the Arabic term for ritual purification required before prayer?', 'Wudu', 'Salah', 'Zikr', '', 'a'),
(4, 2, 'Physical cleanliness in Islam is connected to:', 'Only social status', 'Spiritual purity', 'Only health benefits', '', 'b'),
(5, 3, 'The Prophet Muhammad (PBUH) was known for showing mercy to:', 'Only Muslims', 'Only his family', 'All people and animals', '', 'c');

-- --------------------------------------------------------

--
-- Table structure for table `reflection`
--

CREATE TABLE `reflection` (
  `reflection_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `goals` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('learner','admin') NOT NULL DEFAULT 'learner',
  `xp` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password_hash`, `role`, `xp`, `created_at`) VALUES
(1, 'Zeinab Amadou', 'zamadou889@gmail.com', '$2y$10$xRDrOZ59HbrhJL7Fux3xpeypTp8cYqzbjP7n4Pin0NCKHHZQwMoBm', 'learner', 110, '2025-12-14 16:07:17'),
(2, 'Ahmad', 'ahmad@gmail.com', '$2y$10$DmEu1FjCxikLQb3W1eNdgeS5K45MRah0PnZWyfGYADYH2jDlfVrMW', 'admin', 0, '2025-12-14 16:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `userbadge`
--

CREATE TABLE `userbadge` (
  `user_badge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `awarded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userchallengeprogress`
--

CREATE TABLE `userchallengeprogress` (
  `user_id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userchallengeprogress`
--

INSERT INTO `userchallengeprogress` (`user_id`, `challenge_id`, `completed_at`) VALUES
(1, 2, '2025-12-17 21:07:59');

-- --------------------------------------------------------

--
-- Table structure for table `userquestprogress`
--

CREATE TABLE `userquestprogress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `xp_earned` int(11) NOT NULL DEFAULT 0,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userquestprogress`
--

INSERT INTO `userquestprogress` (`progress_id`, `user_id`, `quest_id`, `score`, `xp_earned`, `completed_at`) VALUES
(1, 1, 2, 50, 0, '2025-12-17 19:22:03'),
(2, 1, 2, 50, 0, '2025-12-17 19:22:16'),
(3, 1, 2, 50, 0, '2025-12-17 19:24:39'),
(4, 1, 2, 0, 0, '2025-12-17 19:24:54'),
(5, 1, 2, 100, 150, '2025-12-17 21:05:19'),
(6, 1, 2, 100, 150, '2025-12-17 21:07:59'),
(7, 1, 2, 100, 1, '2025-12-18 09:00:38'),
(8, 1, 2, 100, 1, '2025-12-18 09:01:09'),
(9, 1, 2, 100, 0, '2025-12-18 11:32:23'),
(10, 1, 2, 100, 0, '2025-12-18 11:32:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`badge_id`);

--
-- Indexes for table `challenge`
--
ALTER TABLE `challenge`
  ADD PRIMARY KEY (`challenge_id`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `gamecompletion`
--
ALTER TABLE `gamecompletion`
  ADD PRIMARY KEY (`completion_id`),
  ADD UNIQUE KEY `unique_user_game` (`user_id`,`game_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `gamedata`
--
ALTER TABLE `gamedata`
  ADD PRIMARY KEY (`data_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `user_quest_unique` (`user_id`,`quest_id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- Indexes for table `quest`
--
ALTER TABLE `quest`
  ADD PRIMARY KEY (`quest_id`);

--
-- Indexes for table `quizquestion`
--
ALTER TABLE `quizquestion`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- Indexes for table `reflection`
--
ALTER TABLE `reflection`
  ADD PRIMARY KEY (`reflection_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userbadge`
--
ALTER TABLE `userbadge`
  ADD PRIMARY KEY (`user_badge_id`),
  ADD UNIQUE KEY `user_badge_unique` (`user_id`,`badge_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Indexes for table `userchallengeprogress`
--
ALTER TABLE `userchallengeprogress`
  ADD PRIMARY KEY (`user_id`,`challenge_id`),
  ADD KEY `challenge_id` (`challenge_id`);

--
-- Indexes for table `userquestprogress`
--
ALTER TABLE `userquestprogress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `badge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `challenge`
--
ALTER TABLE `challenge`
  MODIFY `challenge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gamecompletion`
--
ALTER TABLE `gamecompletion`
  MODIFY `completion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gamedata`
--
ALTER TABLE `gamedata`
  MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quest`
--
ALTER TABLE `quest`
  MODIFY `quest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quizquestion`
--
ALTER TABLE `quizquestion`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reflection`
--
ALTER TABLE `reflection`
  MODIFY `reflection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userbadge`
--
ALTER TABLE `userbadge`
  MODIFY `user_badge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userquestprogress`
--
ALTER TABLE `userquestprogress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gamecompletion`
--
ALTER TABLE `gamecompletion`
  ADD CONSTRAINT `gamecompletion_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gamecompletion_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `gamedata`
--
ALTER TABLE `gamedata`
  ADD CONSTRAINT `gamedata_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quest` (`quest_id`) ON DELETE CASCADE;

--
-- Constraints for table `quizquestion`
--
ALTER TABLE `quizquestion`
  ADD CONSTRAINT `quizquestion_ibfk_1` FOREIGN KEY (`quest_id`) REFERENCES `quest` (`quest_id`) ON DELETE CASCADE;

--
-- Constraints for table `reflection`
--
ALTER TABLE `reflection`
  ADD CONSTRAINT `reflection_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `userbadge`
--
ALTER TABLE `userbadge`
  ADD CONSTRAINT `userbadge_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userbadge_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`badge_id`) ON DELETE CASCADE;

--
-- Constraints for table `userchallengeprogress`
--
ALTER TABLE `userchallengeprogress`
  ADD CONSTRAINT `userchallengeprogress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userchallengeprogress_ibfk_2` FOREIGN KEY (`challenge_id`) REFERENCES `challenge` (`challenge_id`) ON DELETE CASCADE;

--
-- Constraints for table `userquestprogress`
--
ALTER TABLE `userquestprogress`
  ADD CONSTRAINT `userquestprogress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userquestprogress_ibfk_2` FOREIGN KEY (`quest_id`) REFERENCES `quest` (`quest_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
