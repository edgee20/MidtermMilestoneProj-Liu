-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 09:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lutong_bahay`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Breakfast'),
(7, 'Desserts'),
(2, 'Main Dishes'),
(8, 'Rice & Noodles'),
(5, 'Seafood'),
(6, 'Snacks & Merienda'),
(3, 'Soups & Stews'),
(4, 'Vegetables');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `recipe_id`, `user_id`, `content`, `is_edited`, `created_at`, `updated_at`) VALUES
(3, 3, 3, 'wowww!', 1, '2026-10-09 06:30:08', '2026-10-09 06:30:23'),
(5, 3, 4, 'hala wrong comment', 0, '2026-10-09 06:33:48', '2026-10-09 06:33:48'),
(6, 3, 5, 'hello ayen', 0, '2026-10-09 06:56:37', '2026-10-09 06:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `recipe_id`, `created_at`) VALUES
(4, 3, '2026-10-09 06:33:18'),
(5, 5, '2026-10-09 07:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `ingredient_name`, `sort_order`) VALUES
(5, 3, 'Pork', 0),
(6, 3, 'Adobo', 1),
(8, 4, 'Pork', 0),
(11, 5, 'Kangkong', 0),
(12, 5, 'Pink Drink', 1);

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `instructions` text NOT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `description`, `instructions`, `is_edited`, `created_at`, `updated_at`) VALUES
(3, 3, 2, 'Pork Adobo', 'Pork adobo, duhh', '1.asdasd\r\n123fdsfdf', 0, '2026-10-09 06:28:19', '2026-10-09 06:28:19'),
(4, 4, 2, 'Pork Adobo', 'asdasdasdasd', 'asdasdasdasdasd', 1, '2026-10-09 06:37:23', '2026-10-09 06:37:36'),
(5, 5, 7, 'Kangkong Chips', 'Josh Mojica\r\n\r\n- Open a recipe to see e', 'WHAT THE SITE SHOULD LET MEMBERS DO\r\n- Create an account, log in, and log out. Pages meant for members should not be reachable by guests.\r\n- Share a recipe with a title, a short description, a category, a list of ingredients, and the cooking steps. Since recipes have different numbers of ingredients, the form should let the member add more ingredient fields as needed.\r\n- See the latest recipes first, look up recipes by keyword, and narrow them down by category.\r\n- Open a recipe to see everything about it, including what other members said.\r\n- Comment on recipes.\r\n- Save recipes to a personal favorites list, and remove them, without the page reloading.\r\n- Fix or remove their own recipes and comments, and let others see that something was changed after posting.\r\n\r\nYOUR OWN DESIGN DECISIONS\r\n- Design how they relate, and which keys and constraints keep your data clean.\r\n- Decide which parts of your code become classes.\r\n- Decide your own validation rules, and enforce them on both the browser side and the server side.\r\n- Pick a theme for your site (e.g., Filipino home cooking, baking, budget meals, regional dishes) and design the look around it.\r\n- Add one small feature of your own that is not listed here.\r\n\r\nREQUIRED DATABASE TABLES\r\nYour database must use exactly these six tables, with these names:\r\n1. users: the members of the site\r\n2. categories: the recipe categories (fill this with your own starting categories)\r\n3. recipes: the recipes members post\r\n4. ingredients: the ingredients of each recipe, one row per ingredient\r\n5. comments: what members say about a recipe\r\n6. favorites: which member saved which recipe\r\n\r\nNON-NEGOTIABLES\r\n- Passwords must never be stored as plain text.\r\n- Your database connection lives in one file that the other pages include.\r\n- Any query that uses user input must be protected against SQL injection.\r\n- Anything a user typed must be displayed safely.\r\n- The layout must work on both phone and desktop screens.', 1, '2026-10-09 06:59:20', '2026-10-09 07:00:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(3, 'Ej', 'ej@addu.edu.ph', '$2y$10$FPSymEl8S6ztQ4naHoq6Q.AtYWuSpbr0gz.mx4asaroWT82QPKA4e', '2026-10-09 06:24:10'),
(4, 'Ayen', 'ayen@addu.edu.ph', '$2y$10$ZjHlOx9tLeEQIyvFq6oiJeG2uPqU3aDc8tdmvWeZLft6JnjCJicCS', '2026-10-09 06:32:23'),
(5, 'Megan', 'megan@gmail.com', '$2y$10$cO9m9wuKR9Vzdy32SNKN2eDnmmlp524wfrVo.3Ngi6mLk8sFFeQ5u', '2026-10-09 06:39:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recipe_order` (`recipe_id`,`sort_order`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`,`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
