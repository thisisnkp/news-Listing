<?php

/**
 * Laravel - Subdirectory Loader
 * Redirects all requests to public/index.php
 */

// Change to the public directory
chdir(__DIR__ . '/public');

// Include the Laravel entry point
require __DIR__ . '/public/index.php';