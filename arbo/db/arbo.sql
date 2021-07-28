-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2021. Júl 28. 08:29
-- Kiszolgáló verziója: 10.1.37-MariaDB-0+deb9u1
-- PHP verzió: 7.0.33-0+deb9u3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `arbo`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `devices`
--

CREATE TABLE `devices` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `mac_address` varchar(255) NOT NULL,
  `last_message` datetime NOT NULL,
  `place_id` smallint(5) UNSIGNED NOT NULL,
  `master` tinyint(1) NOT NULL,
  `closed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `errors`
--

CREATE TABLE `errors` (
  `id` mediumint(9) NOT NULL,
  `error` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `instructions`
--

CREATE TABLE `instructions` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `statuses_id` smallint(5) UNSIGNED NOT NULL,
  `receiver_id` smallint(6) NOT NULL,
  `instruction` tinyint(1) UNSIGNED NOT NULL COMMENT '0 = IDLE, 1 = Üzemállapot, 2 = Zárt állapot, 3 = Mérés, 4 = Savazó állapot, 5 = P1 reset, 6 = P2 reset, , 3 = Ellenállás, 9 = Hiba, 10 = Kézi üzemmód, 30 = V1 nyit , 31 = V1 zár , 32 = V2 nyit , 33 = V2 zár , 34 = V3 nyit , 35 = V3 zár , 36 = V4 nyit , 37 = V4 zár , 38 = V5 nyit , 39 = V5 zár , 40 = V6 nyit , 41 = V6 zár , 42 = V7 nyit , 43 = V7 zár , 44 = V8 nyit , 45 = V8 zár , 46 = KSZ indul , 47 = KSZ leáll',
  `state` tinyint(1) NOT NULL COMMENT '0 = Elküldve, 1 = Végrehajtás alatt, 2 = Végrehajtva, 3 = Elfogadva, 9 = Hiba',
  `result` double DEFAULT NULL,
  `date` datetime NOT NULL,
  `ready` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `logs`
--

CREATE TABLE `logs` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `device_id` smallint(5) UNSIGNED NOT NULL,
  `details` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `measurements`
--

CREATE TABLE `measurements` (
  `id` int(10) UNSIGNED NOT NULL,
  `source_id` smallint(5) UNSIGNED NOT NULL,
  `value` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `places`
--

CREATE TABLE `places` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ports`
--

CREATE TABLE `ports` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `virtual_name` varchar(255) NOT NULL,
  `port_num` tinyint(3) UNSIGNED NOT NULL,
  `port_type` tinyint(1) NOT NULL COMMENT '0=analóg, 1=digitális, 2=GPIO',
  `device_id` smallint(5) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `sort` tinyint(3) UNSIGNED NOT NULL,
  `unit` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `statuses`
--

CREATE TABLE `statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `instruction` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `user_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `units`
--

CREATE TABLE `units` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `name` varchar(10) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(1) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL DEFAULT '',
  `inactive` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `last_message` (`last_message`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `place_id` (`place_id`),
  ADD KEY `master` (`master`),
  ADD KEY `closed` (`closed`);

--
-- A tábla indexei `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state` (`state`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `date` (`date`),
  ADD KEY `statuses_id` (`statuses_id`);

--
-- A tábla indexei `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `device_id` (`device_id`);

--
-- A tábla indexei `measurements`
--
ALTER TABLE `measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_id` (`source_id`),
  ADD KEY `date` (`date`);

--
-- A tábla indexei `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `ports`
--
ALTER TABLE `ports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `virtual_name` (`virtual_name`),
  ADD KEY `device` (`device_id`),
  ADD KEY `sort` (`sort`);

--
-- A tábla indexei `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`),
  ADD KEY `instruction` (`instruction`),
  ADD KEY `status` (`status`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `type` (`type`),
  ADD KEY `password` (`password`),
  ADD KEY `inactive` (`inactive`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `devices`
--
ALTER TABLE `devices`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT a táblához `errors`
--
ALTER TABLE `errors`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3738;
--
-- AUTO_INCREMENT a táblához `logs`
--
ALTER TABLE `logs`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT a táblához `measurements`
--
ALTER TABLE `measurements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4081914;
--
-- AUTO_INCREMENT a táblához `places`
--
ALTER TABLE `places`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT a táblához `ports`
--
ALTER TABLE `ports`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT a táblához `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=528;
--
-- AUTO_INCREMENT a táblához `units`
--
ALTER TABLE `units`
  MODIFY `id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- phpMyAdmin
INSERT INTO `devices` (`id`, `name`, `ip_address`, `mac_address`, `last_message`, `place_id`, `master`, `closed`) VALUES(1, 'p1', '192.168.5.152', 'b8:27:eb:d4:3b:f9', '2021-06-29 11:29:43', 1, 0, 0);
INSERT INTO `devices` (`id`, `name`, `ip_address`, `mac_address`, `last_message`, `place_id`, `master`, `closed`) VALUES(2, 'p2', '192.168.5.151', 'b8:27:eb:36:a8:74', '2021-07-28 08:38:37', 1, 0, 0);
INSERT INTO `devices` (`id`, `name`, `ip_address`, `mac_address`, `last_message`, `place_id`, `master`, `closed`) VALUES(3, 'p3', '192.168.5.153', 'b8:27:eb:93:b7:ca', '2021-07-28 08:38:30', 1, 1, 1);

--
-- A tábla adatainak kiíratása `places`
--

INSERT INTO `places` (`id`, `name`) VALUES(1, 'Központ 1');

--
-- A tábla adatainak kiíratása `ports`
--

INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(1, 'G27', 0, 0, 1, 'T1 1. hőcser. bemenő hőmérs. (°C)', 0, 2);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(2, 'G25', 0, 0, 1, 'T2 1. hőcser. elmenő hőmérs. (°C)', 0, 2);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(3, 'G22', 0, 0, 1, 'V2 zárva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(4, 'G21', 0, 0, 1, 'V2 nyitva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(5, 'G20', 0, 0, 1, 'V4 zárva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(6, 'G19', 0, 0, 1, 'V4 nyitva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(7, 'G18', 0, 0, 1, 'V2 és V4 nyit/zár vezérlés', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(8, 'G17', 0, 0, 1, 'V1 és V3 nyit/zár vezérlés', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(9, 'G16', 0, 0, 1, 'V1 zárva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(10, 'G13', 0, 0, 1, 'V1 nyitva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(11, 'G12', 0, 0, 1, 'V3 zárva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(12, 'G6', 0, 0, 1, 'V3 nyitva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(15, 'G27', 0, 0, 2, 'T3 2. hőcser. bemenő hőmérs. (°C)', 0, 2);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(16, 'G25', 0, 0, 2, 'T4 2. hőcser. elmenő hőmérs. (°C)', 0, 2);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(17, 'G22', 0, 0, 2, 'V6 zárva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(18, 'G21', 0, 0, 2, 'V6 nyitva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(19, 'G20', 0, 0, 2, 'V8 zárva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(20, 'G19', 0, 0, 2, 'V8 nyitva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(21, 'G18', 0, 0, 2, 'V6 és V8 nyit/zár vezérlés', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(22, 'G17', 0, 0, 2, 'V5 és V7 nyit/zár (vezérlés)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(23, 'G16', 0, 0, 2, 'V5 zárva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(24, 'G13', 0, 0, 2, 'V5 nyitva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(25, 'G12', 0, 0, 2, 'V7 zárva végállás (alapállapot)', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(26, 'G6', 0, 0, 2, 'V7 nyitva végállás', 0, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(29, 'G20', 0, 0, 3, 'P1 reset', 4, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(30, 'G19', 0, 0, 3, 'P2 reset', 4, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(32, 'G17', 0, 0, 3, 'Átfolyásmérő LOW riasztás', 4, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(33, 'G16', 0, 0, 3, 'Átfolyásmérő HIGH riasztás', 4, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(34, 'G5', 0, 0, 3, 'Sav tartály riasztás', 1, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(35, 'G22', 0, 0, 3, 'Sav keringetőszivattyú', 4, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(36, 'A1', 0, 0, 3, 'pH1 előremenő oldat', 0, 3);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(37, 'A2', 0, 0, 3, 'pH2 visszatérő oldat', 0, 3);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(38, 'A3', 0, 0, 3, 'M4 gépterem bemenő áram (m<sup>3</sup>/h)', 0, 4);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(39, 'G12', 0, 0, 3, 'Sav tartály MAX', 2, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(40, 'G6', 0, 0, 3, 'Sav tartály MIN', 3, 1);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(41, 'A4', 0, 0, 3, 'Bemenő nyomás', 0, 4);
INSERT INTO `ports` (`id`, `virtual_name`, `port_num`, `port_type`, `device_id`, `description`, `sort`, `unit`) VALUES(42, 'A5', 0, 0, 3, 'Elmenő nyomás', 0, 4);

--
-- A tábla adatainak kiíratása `units`
--

INSERT INTO `units` (`id`, `name`) VALUES(1, 'H/L');
INSERT INTO `units` (`id`, `name`) VALUES(2, '°C');
INSERT INTO `units` (`id`, `name`) VALUES(3, 'pH');
INSERT INTO `units` (`id`, `name`) VALUES(4, 'bar');
INSERT INTO `units` (`id`, `name`) VALUES(5, 'm3/h');
INSERT INTO `units` (`id`, `name`) VALUES(6, 'Be/Ki');

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `type`, `fullname`, `inactive`) VALUES(1, 'admin', '04CC4D09A28F98A76F1B86A8AE031468', 0, 'Adminisztrátor', 0);
INSERT INTO `users` (`id`, `name`, `password`, `type`, `fullname`, `inactive`) VALUES(2, 'test', '04CC4D09A28F98A76F1B86A8AE031468', 1, 'Teszt felhasználó', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

