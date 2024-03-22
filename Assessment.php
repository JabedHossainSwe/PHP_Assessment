<?php
// Main function to calculate total time required for each person to fill up their water bottle
function calculateFillTime($queue, $numTaps) {
    // Input validation
    if (!is_array($queue) || !array_filter($queue, 'is_int') || $numTaps <= 0 || !is_int($numTaps)) {
        throw new InvalidArgumentException("Invalid input provided.");
    }

    $totalTime = 0;

    while (!empty($queue)) {
        // Calculate the time taken by the person at the front of the queue to fill their bottle
        $timeTaken = array_shift($queue) / 100; // 100ml per second
        // Increment total time by time taken to fill the bottle
        $totalTime += $timeTaken;
        // Reduce the number of available taps by 1
        $numTaps--;

        // If there are still people in the queue and taps available, continue filling bottles
        if (!empty($queue) && $numTaps > 0) {
            // Calculate the minimum of remaining queue length and available taps
            $numFillings = min(count($queue), $numTaps);
            // Calculate the time it takes for these fillings (as they occur concurrently)
            $concurrentTime = max(array_slice($queue, 0, $numFillings)) / 100; // Maximum bottle size determines the concurrent time
            $totalTime += $concurrentTime;
            // Remove the processed people from the queue
            $queue = array_slice($queue, $numFillings);
        }
    }

    return $totalTime;
}

// Input validation function
function validateInputs($queue, $numTaps) {
    if (!is_array($queue) || !array_filter($queue, 'is_int') || $numTaps <= 0 || !is_int($numTaps)) {
        throw new InvalidArgumentException("Invalid input provided.");
    }
}

// Function to calculate fill time considering walking time
function calculateFillTimeWithWalk($queue, $numTaps, $walkTime) {
    // Input validation
    validateInputs($queue, $numTaps);
    if (!is_int($walkTime) || $walkTime < 0) {
        throw new InvalidArgumentException("Walk time must be a non-negative integer.");
    }

    $totalTime = 0;

    while (!empty($queue)) {
        // Calculate the time taken by the person at the front of the queue to walk to the tap
        $totalTime += $walkTime;
        // Calculate the time taken by the person at the front of the queue to fill their bottle
        $timeTaken = array_shift($queue) / 100; // 100ml per second
        // Increment total time by time taken to fill the bottle
        $totalTime += $timeTaken;
        // Reduce the number of available taps by 1
        $numTaps--;

        // If there are still people in the queue and taps available, continue filling bottles
        if (!empty($queue) && $numTaps > 0) {
            // Calculate the minimum of remaining queue length and available taps
            $numFillings = min(count($queue), $numTaps);
            // Calculate the time it takes for these fillings (as they occur concurrently)
            $concurrentTime = max(array_slice($queue, 0, $numFillings)) / 100; // Maximum bottle size determines the concurrent time
            $totalTime += $concurrentTime;
            // Remove the processed people from the queue
            $queue = array_slice($queue, $numFillings);
        }
    }

    return $totalTime;
}

// Function to calculate fill time with different flow rates of taps
function calculateFillTimeWithDifferentFlowRates($queue, $tapFlowRates) {
    // Input validation
    if (!is_array($queue) || !array_filter($queue, 'is_int') || !is_array($tapFlowRates) || !array_filter($tapFlowRates, 'is_int') || count($tapFlowRates) < 1) {
        throw new InvalidArgumentException("Invalid input provided.");
    }

    $totalTime = 0;

    while (!empty($queue)) {
        // Calculate the time taken by the person at the front of the queue to fill their bottle
        $timeTaken = array_shift($queue) / $tapFlowRates[0]; // Use the first tap's flow rate
        // Increment total time by time taken to fill the bottle
        $totalTime += $timeTaken;

        // If there are still people in the queue, continue filling bottles
        if (!empty($queue)) {
            // Find the tap with the minimum time to fill the next bottle
            $minTime = PHP_INT_MAX;
            $minTapIndex = -1;
            foreach ($tapFlowRates as $i => $rate) {
                if ($rate * count($queue) < $minTime) {
                    $minTime = $rate * count($queue);
                    $minTapIndex = $i;
                }
            }

            // Calculate the time it takes for the next person to fill their bottle using the chosen tap
            $timeTaken = array_shift($queue) / $tapFlowRates[$minTapIndex];
            // Increment total time by time taken to fill the bottle
            $totalTime += $timeTaken;
        }
    }

    return $totalTime;
}

// Example usage:
$queue = [400, 750, 1000];
$numTaps = 1;

try {
    $fillTime = calculateFillTime($queue, $numTaps);
    echo "Total time to fill bottles without walking time: $fillTime seconds\n";

    $walkTime = 2; // 2 seconds walking time
    $fillTimeWithWalk = calculateFillTimeWithWalk($queue, $numTaps, $walkTime);
    echo "Total time to fill bottles with walking time: $fillTimeWithWalk seconds\n";

    $tapFlowRates = [100]; // Flow rate of tap (ml per second)
    $fillTimeWithDiffFlow = calculateFillTimeWithDifferentFlowRates($queue, $tapFlowRates);
    echo "Total time to fill bottles with different flow rates: $fillTimeWithDiffFlow seconds\n";
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage();
}
?>
