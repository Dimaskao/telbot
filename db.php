<?php

$driver = 'mysql';
$host = 'localhost';
$db_name = 'telbot';
$db_user = 'root';
$db_pass = '11111111';// root for host | 11111111 for desktop
$charset = 'utf8';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try{$pdo = new PDO("$driver:host=$host;dbname=$db_name;charset=$charset", $db_user, $db_pass, $options);
}catch(PDOException $e){
    die("Не могу подключится");
}
//$sql = 'INSERT INTO words_to_learn (`user_id`, `words`) VALUES (1, "help")';
//$result = $pdo->query('SELECT * FROM words_to_learn');
//$stmt = $pdo->prepare($sql);
//$stmt->execute();
//$row = $result->fetch(PDO::FETCH_ASSOC);
//echo '<pre>';
//var_dump($row);