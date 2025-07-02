<?php
// includes/google-login.php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = "/includes/google-callback.php";
$redirect_uri = $protocol . "://" . $host . $path;

$client->setRedirectUri($redirect_uri);
$client->addScope(['email','profile']);
$client->setPrompt('select_account');

header('Location: ' . $client->createAuthUrl());
exit;
