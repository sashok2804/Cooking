-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 14 2024 г., 09:21
-- Версия сервера: 10.8.4-MariaDB
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Cook`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Cart`
--

CREATE TABLE `Cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Структура таблицы `order_status`
--

CREATE TABLE `order_status` (
  `id` int(11) NOT NULL,
  `status_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `order_status`
--

INSERT INTO `order_status` (`id`, `status_name`) VALUES
(1, 'Новый'),
(2, 'Готовится'),
(3, 'Готов к отправке'),
(4, 'В процессе доставки'),
(5, 'Доставлен'),
(6, 'Отменен');

-- --------------------------------------------------------

--
-- Структура таблицы `Products`
--

CREATE TABLE `Products` (
  `id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Products`
--

INSERT INTO `Products` (`id`, `title`, `description`, `price`, `image_path`) VALUES
(1, 'Хлеб', 'Свежий хлеб из пшеничной муки', '2.50', 'https://pic.rutubelist.ru/video/bc/c5/bcc5dc0d275daa509835684a5ac5be86.jpg'),
(2, 'Багет', 'Пышный французский багет', '1.80', 'https://img.youtube.com/vi/jBpmZCCrOS0/maxresdefault.jpg'),
(3, 'Круассан', 'Нежный круассан с начинкой', '2.00', 'https://i.ytimg.com/vi/pZL0Ug9au48/maxresdefault.jpg'),
(4, 'Пончики', 'Сладкие пончики с начинкой', '1.50', 'https://o-tendencii.com/uploads/posts/2022-02/1645958712_12-o-tendencii-com-p-ponchiki-s-nachinkoi-foto-12.jpg'),
(5, 'Булочки с корицей', 'Ароматные булочки с корицей', '1.20', 'https://sneg.top/uploads/posts/2023-04/1681999952_sneg-top-p-sladkaya-bulochka-kartinka-instagram-61.jpg'),
(6, 'Пирог с ягодами', 'Сочный пирог с свежими ягодами', '3.00', 'https://i.ytimg.com/vi/AjzZ4EbrsZA/maxresdefault.jpg'),
(7, 'Булочка с маком', 'Воздушные булочки с маком', '1.00', 'https://i.ytimg.com/vi/HyMGu3lQInc/maxresdefault.jpg'),
(8, 'Кекс с изюмом', 'Нежный кекс с изюмом', '1.50', 'https://i.ytimg.com/vi/8j5xD2KsG4g/maxresdefault.jpg'),
(9, 'Булочки с марципаном', 'Нежные булочки с марципаном', '1.20', 'https://i.ytimg.com/vi/pcWbT-LfAIU/maxresdefault.jpg'),
(10, 'Эклеры', 'Изысканные эклеры с заварным кремом', '2.20', 'https://i.ytimg.com/vi/LICMncnc--8/maxresdefault.jpg'),
(11, 'Шоколадный торт', 'Нежный шоколадный торт с глазурью', '4.00', 'https://i.ytimg.com/vi/a_a2iRCwbXA/maxresdefault.jpg'),
(12, 'Печенье', 'Ароматное печенье с орехами', '2.00', 'https://i.ytimg.com/vi/sQvCh44LlKg/maxresdefault.jpg'),
(13, 'Тарталетки с фруктами', 'Изысканные тарталетки с свежими фруктами', '2.50', 'https://avatars.mds.yandex.net/get-mpic/4466428/img_id5520257393100586228.jpeg/orig'),
(14, 'Сдобные булочки', 'Сдобные булочки с маслом', '1.20', 'https://i.ytimg.com/vi/0fUR0_iqO7w/maxresdefault.jpg'),
(15, 'Пирожки с капустой', 'Ароматные пирожки с капустой', '1.00', 'https://cake-dance.ru/wp-content/uploads/c/1/2/c120eab488a8756224236d756b217389.jpeg'),
(16, 'Пирожки с мясом', 'Сочные пирожки с мясом', '1.20', 'https://i.ytimg.com/vi/5ag6RWv8khY/maxresdefault.jpg'),
(17, 'Брауни', 'Плотные шоколадные брауни', '2.50', 'https://media-manager.noticiasaominuto.com/1280/naom_61278130665cf.jpg?crop_params=eyJsYW5kc2NhcGUiOnsiY3JvcFdpZHRoIjoyNDg3LCJjcm9wSGVpZ2h0IjoxMzk5LCJjcm9wWCI6NDksImNyb3BZIjoyNzF9fQ=='),
(19, 'Вафли', 'Хрустящие вафли с начинкой', '1.80', 'https://i.ytimg.com/vi/uAYMDRmeOQE/maxresdefault.jpg'),
(20, 'Слоеный пирог', 'Пышный слоеный пирог с начинкой', '3.50', 'https://i.ytimg.com/vi/M1nc_Tb1z_s/maxresdefault.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `Roles`
--

CREATE TABLE `Roles` (
  `id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Roles`
--

INSERT INTO `Roles` (`id`, `title`) VALUES
(1, 'Администратор'),
(2, 'Пекарь'),
(3, 'Курьер'),
(4, 'Пользователь'),
(5, 'Удален');

-- --------------------------------------------------------

--
-- Структура таблицы `Shifts`
--

CREATE TABLE `Shifts` (
  `id` int(11) NOT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Shifts`
--


-- --------------------------------------------------------

--
-- Структура таблицы `Shift_Participation`
--

CREATE TABLE `Shift_Participation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Shift_Participation`
--


-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `authentication_token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Cart`
--
ALTER TABLE `Cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Shifts`
--
ALTER TABLE `Shifts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Shift_Participation`
--
ALTER TABLE `Shift_Participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Cart`
--
ALTER TABLE `Cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT для таблицы `Products`
--
ALTER TABLE `Products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `Roles`
--
ALTER TABLE `Roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `Shifts`
--
ALTER TABLE `Shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `Shift_Participation`
--
ALTER TABLE `Shift_Participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Cart`
--
ALTER TABLE `Cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `Shift_Participation`
--
ALTER TABLE `Shift_Participation`
  ADD CONSTRAINT `shift_participation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `shift_participation_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `Shifts` (`id`);

--
-- Ограничения внешнего ключа таблицы `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
