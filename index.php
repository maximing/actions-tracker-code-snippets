<?php

require_once 'vendor/autoload.php';
require_once 'ActionTracker.php';

$companyId = 10;
$taskId = 456;
$userId = 789;
$walletAddress = '0x45bff18af1e5eb4420913d9aa52aee78178e6345';
$privateKey = '45bff18af1e5eb4420913d9aa52aee78178e6345';

// click event track
$actionTracker = new ActionTracker(
    $companyId,
    $taskId,
    $userId,
    [
        ['nm' => 'click']
    ],
    $walletAddress,
    $privateKey
);

$actionTracker->trackAction();


// purchase event track
$actionTracker->setEvents([
    [
        'nm' => 'purchase',
        'am' => 1000
    ]
]);

$actionTracker->trackAction();