<?php

$config = [
    'private_key_type' => OPENSSL_KEYTYPE_EC,
    'curve_name' => 'prime256v1'
];

$key = openssl_pkey_new($config);

var_dump($key);

while ($msg = openssl_error_string()) {
    echo $msg . PHP_EOL;
}
