<?php

namespace app\helpers;

use Exception;
use mysqli;
use Yii;
use yii\db\mssql\PDO;

/**
 * DWHelper contains simple utility functions
 */
class DWHelper
{
    /**
     * Format number to Indonesian Rupiah currency format
     * 
     * @param float|int $amount The amount to format
     * @param bool $showSymbol Whether to show Rp symbol (default: true)
     * @param int $decimals Number of decimal places (default: 0)
     * @return string Formatted currency string
     */

    public static function getDWInfoFromCache($cacheTTL = 3600)
    {
        $hostname = getenv('postgres_host') ?? '';
        $username = getenv('postgres_user') ?? '';
        $port     = getenv('postgres_port') ?? 5432;
        $database = getenv('postgres_db') ?? '';

        // Validate required parameters for cache lookup
        $missing = [];
        if (empty($hostname)) {
            $missing[] = 'hostname';
        }
        if (empty($username)) {
            $missing[] = 'username';
        }
        if (empty($database)) {
            $missing[] = 'database';
        }
        if (empty($port) && $port !== 0) {
            $missing[] = 'port';
        }

        if (!empty($missing)) {
            return [
                'status' => 'error',
                'message' => 'Missing required parameter(s) for cache lookup: ' . implode(', ', $missing),
                'data' => [
                    'hostname' => $hostname,
                    'port' => $port,
                    'username' => $username,
                    'database' => $database,
                ]
            ];
        }

        $cacheKey = 'mysql_schema_' . md5("$hostname:$port:$username:$database");

        $cachedData = self::getFromCache($cacheKey);

        if ($cachedData === null) {
            return [
                'status' => 'error',
                'message' => 'Cache not found for system_code: datawarehouse'
            ];
        }

        // Cek TTL
        if (time() - $cachedData['cached_at'] >= $cacheTTL) {
            return [
                'status' => 'error',
                'message' => 'Cache expired for system_code: ',
                'cache_info' => [
                    'cached_at' => date('Y-m-d H:i:s', $cachedData['cached_at']),
                    'expired_at' => date('Y-m-d H:i:s', $cachedData['cached_at'] + $cacheTTL)
                ]
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Successfully retrieved database info from cache datawarehouse',
            'cache_info' => [
                'cached_at' => date('Y-m-d H:i:s', $cachedData['cached_at']),
                'expires_at' => date('Y-m-d H:i:s', $cachedData['cached_at'] + $cacheTTL)
            ],
            'result' => $cachedData
        ];
    }

    public static function testConDW()
    {

        $hostname = getenv('postgres_host') ?? '';
        $username = getenv('postgres_user') ?? '';
        $port     = getenv('postgres_port') ?? 5432;
        $password = getenv('postgres_password') ?? '';
        $database = getenv('postgres_db') ?? '';

        $useCache = true;
        $cacheTTL = 3600;

        $missing = [];

        if (empty($hostname)) $missing[] = 'hostname';
        if (empty($username)) $missing[] = 'username';
        if (empty($database)) $missing[] = 'database';
        if (empty($port) && $port !== 0) $missing[] = 'port';

        if (!empty($missing)) {
            return [
                'status' => 'error',
                'message' => 'Missing required parameter(s): ' . implode(', ', $missing)
            ];
        }

        $cacheKey = 'pgsql_schema_' . md5("$hostname:$port:$username:$database");

        if ($useCache) {
            $cachedData = self::getFromCache($cacheKey);
            if ($cachedData && time() - $cachedData['cached_at'] < $cacheTTL) {
                return [
                    'status' => 'success',
                    'message' => 'Successfully retrieved from cache',
                    'data' => $cachedData['data']
                ];
            }
        }

        try {
            $dsn = "pgsql:host=$hostname;port=$port;dbname=$database";

            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $dbInfo        = self::getDatabaseInfo($pdo, $database);
            $tablesInfo    = self::getTablesInfo($pdo);
            $additional    = self::getAdditionalInfo($pdo);

            $result = [
                'status' => 'success',
                'message' => 'Successfully connected to PostgreSQL',
                'data' => [
                    'connection' => [
                        'hostname' => $hostname,
                        'port' => $port,
                        'username' => $username,
                        'database' => $database,
                        'server_version' => $pdo->query("SHOW server_version")->fetchColumn()
                    ],
                    'database_info' => $dbInfo,
                    'tables' => $tablesInfo,
                    'additional_info' => $additional
                ]
            ];

            if ($useCache) {
                self::saveToCache($cacheKey, [
                    'data' => $result['data'],
                    'cached_at' => time()
                ]);
            }

            return $result;
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private static function getDatabaseInfo($pdo, $database)
    {
        $info = [
            'name' => $database,
            'size_mb' => 0,
            'table_count' => 0
        ];

        $size = $pdo->query("
        SELECT pg_database_size('$database')
        ")->fetchColumn();

        $info['size_mb'] = round($size / 1024 / 1024, 2);

        $count = $pdo->query("
        SELECT COUNT(*) 
        FROM information_schema.tables 
        WHERE table_schema = 'public'
        ")->fetchColumn();

        $info['table_count'] = $count;

        return $info;
    }

    private static function getTablesInfo($pdo)
    {
        $tables = [];

        $stmt = $pdo->query("
        SELECT table_name
        FROM information_schema.tables
        WHERE table_schema = 'public'
        ORDER BY table_name
        ");

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $tableName = $row['table_name'];

            $size = $pdo->query("
            SELECT pg_total_relation_size('$tableName')
            ")->fetchColumn();

            $columns = self::getColumnsInfo($pdo, $tableName);

            $tables[$tableName] = [
                'name' => $tableName,
                'total_size_mb' => round($size / 1024 / 1024, 2),
                'columns_count' => count($columns),
                'columns' => $columns
            ];
        }

        return $tables;
    }

    private static function getColumnsInfo($pdo, $tableName)
    {
        $columns = [];

        $stmt = $pdo->query("
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_name = '$tableName'
        ORDER BY ordinal_position
        ");

        while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = [
                'name' => $col['column_name'],
                'data_type' => $col['data_type'],
                'nullable' => $col['is_nullable'] === 'YES',
                'default' => $col['column_default']
            ];
        }

        return $columns;
    }

    private static function getAdditionalInfo($pdo)
    {
        $info = [];

        $info['uptime'] = $pdo->query("
        SELECT date_trunc('second', now() - pg_postmaster_start_time())
        ")->fetchColumn();

        $info['connections'] = $pdo->query("
        SELECT numbackends FROM pg_stat_database 
        WHERE datname = current_database()
        ")->fetchColumn();

        $info['max_connections'] = $pdo->query("
        SHOW max_connections
        ")->fetchColumn();

        return $info;
    }

    private static function saveToCache($key, $data)
    {
        // Use Yii runtime directory for cache to avoid permission issues in source tree
        $cacheDir = Yii::getAlias('@runtime') . '/db_cache/';
        if (!is_dir($cacheDir)) {
            if (!@mkdir($cacheDir, 0755, true)) {
                // cannot create cache directory; skip caching silently
                return false;
            }
        }

        $cacheFile = $cacheDir . $key . '.cache';
        @file_put_contents($cacheFile, serialize($data));
        return true;
    }

    private static function getFromCache($key)
    {
        $cacheFile = Yii::getAlias('@runtime') . '/db_cache/' . $key . '.cache';

        if (file_exists($cacheFile)) {
            $contents = @file_get_contents($cacheFile);
            if ($contents === false) {
                return null;
            }
            $data = @unserialize($contents);
            return $data;
        }

        return null;
    }

    public static function clearCache($params)
    {
        $hostname = $params['hostname'] ?? '';
        $port = $params['port'] ?? '';
        $username = $params['username'] ?? '';
        $database = $params['database'] ?? '';

        $cacheKey = 'mysql_schema_' . md5("$hostname:$port:$username:$database");
        $cacheFile = Yii::getAlias('@runtime') . '/db_cache/' . $cacheKey . '.cache';

        if (file_exists($cacheFile)) {
            @unlink($cacheFile);
            return ['status' => 'success', 'message' => 'Cache cleared'];
        }

        return ['status' => 'error', 'message' => 'Cache not found'];
    }
}
