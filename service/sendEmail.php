<?php

require __DIR__ . "/../vendor/autoload.php";

/**
 * SEND QUEUE
 */
$emailQueue = new \Source\Support\Email();
$emailQueue->sendQueue();