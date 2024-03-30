<?php

$siteUrl = 'https://example.com';

function generateRef() {
    $data = openssl_random_pseudo_bytes(16);

    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function generateShortURL($url) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $shortURL = '';

    for ($i = 0; $i < 6; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $shortURL .= $characters[$index];
    }

    return $shortURL;
}

function saveShortURL($shortURL, $longURL) {
    $ref = generateRef();
    $dbHost = 'localhost';
    $dbName = 'database name';
    $dbUser = 'database user';
    $dbPass = 'database password';

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO short_urls (ref, short_code, long_url) VALUES (:ref, :shortCode, :longURL)");
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':shortCode', $shortURL);
        $stmt->bindParam(':longURL', $longURL);
        $stmt->execute();
    } catch (PDOException $e) {
        exit("Error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $longURL = $_POST['longURL'];
    if (filter_var($longURL, FILTER_VALIDATE_URL)) {
        $shortURL = generateShortURL($longURL);
        saveShortURL($shortURL, $longURL);
        $response = "Short URL: <a href='$siteUrl/$shortURL' target='_blank' class='result-link'>$siteUrl/$shortURL</a>.";
    } else {
        $response = "Invalid URL";
    }

    echo $response;
}else{
    $response = "Bad Request";
    echo $response;
}

?>