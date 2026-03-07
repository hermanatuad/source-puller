<?php
// Usage: php scripts/truncate_dw.php --yes
// WARNING: destructive. Requires explicit --yes confirmation.

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$yes = false;
foreach ($argv as $arg) {
    if ($arg === '--yes' || $arg === '-y') $yes = true;
}

if (!$yes) {
    echo "Destructive operation. To proceed, re-run with --yes flag.\n";
    exit(1);
}

// Try to autoload classes (composer)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    echo "Cannot find vendor/autoload.php. Run from project root or install dependencies.\n";
    exit(1);
}
require $autoload;

use app\helpers\DWHelper;

$config = DWHelper::getConfig();
$host = $config['hostname'] ?? null;
$port = $config['port'] ?? 5432;
$db = $config['database'] ?? null;
$user = $config['username'] ?? null;
$pass = $config['password'] ?? null;

if (!$host || !$db || !$user) {
    echo "Missing DW credentials in DWHelper::getConfig()\n";
    exit(1);
}

$dsn = "pgsql:host={$host};port={$port};dbname={$db}";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    echo "Failed to connect: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    echo "Setting session_replication_role = 'replica'...\n";
    $pdo->exec("SET session_replication_role = 'replica';");

    echo "Fetching tables in public schema...\n";
    $stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "No tables found in public schema.\n";
    } else {
        foreach ($tables as $t) {
            $tq = str_replace('"', '""', $t);
            $sql = "TRUNCATE TABLE \"{$tq}\" CASCADE;";
            echo "Truncating {$t}...\n";
            $pdo->exec($sql);
        }
    }

    echo "Restoring session_replication_role = 'origin'...\n";
    $pdo->exec("SET session_replication_role = 'origin';");

    echo "Done. All tables truncated.\n";
} catch (Exception $e) {
    echo "Error during truncate: " . $e->getMessage() . "\n";
    // Try to restore
    try { $pdo->exec("SET session_replication_role = 'origin';"); } catch (Exception $ex) {}
    exit(1);
}
