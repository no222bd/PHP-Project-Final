-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Värd: 10.209.1.120
-- Skapad: 30 nov 2014 kl 17:14
-- Serverversion: 5.5.32
-- PHP-version: 5.3.10-1ubuntu3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databas: `186084-quizzy`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `answerId` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `isCorrect` tinyint(1) NOT NULL,
  PRIMARY KEY (`answerId`),
  KEY `questionId` (`questionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=209 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `donequiz`
--

CREATE TABLE IF NOT EXISTS `donequiz` (
  `doneQuizId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `quizId` int(11) NOT NULL,
  `isComplete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doneQuizId`),
  KEY `userId` (`userId`,`quizId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `mediapath`
--

CREATE TABLE IF NOT EXISTS `mediapath` (
  `questionId` int(11) NOT NULL,
  `mediaPath` varchar(255) NOT NULL,
  PRIMARY KEY (`questionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `questionId` int(11) NOT NULL AUTO_INCREMENT,
  `quizId` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  PRIMARY KEY (`questionId`),
  KEY `quizId` (`quizId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `quiz`
--

CREATE TABLE IF NOT EXISTS `quiz` (
  `quizId` int(11) NOT NULL AUTO_INCREMENT,
  `creatorId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`quizId`),
  KEY `creatorId` (`creatorId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `useranswer`
--

CREATE TABLE IF NOT EXISTS `useranswer` (
  `userAnswerId` int(11) NOT NULL AUTO_INCREMENT,
  `doneQuizId` int(11) NOT NULL,
  `answerId` int(11) NOT NULL,
  PRIMARY KEY (`userAnswerId`),
  KEY `doneQuizId` (`doneQuizId`,`answerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;