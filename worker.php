<?php

/**
 * The actual worker that is supposed to do stuff in the application.
 * For demo purposes, we will just randomly sleep 2-8 times and log something in between.
 */

$stdout = fopen('php://stdout', 'wb');
$stderr = fopen('php://stderr', 'wb');

try {
    fwrite($stderr, "Worker started\n");

    $iterations = random_int(2, 8);

    fwrite($stderr, "Worker will run $iterations iterations\n");

    for ($i = 0; $i < $iterations; $i++) {
        $sleepTime = random_int(1, 5);

        fwrite($stderr, "Sleeping for $sleepTime seconds\n");

        sleep($sleepTime);

        $randomData = bin2hex(random_bytes(16));

        fwrite($stdout, $randomData);
        fwrite($stderr, "Iteration $i\n");
    }

    fwrite($stderr, "Worker finished\n");
} finally {
    fclose($stdout);
    fclose($stderr);
}
