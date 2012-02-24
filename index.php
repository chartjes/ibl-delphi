<?php

require './bootstrap.php';

// Include our Slim routes
require './routes/main.php';
require './routes/transactions.php';

$app->run();

