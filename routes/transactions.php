<?php

/**
 * All routes dealing with returning transaction information
 */

$app->get('/transactions/current', function() use ($container) {
    $transaction = new \IBL\Transaction($container['db_connection']);
    echo $transaction->getCurrent();
});
