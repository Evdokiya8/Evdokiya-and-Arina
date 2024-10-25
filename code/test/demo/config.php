<?php
if ($_SERVER["SERVER_NAME"] == '172.19.3.23') {
    $dbhost = '172.19.3.23';
    $dbuser = '';
    $dbpassword = '';
    $database = '';
} else {
    $dbhost = 'localhost';
    $dbuser = 'demo';
    $dbpassword = 'demo';
    $database = 'demo';
}
