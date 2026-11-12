<?php

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '5432';
$name = getenv('DB_DATABASE') ?: 'smart_mining';
$user = getenv('DB_USERNAME') ?: 'smart_mining';
$pass = getenv('DB_PASSWORD') ?: '';

try {
    new PDO(
        "pgsql:host={$host};port={$port};dbname={$name};connect_timeout=3",
        $user,
        $pass
    );
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
