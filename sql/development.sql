-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2019 at 12:31 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `development`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_roles`
--

CREATE TABLE `access_roles` (
  `id_access_roles` int(10) UNSIGNED NOT NULL,
  `access_scope` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(17, '2014_10_12_000000_create_users_table', 1),
(18, '2014_10_12_100000_create_password_resets_table', 1),
(19, '2019_04_06_125745_create_table_module', 1),
(20, '2019_04_06_134659_create_table_setting', 1),
(21, '2019_04_21_154121_create_user_account_table', 1),
(22, '2019_05_11_074059_create_role_table', 1),
(23, '2019_05_11_075918_create_access_roles_table', 1),
(24, '2019_05_11_081506_create_role_users_table', 1),
(25, '2019_05_15_171942_create_table_terms', 1),
(26, '2019_05_15_173051_create_table_termsmeta', 1),
(27, '2019_05_16_173058_create_term_taxonomy_table', 1),
(28, '2019_05_29_183640_add_create_modified_attribute', 1),
(29, '2019_05_31_080221_create_posts_table', 1),
(30, '2019_05_31_113826_create_postmeta_table', 1),
(31, '2019_05_31_115324_create_term_relationships_table', 1),
(32, '2019_07_21_123310_create_product_meta_table', 1),
(36, '2019_09_12_092225_add_availability_condition_product_meta_table', 2),
(37, '2019_09_17_080454_create_jobs_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id_module` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '1',
  `is_scanable` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `scope` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '[]',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id_module`, `name`, `slug`, `description`, `order`, `is_scanable`, `scope`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Appearance', 'appearance', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:49', NULL),
(2, 'test', 'test', NULL, 1, '0', '[]', NULL, '2019-09-10 15:59:50', NULL),
(3, 'Taxonomy', 'taxonomy', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(4, 'User', 'user', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(5, 'Page', 'page', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(6, 'Post', 'post', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(7, 'Role', 'role', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(8, 'ECommerce', 'ecommerce', NULL, 1, '1', '[\"menu\",\"create\",\"read\",\"update\",\"delete\"]', NULL, '2019-09-10 15:59:50', NULL),
(9, 'Account', 'account', '', 1, '0', '[]', NULL, '2019-09-12 23:51:21', '2019-09-12 23:51:21'),
(10, 'Blog', 'blog', '', 1, '0', '[]', NULL, '2019-09-12 23:51:21', '2019-09-12 23:51:21'),
(11, 'Core', 'core', '', 1, '0', '[]', NULL, '2019-09-12 23:51:21', '2019-09-12 23:51:21');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postmeta`
--

CREATE TABLE `postmeta` (
  `id_postmeta` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `postmeta`
--

INSERT INTO `postmeta` (`id_postmeta`, `post_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) VALUES
(1, 3, 'tokopedia_store', '\"sparda-store\"', '2019-09-10 16:25:12', '2019-09-10 16:25:12'),
(2, 3, 'tokopedia_slug', '\"laptop-acer-nitro-5-an515-52-51t2-i5-8300h-8gb-1tb-gtx1050-4gb-w10\"', '2019-09-10 16:25:12', '2019-09-25 22:19:42'),
(3, 3, 'tokopedia_supplier', '\"prostorecomputer\"', '2019-09-10 16:25:12', '2019-09-17 00:56:05'),
(4, 3, 'tokopedia_source', '\"lenovo-flex-6-11igm-x360-touch-n4000-4gb-64ssd-11-6hd-ts-w10\"', '2019-09-10 16:25:12', '2019-09-17 00:56:05'),
(5, 3, 'meta_title', '\"eaque\"', '2019-09-10 16:25:12', '2019-09-11 21:50:37'),
(6, 3, 'meta_keyword', NULL, '2019-09-10 16:25:12', '2019-09-10 16:25:12'),
(7, 3, 'meta_description', '\"Cum sunt totam quas totam rem aut dolorem. Est nulla provident ipsa doloribus sint fuga perspiciatis.\"', '2019-09-10 16:25:12', '2019-09-11 21:50:37'),
(8, 3, 'feature_image', '\"post\\/nihil\\/A4hMOtoZuvNZWkzHJaHgpG63i4JgZZgRXOwuMLTP.jpeg\"', '2019-09-11 21:49:06', '2019-09-16 03:26:38'),
(9, 2, 'meta_title', NULL, '2019-09-12 05:49:44', '2019-09-12 05:49:44'),
(10, 2, 'meta_keyword', NULL, '2019-09-12 05:49:44', '2019-09-12 05:49:44'),
(11, 2, 'meta_description', NULL, '2019-09-12 05:49:44', '2019-09-12 05:49:44'),
(12, 2, 'feature_image', '\"post\\/illo\\/fulH7I1kmywSSpQcSnLUvd2oA47iEelfttQ4uJcD.jpeg\"', '2019-09-12 05:49:44', '2019-09-12 05:49:44'),
(13, 4, 'tokopedia_store', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(14, 4, 'tokopedia_slug', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(15, 4, 'tokopedia_supplier', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(16, 4, 'tokopedia_source', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(17, 4, 'meta_title', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(18, 4, 'meta_keyword', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(19, 4, 'meta_description', NULL, '2019-09-12 06:07:26', '2019-09-12 06:07:26'),
(20, 4, 'feature_image', '\"post\\/sdsd\\/2J9vTl0r8COGyzvhoRsssCyrICUKXdLXdam6raoB.jpeg\"', '2019-09-16 03:26:19', '2019-09-16 03:26:19'),
(21, 3, 'shopee_slug', '\"product\\/89946759\\/2865856232\"', '2019-09-25 22:19:42', '2019-09-25 22:19:42'),
(22, 4, 'shopee_slug', NULL, '2019-10-21 04:33:43', '2019-10-21 04:33:43'),
(23, 5, 'meta_title', NULL, '2019-10-22 03:09:22', '2019-10-22 03:09:22'),
(24, 5, 'meta_keyword', NULL, '2019-10-22 03:09:22', '2019-10-22 03:09:22'),
(25, 5, 'meta_description', NULL, '2019-10-22 03:09:22', '2019-10-22 03:09:22'),
(26, 5, 'feature_image', '\"post\\/lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-incidunt-magnam-corporis-enim-dolor-quasi-distinctio\\/LC70ptXQSgxYhhIpOWM42QvF9yRFt4W6Gs0ABxlq.jpeg\"', '2019-10-22 03:09:22', '2019-10-22 03:09:22');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id_posts` bigint(20) UNSIGNED NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_content` longtext COLLATE utf8mb4_unicode_ci,
  `post_excerpt` text COLLATE utf8mb4_unicode_ci,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_ping` text COLLATE utf8mb4_unicode_ci,
  `pinged` text COLLATE utf8mb4_unicode_ci,
  `post_content_filtered` text COLLATE utf8mb4_unicode_ci,
  `post_parent` bigint(20) UNSIGNED DEFAULT NULL,
  `guid` text COLLATE utf8mb4_unicode_ci,
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `modified_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id_posts`, `post_title`, `post_slug`, `post_content`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `to_ping`, `pinged`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `created_by`, `modified_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'homepage', 'homepage', 'Aut autem dolorem saepe neque ut. Nihil ipsum asperiores tempore molestiae atque ut. Alias consectetur et provident aut eos aut. Eius quod corrupti facilis. Sunt debitis iste non et.', 'debitis', 'draft', 'open', 'open', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'page', NULL, 1, 1, NULL, '2019-09-10 15:59:50', NULL),
(2, 'corrupti', 'illo', '<p>Id facere possimus magni laboriosam occaecati. Laboriosam aspernatur voluptas at itaque corrupti laborum. Tempore modi et saepe perspiciatis optio quod. Quaerat impedit sunt architecto cumque.</p>', 'Id facere possimus magni laboriosam occaecati. Lab[...]', 'draft', 'open', 'open', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'post', NULL, 1, 1, NULL, '2019-09-10 15:59:50', '2019-09-12 05:49:44'),
(3, 'eaque', 'nihil', '<p>Cum sunt totam quas totam rem aut dolorem. Est nulla provident ipsa doloribus sint fuga perspiciatis.</p>', 'Cum sunt totam quas totam rem aut dolorem. Est nul[...]', 'publish', 'open', 'open', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'product', NULL, 1, 1, NULL, '2019-09-10 15:59:50', '2019-09-12 06:07:44'),
(4, 'sdsd', 'sdsd', NULL, NULL, 'publish', 'open', 'open', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'product', NULL, 1, 1, NULL, '2019-09-12 06:07:26', '2019-09-12 06:09:20'),
(5, 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Incidunt magnam corporis enim dolor quasi distinctio.', 'lorem-ipsum-dolor-sit-amet-consectetur-adipisicing-elit-incidunt-magnam-corporis-enim-dolor-quasi-distinctio', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p>See Another&nbsp;<a href=\"javascript:;\">: Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a></p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p>See Another&nbsp;<a href=\"javascript:;\">: Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a></p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos voluptatum sequi soluta blanditiis, eos beatae perspiciatis et debitis officia modi alias porro libero molestias? Corrupti distinctio labore accusantium possimus exercitationem? Lorem ipsum dolor, sit amet consectetur adipisicing elit. Neque&nbsp;<a href=\"javascript:;\">reiciendis</a>&nbsp;delectus id suscipit accusantium ducimus corporis consequuntur, iste est illum? Ducimus odio qui debitis alias sed incidunt eos cupiditate eaque.</p>\r\n\r\n<p><iframe frameborder=\"0\" height=\"300px\" scrolling=\"no\" src=\"https://www.youtube.com/embed/AsasbEx6xQU\" width=\"100%\"></iframe></p>', 'Lorem ipsum dolor sit amet, consectetur adipisicin[...]', 'draft', 'open', 'open', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'post', NULL, 1, 1, NULL, '2019-10-22 03:09:22', '2019-10-23 03:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(10) UNSIGNED NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `modified_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `slug`, `name`, `description`, `created_by`, `modified_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'Super Admin', NULL, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `name`, `value`, `created_at`, `updated_at`) VALUES
(1, 'taxonomy_menu', '\"category,product-category\"', NULL, '2019-09-12 05:53:25'),
(2, 'theme_public', '\"newsmaker\"', NULL, NULL),
(3, 'theme_cms', '\"v_1\"', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `termmeta`
--

CREATE TABLE `termmeta` (
  `id_termmeta` int(10) UNSIGNED NOT NULL,
  `meta_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci,
  `term_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `termmeta`
--

INSERT INTO `termmeta` (`id_termmeta`, `meta_key`, `meta_value`, `term_id`, `created_at`, `updated_at`) VALUES
(1, 'menu_text', '\"Uncategorized\"', 1, '2019-09-12 05:48:58', '2019-09-12 05:48:58'),
(2, 'menu_title', NULL, 1, '2019-09-12 05:48:58', '2019-09-12 05:48:58'),
(3, 'menu_target', '\"_self\"', 1, '2019-09-12 05:48:58', '2019-09-12 05:48:58'),
(4, 'menu_order', '0', 1, '2019-09-12 05:48:58', '2019-09-12 05:48:58'),
(5, 'menu_text', '\"Asus\"', 2, '2019-09-12 05:53:36', '2019-09-12 05:53:36'),
(6, 'menu_title', NULL, 2, '2019-09-12 05:53:36', '2019-09-12 05:53:36'),
(7, 'menu_target', '\"_self\"', 2, '2019-09-12 05:53:36', '2019-09-12 05:53:36'),
(8, 'menu_order', '1', 2, '2019-09-12 05:53:36', '2019-09-12 05:53:36'),
(9, 'menu_text', '\"nirvana\"', 3, '2019-10-23 04:24:10', '2019-10-23 04:24:10'),
(10, 'menu_title', NULL, 3, '2019-10-23 04:24:10', '2019-10-23 04:24:10'),
(11, 'menu_target', '\"_self\"', 3, '2019-10-23 04:24:10', '2019-10-23 04:24:10'),
(12, 'menu_order', '0', 3, '2019-10-23 04:24:10', '2019-10-23 04:24:10'),
(13, 'menu_text', '\"dante\"', 5, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(14, 'menu_title', NULL, 5, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(15, 'menu_target', '\"_self\"', 5, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(16, 'menu_order', '0', 5, '2019-10-23 04:42:45', '2019-10-23 04:42:45');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id_terms` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term_group` int(10) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `modified_by` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id_terms`, `name`, `slug`, `term_group`, `created_by`, `modified_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Uncategorized', 'uncategorized', NULL, 1, 1, NULL, '2019-09-10 15:59:50', NULL),
(2, 'Asus', 'asus', NULL, 1, 1, NULL, '2019-09-12 05:52:49', '2019-09-12 05:52:49'),
(3, 'nirvana', 'nirvana', NULL, 1, 1, NULL, '2019-10-21 04:27:42', '2019-10-21 04:28:12'),
(4, 'test', 'test', NULL, 1, 1, NULL, '2019-10-21 04:28:48', '2019-10-21 04:28:48'),
(5, 'dante', 'dante', NULL, 1, 1, NULL, '2019-10-23 04:40:43', '2019-10-23 04:40:43');

-- --------------------------------------------------------

--
-- Table structure for table `term_relationships`
--

CREATE TABLE `term_relationships` (
  `id_term_relationships` int(10) UNSIGNED NOT NULL,
  `term_taxonomy_id` int(10) UNSIGNED NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `term_order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `term_relationships`
--

INSERT INTO `term_relationships` (`id_term_relationships`, `term_taxonomy_id`, `object_id`, `term_order`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 0, '2019-09-10 15:59:50', NULL),
(3, 5, 3, 0, '2019-09-12 05:53:02', '2019-09-12 05:53:02'),
(5, 5, 4, 0, '2019-09-12 06:09:20', '2019-09-12 06:09:20'),
(6, 8, 4, 0, '2019-10-21 04:33:43', '2019-10-21 04:33:43'),
(8, 1, 5, 0, '2019-10-22 03:09:22', '2019-10-22 03:09:22'),
(9, 8, 5, 0, '2019-10-22 04:00:31', '2019-10-22 04:00:31'),
(10, 11, 5, 0, '2019-10-22 04:00:31', '2019-10-22 04:00:31'),
(11, 11, 4, 0, '2019-10-22 05:53:33', '2019-10-22 05:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `term_taxonomy`
--

CREATE TABLE `term_taxonomy` (
  `id_term_taxonomy` int(10) UNSIGNED NOT NULL,
  `term_id` int(10) UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `taxonomy` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `modified_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `term_taxonomy`
--

INSERT INTO `term_taxonomy` (`id_term_taxonomy`, `term_id`, `description`, `taxonomy`, `parent_id`, `created_by`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'category', NULL, 1, 1, '2019-09-10 15:59:50', NULL),
(2, 1, NULL, 'tag', NULL, 1, 1, '2019-09-10 15:59:50', NULL),
(3, 1, NULL, 'product-category', NULL, 1, 1, '2019-09-10 15:59:50', NULL),
(5, 2, NULL, 'product-category', NULL, 1, 1, '2019-09-12 05:52:49', '2019-09-12 05:52:49'),
(8, 3, NULL, 'tag', NULL, 1, 1, '2019-10-21 04:27:42', '2019-10-21 04:27:42'),
(11, 4, NULL, 'tag', NULL, 1, 1, '2019-10-21 04:33:17', '2019-10-21 04:33:17'),
(12, 3, NULL, 'category', 1, 1, 1, '2019-10-23 04:23:08', '2019-10-23 04:23:08'),
(16, 5, NULL, 'category', 3, 1, 1, '2019-10-23 04:40:43', '2019-10-23 04:40:52'),
(17, 1, NULL, 'navbar', NULL, 1, 1, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(18, 3, NULL, 'navbar', 1, 1, 1, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(19, 5, NULL, 'navbar', 3, 1, 1, '2019-10-23 04:42:45', '2019-10-23 04:42:45'),
(20, 2, NULL, 'navbar', NULL, 1, 1, '2019-10-23 04:42:45', '2019-10-23 04:42:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `modified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_by`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@default.app', NULL, '$2y$10$iiMMmihyR/bD/IztDqEJdOvLtqwG0dDP8t8Zl3gXClgz8ykIdgmPi', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `gender` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image_url` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_roles`
--
ALTER TABLE `access_roles`
  ADD PRIMARY KEY (`id_access_roles`),
  ADD KEY `access_roles_module_id_foreign` (`module_id`),
  ADD KEY `access_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id_module`),
  ADD UNIQUE KEY `module_slug_unique` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `postmeta`
--
ALTER TABLE `postmeta`
  ADD PRIMARY KEY (`id_postmeta`),
  ADD KEY `postmeta_post_id_foreign` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_posts`),
  ADD UNIQUE KEY `posts_post_slug_unique` (`post_slug`),
  ADD KEY `posts_created_by_foreign` (`created_by`),
  ADD KEY `posts_modified_by_foreign` (`modified_by`),
  ADD KEY `posts_post_parent_foreign` (`post_parent`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_slug_unique` (`slug`),
  ADD KEY `role_created_by_foreign` (`created_by`),
  ADD KEY `role_modified_by_foreign` (`modified_by`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD UNIQUE KEY `role_users_user_id_unique` (`user_id`),
  ADD KEY `role_users_role_id_foreign` (`role_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name_unique` (`name`);

--
-- Indexes for table `termmeta`
--
ALTER TABLE `termmeta`
  ADD PRIMARY KEY (`id_termmeta`),
  ADD KEY `termmeta_term_id_foreign` (`term_id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id_terms`),
  ADD UNIQUE KEY `terms_slug_unique` (`slug`),
  ADD KEY `terms_term_group_foreign` (`term_group`),
  ADD KEY `terms_created_by_foreign` (`created_by`),
  ADD KEY `terms_modified_by_foreign` (`modified_by`);

--
-- Indexes for table `term_relationships`
--
ALTER TABLE `term_relationships`
  ADD PRIMARY KEY (`id_term_relationships`),
  ADD KEY `term_relationships_term_taxonomy_id_foreign` (`term_taxonomy_id`),
  ADD KEY `term_relationships_object_id_foreign` (`object_id`);

--
-- Indexes for table `term_taxonomy`
--
ALTER TABLE `term_taxonomy`
  ADD PRIMARY KEY (`id_term_taxonomy`),
  ADD KEY `term_taxonomy_term_id_foreign` (`term_id`),
  ADD KEY `term_taxonomy_parent_id_foreign` (`parent_id`),
  ADD KEY `term_taxonomy_created_by_foreign` (`created_by`),
  ADD KEY `term_taxonomy_modified_by_foreign` (`modified_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_created_by_foreign` (`created_by`),
  ADD KEY `users_modified_by_foreign` (`modified_by`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD UNIQUE KEY `user_account_user_id_unique` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_roles`
--
ALTER TABLE `access_roles`
  MODIFY `id_access_roles` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id_module` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `postmeta`
--
ALTER TABLE `postmeta`
  MODIFY `id_postmeta` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id_posts` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `termmeta`
--
ALTER TABLE `termmeta`
  MODIFY `id_termmeta` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id_terms` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `term_relationships`
--
ALTER TABLE `term_relationships`
  MODIFY `id_term_relationships` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `term_taxonomy`
--
ALTER TABLE `term_taxonomy`
  MODIFY `id_term_taxonomy` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_roles`
--
ALTER TABLE `access_roles`
  ADD CONSTRAINT `access_roles_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `access_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `postmeta`
--
ALTER TABLE `postmeta`
  ADD CONSTRAINT `postmeta_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id_posts`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `posts_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `posts_post_parent_foreign` FOREIGN KEY (`post_parent`) REFERENCES `posts` (`id_posts`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `role_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_users`
--
ALTER TABLE `role_users`
  ADD CONSTRAINT `role_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id_role`),
  ADD CONSTRAINT `role_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `termmeta`
--
ALTER TABLE `termmeta`
  ADD CONSTRAINT `termmeta_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id_terms`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `terms`
--
ALTER TABLE `terms`
  ADD CONSTRAINT `terms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `terms_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `terms_term_group_foreign` FOREIGN KEY (`term_group`) REFERENCES `terms` (`id_terms`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `term_relationships`
--
ALTER TABLE `term_relationships`
  ADD CONSTRAINT `term_relationships_object_id_foreign` FOREIGN KEY (`object_id`) REFERENCES `posts` (`id_posts`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `term_relationships_term_taxonomy_id_foreign` FOREIGN KEY (`term_taxonomy_id`) REFERENCES `term_taxonomy` (`id_term_taxonomy`);

--
-- Constraints for table `term_taxonomy`
--
ALTER TABLE `term_taxonomy`
  ADD CONSTRAINT `term_taxonomy_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `term_taxonomy_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `term_taxonomy_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `terms` (`id_terms`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `term_taxonomy_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id_terms`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_modified_by_foreign` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
