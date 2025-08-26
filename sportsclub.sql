-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 23, 2025 at 08:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sports-club(4)`
--

-- --------------------------------------------------------

--
-- Table structure for table `badminton_players`
--

CREATE TABLE `badminton_players` (
  `id` int(11) NOT NULL,
  `fullName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `teamName` varchar(100) DEFAULT NULL,
  `player1` varchar(100) DEFAULT NULL,
  `dob1` date DEFAULT NULL,
  `player2` varchar(100) DEFAULT NULL,
  `dob2` date DEFAULT NULL,
  `category` varchar(10) DEFAULT NULL,
  `health_certificate1` varchar(255) DEFAULT NULL,
  `health_certificate2` varchar(255) DEFAULT NULL,
  `game` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'badminton',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cricket_players`
--

CREATE TABLE `cricket_players` (
  `id` int(11) NOT NULL,
  `player_name` varchar(225) NOT NULL,
  `age` int(11) NOT NULL,
  `role` varchar(225) NOT NULL,
  `health_certificate` varchar(225) NOT NULL,
  `batting_style` varchar(255) DEFAULT NULL,
  `bowling_style` varchar(255) DEFAULT NULL,
  `game` varchar(255) DEFAULT 'cricket'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cricket_teams`
--

CREATE TABLE `cricket_teams` (
  `id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `captain_name` varchar(100) NOT NULL,
  `vice_captain_name` varchar(100) NOT NULL,
  `contact_number` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `sport` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `close` varchar(255) NOT NULL DEFAULT 'close',
  `event_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `event_location`, `sport`, `status`, `start_date`, `end_date`, `close`, `event_time`) VALUES
(4, 'Sports Gala 2025', 'FG Kharian Women Sports Club', 'Volleyball, Badminton, Table Tennis, Cricket', 'active', '2025-08-24', '2025-08-27', 'close', '09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `event_games`
--

CREATE TABLE `event_games` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `game_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(10) UNSIGNED NOT NULL,
  `game_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `game_name`) VALUES
(1, 'Cricket'),
(2, 'TableTennis'),
(3, 'Volleyball'),
(4, 'Badminton');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `event_name` varchar(255) NOT NULL,
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `game` varchar(100) DEFAULT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team1_name` varchar(50) DEFAULT NULL,
  `team2_name` varchar(50) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `loser_id` int(11) DEFAULT NULL,
  `round` varchar(50) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `bracket_type` varchar(50) DEFAULT NULL,
  `winner_name` varchar(255) DEFAULT NULL,
  `loser_name` varchar(255) DEFAULT NULL,
  `match_status` varchar(100) NOT NULL,
  `team1_score` int(255) NOT NULL,
  `team2_score` int(255) NOT NULL,
  `total_over` int(255) NOT NULL,
  `toss_winner` varchar(255) NOT NULL,
  `result_winner` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `player_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tabletennis_players`
--

CREATE TABLE `tabletennis_players` (
  `id` int(11) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `category` varchar(20) NOT NULL,
  `player1_name` varchar(100) NOT NULL,
  `player1_dob` date NOT NULL,
  `hand1` varchar(10) NOT NULL,
  `play_style1` varchar(20) NOT NULL,
  `player1_certificate` varchar(255) NOT NULL,
  `player2_name` varchar(100) DEFAULT NULL,
  `player2_dob` date DEFAULT NULL,
  `hand2` varchar(10) DEFAULT NULL,
  `play_style2` varchar(20) DEFAULT NULL,
  `player2_certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `game` varchar(11) NOT NULL DEFAULT 'tabletennis',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_name` varchar(100) DEFAULT NULL,
  `players_per_team` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `id` int(11) NOT NULL,
  `tournament_type` varchar(100) NOT NULL,
  `event_game_id` int(10) UNSIGNED NOT NULL,
  `round` varchar(50) DEFAULT NULL,
  `number_of_teams` int(11) DEFAULT NULL,
  `players_per_team` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`id`, `tournament_type`, `event_game_id`, `round`, `number_of_teams`, `players_per_team`) VALUES
(19, 'Single Elimination', 1, NULL, NULL, NULL),
(20, 'Single Elimination', 2, NULL, NULL, NULL),
(21, 'Single Elimination', 3, NULL, NULL, NULL),
(22, 'Single Elimination', 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `contactno` varchar(50) NOT NULL,
  `posting_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('admin','user','newadmin') NOT NULL DEFAULT 'user',
  `cnic` varchar(15) NOT NULL,
  `club_college` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `password`, `contactno`, `posting_date`, `role`, `cnic`, `club_college`) VALUES
(5, 'Amrana', 'Bibi', 'imranaaltaf0347k@gmail.com', '$2y$10$u0VkbXl6yQ4xFdOnYog/tedECSVio0SeojJY6.iDKKT7Su3RzjIh2', '3247808271', '2025-08-20 05:33:50', 'admin', '34603-56738-6', 'FGKWSC'),
(49, 'Areeba', 'Farooq', 'areebafarooq@gmail.com', '$2y$10$PVxOSfO7l3e9dN1TIO5AWOoVdYVJo/QId.x0OeYSxRzA7Nys0xuNy', '03001278967', '2025-08-20 05:34:14', 'newadmin', '34603-567358-9', 'FGKWSC'),
(54, 'Areej', 'fatima', 'areejfatima@gmail.com', '$2y$10$0S4BK4juIgh1Gv/hFPGlGOqltSwYW1ZPEoCIkAhncQAYcEX4oN6DC', '03214662454', '2025-08-21 03:26:44', 'user', '54400-1456841-9', 'FG degree college kharian cantt'),
(57, 'sidra', 'khan', 'sidrakhan@gmail.com', '$2y$10$qbBFMR1NSV1.ptgeu4d06uLUR2djyhU5GZf6gQHEkDqZ06gwFcPiC', '03764836748', '2025-08-22 16:56:01', 'user', '34603-4567382-9', 'FG degree college kharian cantt');

-- --------------------------------------------------------

--
-- Table structure for table `volleyball_players`
--

CREATE TABLE `volleyball_players` (
  `id` int(11) NOT NULL,
  `player_name` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `handedness` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `health_certificate` varchar(255) DEFAULT NULL,
  `team_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volleyball_teams`
--

CREATE TABLE `volleyball_teams` (
  `id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `captain_name` varchar(100) NOT NULL,
  `captain_age` int(11) NOT NULL,
  `captain_height` int(11) NOT NULL,
  `captain_handed` varchar(10) NOT NULL,
  `captain_position` varchar(50) DEFAULT NULL,
  `captain_standing_reach` int(11) DEFAULT NULL,
  `captain_block_jump` int(11) DEFAULT NULL,
  `captain_approach_jump` int(11) DEFAULT NULL,
  `club_team` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `game` varchar(20) DEFAULT 'VolleyBall',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `badminton_players`
--
ALTER TABLE `badminton_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cricket_players`
--
ALTER TABLE `cricket_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cricket_teams`
--
ALTER TABLE `cricket_teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_games`
--
ALTER TABLE `event_games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `team1_id` (`team1_name`),
  ADD KEY `team2_id` (`team2_name`),
  ADD KEY `winner_id` (`winner_id`),
  ADD KEY `loser_id` (`loser_id`),
  ADD KEY `fk_matches_event` (`event_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `tabletennis_players`
--
ALTER TABLE `tabletennis_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`event_game_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volleyball_players`
--
ALTER TABLE `volleyball_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `volleyball_teams`
--
ALTER TABLE `volleyball_teams`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `badminton_players`
--
ALTER TABLE `badminton_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cricket_players`
--
ALTER TABLE `cricket_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cricket_teams`
--
ALTER TABLE `cricket_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_games`
--
ALTER TABLE `event_games`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabletennis_players`
--
ALTER TABLE `tabletennis_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `volleyball_players`
--
ALTER TABLE `volleyball_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volleyball_teams`
--
ALTER TABLE `volleyball_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_games`
--
ALTER TABLE `event_games`
  ADD CONSTRAINT `event_games_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_games_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `fk_matches_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
