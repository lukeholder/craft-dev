<?php
function accessGate() {
// Check if the access gate is disabled globally (i.e., if either username or password is empty)
    $validUsername = getenv('ACCESS_GATE_USER') ?: 'user';
    $validPassword = getenv('ACCESS_GATE_PASSWORD') ?: 'pass';

    if (empty($validUsername) || empty($validPassword)) {
        return; // Gate is disabled globally, so do nothing
    }

    return regularAccessGate($validUsername, $validPassword);
}

function regularAccessGate($validUsername, $validPassword) {
// Check if the user has provided a username and password
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
// If not, prompt the user for credentials
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authentication required';
        exit;
    } else {
// Validate the provided credentials
        if ($_SERVER['PHP_AUTH_USER'] === $validUsername && $_SERVER['PHP_AUTH_PW'] === $validPassword) {
// Authentication successful, allow access
            return;
        } else {
// If authentication fails, prompt again
            header('WWW-Authenticate: Basic realm="Restricted Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Invalid credentials';
            exit;
        }
    }
}

// Execute the access gate function
accessGate();
