<?php

require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;
$ref =  $ref = Uuid::uuid4();
$ref=$ref->toString();

$URL = "https://example.com".$_SERVER['REQUEST_URI'];
$baseURL = "https://example.com/";
$host = "https://example.com";

// Browser
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
    $browser = 'Internet Explorer';
} elseif (strpos($userAgent, 'Firefox') !== false) {
    $browser = 'Mozilla Firefox';
} elseif (strpos($userAgent, 'Chrome') !== false) {
    $browser = 'Google Chrome';
} elseif (strpos($userAgent, 'Safari') !== false) {
    $browser = 'Apple Safari';
} elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
    $browser = 'Opera';
} else {
    $browser = 'Unknown Browser';
}

$shortURL = str_replace($baseURL, '', $URL);

function getLongURL($shortURL) {
    $dbHost = 'localhost';
    $dbName = 'database name';
    $dbUser = 'database use';
    $dbPass = 'database password';

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT long_url FROM short_urls WHERE short_code = :shortCode");
        $stmt->bindParam(':shortCode', $shortURL);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['long_url'])) {
            return $result['long_url'];
        } else {
            return false; // Short URL not found
        }
    } catch (PDOException $e) {
        // Handle database errors here
        echo "Error: " . $e->getMessage();
        return false;
    }
}

if (empty($shortURL)) {
    header("Location: start?i=".$ref."&src=".$browser);
}else{
    $longURL = getLongURL($shortURL);
    if ($longURL) {
        header("Location: $longURL", true, 301);
        exit();
    } else {
        exit("Invalid short URL");
    }
}

