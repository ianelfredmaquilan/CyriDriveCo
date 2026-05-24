<?php

DEFINE ('DB_HOST',     'mysql.railway.internal');
DEFINE ('DB_USER',     'root');
DEFINE ('DB_PASS', 'WcPxaesYmUdQjBcmizQuGWYhOyutIRzn');
DEFINE ('DB_NAME',     'railway');
DEFINE ('DB_PORT',     '3306');

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)
    OR die('Could not connect to MySQL: ' . mysqli_connect_error());

mysqli_set_charset($dbc, 'utf8');       
