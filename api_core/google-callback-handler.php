<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/user_functions.php';

use Google\Service\Oauth2 as Google_Service_Oauth2;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = "/includes/google-callback.php";
$redirect_uri = $protocol . "://" . $host . $path;

$client->setRedirectUri($redirect_uri);

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
if (isset($token['error'])) {
    die('OAuth Error: '.$token['error_description']);
}
$client->setAccessToken($token);

$oauth = new Google_Service_Oauth2($client);
$userInfo = $oauth->userinfo->get();
$email    = $userInfo->email;
$name     = $userInfo->name;
$googleId = $userInfo->id;

// Cek di DB
$user = getUserByEmail($email);
if (!$user) {
    // auto-register tanpa password
    $username = strtolower(explode('@', $email)[0]);
    $roleFromDb = 'user'; // karena user baru
    $newId = insertUser([
      'username'  => $username,
      'password'  => '', 
      'email'     => $email,
      'google_id' => $googleId,
      'role'      => $roleFromDb
    ]);
    $userId = $newId;
    $_SESSION['username'] = $username;
} else {
    $userId     = $user['id'];
    $roleFromDb = $user['role']; // ambil dari database

    // sync google_id jika kosong
    if (empty($user['google_id'])) {
      $stmt = $pdo->prepare("UPDATE users SET google_id = ? WHERE id = ?");
      $stmt->bindParam(1, $googleId, PDO::PARAM_STR);
      $stmt->bindParam(2, $userId, PDO::PARAM_INT);
      $stmt->execute();
    }

    $_SESSION['username'] = $user['username'];
}

// ✅ Tambahkan ini supaya session role ke-set!
$_SESSION['user_id'] = $userId;
$_SESSION['role']    = $roleFromDb;

// Redirect
header('Location: /pages/forum.php');
exit;