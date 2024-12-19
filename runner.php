<?php
/**
 * The runner, which is supposed to manage the worker in a screen session.
 */

$command = $argv[1] ?? null;

$stdout = fopen('php://stdout', 'wb');
$stderr = fopen('php://stderr', 'wb');

$screenName = "plesk-cron-task-reproducer-xyz";
$logPath = __DIR__ . '/worker.log';
$workerPath = __DIR__ . '/worker.php';

$listCommand = "screen -ls | grep $screenName";
$createCommand = "screen -L -Logfile $logPath -dmS $screenName php $workerPath";
$killCommand = "screen -S $screenName -X quit";
$exitCode = 0;

try {
    fwrite($stderr, "Runner started\n");

    if (null === $command) {
        fwrite($stderr, "No command given\n");
        fwrite($stderr, "Usage: php runner.php <start|stop|status>\n");
        $exitCode = 1;
    } elseif ("status" === $command) {
        $listOutput = shell_exec($listCommand);

        if (str_contains($listOutput, $screenName)) {
            fwrite($stderr, "Worker running\n");
        } else {
            fwrite($stderr, "Worker not running\n");
        }
    } elseif ("start" === $command) {
        $listOutput = shell_exec($listCommand);

        if (!str_contains($listOutput, $screenName)) {
            file_put_contents($logPath, '');

            fwrite($stderr, "Starting worker\n");
            shell_exec($createCommand);
        } else {
            fwrite($stderr, "Worker already running\n");
        }
    } elseif ("stop" === $command) {
        $listOutput = shell_exec($listCommand);

        if (!str_contains($listOutput, $screenName)) {
            fwrite($stderr, "Worker not running\n");
        } else {
            fwrite($stderr, "Stopping worker\n");
            shell_exec($killCommand);
        }
    } else {
        fwrite($stderr, "Unknown command\n");
        $exitCode = 1;
    }

    fwrite($stderr, "Runner finished\n");
} finally {
    fclose($stdout);
    fclose($stderr);

    exit($exitCode);
}
