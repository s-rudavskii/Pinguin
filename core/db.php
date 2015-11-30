<?php

$db = new PDO(
  'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
  getenv('DB_USER'),
  getenv('DB_PASS'),
  array(
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
  )
);