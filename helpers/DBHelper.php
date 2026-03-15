<?php

namespace app\helpers;

use Exception;
use mysqli;
use Yii;

/**
 * DBHelper contains simple utility functions
 */
class DBHelper
{
    /**
     * Format number to Indonesian Rupiah currency format
     * 
     * @param float|int $amount The amount to format
     * @param bool $showSymbol Whether to show Rp symbol (default: true)
     * @param int $decimals Number of decimal places (default: 0)
     * @return string Formatted currency string
     */

    public static function getDatabaseInfoFromCache($params, $cacheTTL = 3600)
    {
        $systemCode = $params['system_code'] ?? '';
        $hostname = $params['hostname'] ?? '';
        $username = $params['username'] ?? '';
        $port = $params['port'] ?? '';
        $database = $params['database_name'] ?? '';

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
                    'system_code' => $systemCode,
                    'hostname' => $hostname,
                    'port' => $port,
                    'username' => $username,
                    'database' => $database,
                ]
            ];
        }

        $cacheKey = 'mysql_schema_' . md5("{$hostname}:{$port}:{$username}:{$database}");

        $cachedData = self::getFromCache($cacheKey);

        $needRefresh = false;
        if ($cachedData === null) {
            $needRefresh = true;
        } else {
            if (time() - $cachedData['cached_at'] >= $cacheTTL) {
                $needRefresh = true;
            }
        }

        if ($needRefresh) {
            $callParams = [
                'system_code' => $systemCode,
                'hostname' => $hostname,
                'username' => $username,
                'password' => $params['password'] ?? '',
                'port' => $port,
                'database' => $database,
                'use_cache' => false,
                'cache_ttl' => $cacheTTL,
            ];

            $fresh = self::testConMysql($callParams);

            if (!is_array($fresh) || ($fresh['status'] ?? '') !== 'success') {
                if ($cachedData === null) {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to retrieve live data and no cache available',
                        'details' => $fresh
                    ];
                }

                return [
                    'status' => 'warning',
                    'message' => 'Using stale cache; failed to refresh live data',
                    'cache_info' => [
                        'cached_at' => date('Y-m-d H:i:s', $cachedData['cached_at']),
                    ],
                    'result' => $cachedData['data'] ?? $cachedData
                ];
            }

            $cachePayload = ['data' => $fresh['data'], 'cached_at' => time()];
            self::saveToCache($cacheKey, $cachePayload);
            $cachedData = $cachePayload;
        }

        return [
            'status' => 'success',
            'message' => 'Successfully retrieved database info',
            'cache_info' => [
                'cached_at' => date('Y-m-d H:i:s', $cachedData['cached_at']),
                'expires_at' => date('Y-m-d H:i:s', $cachedData['cached_at'] + $cacheTTL)
            ],
            'result' => $cachedData['data'] ?? $cachedData
        ];
    }

    public static function testConMysql($params)
    {
        // Ekstrak parameter dengan default values
        $systemCode = $params['system_code'] ?? '';
        $hostname = $params['hostname'] ?? '';
        $username = $params['username'] ?? '';
        $port = $params['port'] ?? '';
        $password = $params['password'] ?? '';
        $database = $params['database'] ?? '';
        $useCache = $params['use_cache'] ?? true; // Parameter untuk mengontrol cache
        $cacheTTL = $params['cache_ttl'] ?? 3600; // Cache Time To Live dalam detik (default 1 jam)

        // Validasi parameter wajib
        $missing = [];
        if (empty($systemCode)) {
            $missing[] = 'system_code';
        }
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
                'message' => 'Missing required parameter(s): ' . implode(', ', $missing),
                'data' => [
                    'hostname' => $hostname,
                    'port' => $port,
                    'username' => $username,
                    'database' => $database
                ]
            ];
        }

        // Buat cache key unik berdasarkan parameter koneksi
        $cacheKey = 'mysql_schema_' . md5("$hostname:$port:$username:$database");

        // Cek cache jika diaktifkan
        if ($useCache) {
            $cachedData = self::getFromCache($cacheKey);
            // echo '<pre>';print_r();exit;
            if ($cachedData !== null) {
                // Cek apakah cache masih valid (belum expired)
                if (time() - $cachedData['cached_at'] < $cacheTTL) {
                    return [
                        'status' => 'success',
                        'message' => 'Successfully retrieved from cache',
                        'data' => $cachedData['data'],
                        'cache_info' => [
                            'used_cache' => true,
                            'cached_at' => date('Y-m-d H:i:s', $cachedData['cached_at']),
                            'expires_at' => date('Y-m-d H:i:s', $cachedData['cached_at'] + $cacheTTL)
                        ]
                    ];
                }
            }
        }

        try {
            // Buat koneksi
            $mysqli = new mysqli($hostname, $username, $password, $database, $port);

            // Cek error koneksi
            if ($mysqli->connect_error) {
                throw new Exception("Connection failed: " . $mysqli->connect_error);
            }

            // Ambil informasi database
            $dbInfo = self::getDatabaseInfo($mysqli, $database);

            // Ambil informasi tabel dan kolom
            $tablesInfo = self::getTablesInfo($mysqli, $database);

            // Ambil informasi tambahan
            $additionalInfo = self::getAdditionalInfo($mysqli);

            // Koneksi berhasil
            $result = [
                'status' => 'success',
                'message' => 'Successfully connected to MySQL',
                'data' => [
                    'connection' => [
                        'hostname' => $hostname,
                        'port' => $port,
                        'username' => $username,
                        'database' => $database,
                        'server_info' => $mysqli->server_info,
                        'server_version' => $mysqli->query("SELECT VERSION()")->fetch_row()[0],
                        'host_info' => $mysqli->host_info,
                        'protocol_version' => $mysqli->protocol_version,
                        'thread_id' => $mysqli->thread_id,
                        'character_set' => $mysqli->character_set_name()
                    ],
                    'database_info' => $dbInfo,
                    'tables' => $tablesInfo,
                    'additional_info' => $additionalInfo
                ]
            ];

            $mysqli->close();

            // Simpan ke cache
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
                'message' => $e->getMessage(),
                'data' => [
                    'hostname' => $hostname,
                    'port' => $port,
                    'username' => $username,
                    'database' => $database
                ]
            ];
        }
    }

    private static function getDatabaseInfo($mysqli, $database)
    {
        $info = [
            'name' => $database,
            'size_mb' => 0,
            'table_count' => 0,
            'charset' => null,
            'collation' => null
        ];

        // Ambil ukuran database
        $sizeQuery = "
        SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
            COUNT(*) AS table_count
        FROM information_schema.tables 
        WHERE table_schema = '$database'
    ";
        $sizeResult = $mysqli->query($sizeQuery);
        if ($sizeResult && $row = $sizeResult->fetch_assoc()) {
            $info['size_mb'] = $row['size_mb'] ?? 0;
            $info['table_count'] = $row['table_count'] ?? 0;
        }

        // Ambil default charset dan collation
        $charsetQuery = "
        SELECT 
            DEFAULT_CHARACTER_SET_NAME as charset,
            DEFAULT_COLLATION_NAME as collation
        FROM information_schema.schemata 
        WHERE schema_name = '$database'
    ";
        $charsetResult = $mysqli->query($charsetQuery);
        if ($charsetResult && $row = $charsetResult->fetch_assoc()) {
            $info['charset'] = $row['charset'];
            $info['collation'] = $row['collation'];
        }

        return $info;
    }

    private static function getTablesInfo($mysqli, $database)
    {
        $tables = [];

        // Ambil daftar tabel
        $tablesResult = $mysqli->query("
        SELECT 
            TABLE_NAME,
            TABLE_TYPE,
            ENGINE,
            TABLE_ROWS,
            AVG_ROW_LENGTH,
            DATA_LENGTH,
            INDEX_LENGTH,
            CREATE_TIME,
            UPDATE_TIME,
            TABLE_COLLATION
        FROM information_schema.tables 
        WHERE table_schema = '$database'
        ORDER BY TABLE_NAME
    ");

        if ($tablesResult) {
            while ($table = $tablesResult->fetch_assoc()) {
                $tableName = $table['TABLE_NAME'];

                // Ambil kolom untuk tabel ini
                $columns = self::getColumnsInfo($mysqli, $database, $tableName);

                // Ambil index untuk tabel ini
                $indexes = self::getIndexesInfo($mysqli, $database, $tableName);

                // Ambil foreign keys
                $foreignKeys = self::getForeignKeysInfo($mysqli, $database, $tableName);

                // Ambil seluruh row data tabel untuk disimpan ke cache
                $tableDataRows = self::getTableRowsData($mysqli, $tableName);

                $tables[$tableName] = [
                    'name' => $tableName,
                    'type' => $table['TABLE_TYPE'],
                    'engine' => $table['ENGINE'],
                    'rows' => $table['TABLE_ROWS'],
                    'avg_row_length' => $table['AVG_ROW_LENGTH'],
                    'data_length_mb' => round($table['DATA_LENGTH'] / 1024 / 1024, 2),
                    'index_length_mb' => round($table['INDEX_LENGTH'] / 1024 / 1024, 2),
                    'total_size_mb' => round(($table['DATA_LENGTH'] + $table['INDEX_LENGTH']) / 1024 / 1024, 2),
                    'create_time' => $table['CREATE_TIME'],
                    'update_time' => $table['UPDATE_TIME'],
                    'collation' => $table['TABLE_COLLATION'],
                    'columns_count' => count($columns),
                    'indexes_count' => count($indexes),
                    'foreign_keys_count' => count($foreignKeys),
                    'columns' => $columns,
                    'indexes' => $indexes,
                    'foreign_keys' => $foreignKeys,
                    'data_rows_count' => count($tableDataRows),
                    'data_rows' => $tableDataRows
                ];
            }
        }

        return $tables;
    }

    private static function getTableRowsData($mysqli, $tableName)
    {
        $rows = [];
        $safeTableName = str_replace('`', '``', $tableName);
        $result = $mysqli->query("SELECT * FROM `{$safeTableName}`");

        if ($result === false) {
            return $rows;
        }

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();

        return $rows;
    }

    private static function getColumnsInfo($mysqli, $database, $tableName)
    {
        $columns = [];

        $columnsResult = $mysqli->query("
        SELECT 
            COLUMN_NAME,
            DATA_TYPE,
            COLUMN_TYPE,
            IS_NULLABLE,
            COLUMN_DEFAULT,
            COLUMN_KEY,
            EXTRA,
            CHARACTER_SET_NAME,
            COLLATION_NAME,
            COLUMN_COMMENT
        FROM information_schema.columns 
        WHERE table_schema = '$database' 
            AND TABLE_NAME = '$tableName'
        ORDER BY ORDINAL_POSITION
    ");

        if ($columnsResult) {
            while ($column = $columnsResult->fetch_assoc()) {
                $columns[] = [
                    'name' => $column['COLUMN_NAME'],
                    'data_type' => $column['DATA_TYPE'],
                    'column_type' => $column['COLUMN_TYPE'],
                    'nullable' => $column['IS_NULLABLE'] === 'YES',
                    'default' => $column['COLUMN_DEFAULT'],
                    'key' => $column['COLUMN_KEY'],
                    'extra' => $column['EXTRA'],
                    'charset' => $column['CHARACTER_SET_NAME'],
                    'collation' => $column['COLLATION_NAME'],
                    'comment' => $column['COLUMN_COMMENT']
                ];
            }
        }

        return $columns;
    }

    private static function getIndexesInfo($mysqli, $database, $tableName)
    {
        $indexes = [];

        $indexesResult = $mysqli->query("
        SELECT 
            INDEX_NAME,
            COLUMN_NAME,
            NON_UNIQUE,
            SEQ_IN_INDEX,
            CARDINALITY,
            INDEX_TYPE,
            COMMENT
        FROM information_schema.statistics 
        WHERE table_schema = '$database' 
            AND TABLE_NAME = '$tableName'
        ORDER BY INDEX_NAME, SEQ_IN_INDEX
    ");

        if ($indexesResult) {
            while ($index = $indexesResult->fetch_assoc()) {
                $indexes[] = [
                    'name' => $index['INDEX_NAME'],
                    'column' => $index['COLUMN_NAME'],
                    'unique' => $index['NON_UNIQUE'] == 0,
                    'sequence' => $index['SEQ_IN_INDEX'],
                    'cardinality' => $index['CARDINALITY'],
                    'type' => $index['INDEX_TYPE'],
                    'comment' => $index['COMMENT']
                ];
            }
        }

        return $indexes;
    }

    private static function getForeignKeysInfo($mysqli, $database, $tableName)
    {
        $foreignKeys = [];

        $fkQuery = "
        SELECT 
            k.CONSTRAINT_NAME,
            k.COLUMN_NAME,
            k.REFERENCED_TABLE_NAME,
            k.REFERENCED_COLUMN_NAME,
            c.UPDATE_RULE,
            c.DELETE_RULE
        FROM information_schema.KEY_COLUMN_USAGE k
        JOIN information_schema.REFERENTIAL_CONSTRAINTS c
            ON k.CONSTRAINT_NAME = c.CONSTRAINT_NAME
            AND k.CONSTRAINT_SCHEMA = c.CONSTRAINT_SCHEMA
        WHERE k.TABLE_SCHEMA = '$database' 
            AND k.TABLE_NAME = '$tableName'
            AND k.REFERENCED_TABLE_NAME IS NOT NULL
    ";

        $fkResult = $mysqli->query($fkQuery);
        if ($fkResult) {
            while ($fk = $fkResult->fetch_assoc()) {
                $foreignKeys[] = [
                    'constraint_name' => $fk['CONSTRAINT_NAME'],
                    'column' => $fk['COLUMN_NAME'],
                    'referenced_table' => $fk['REFERENCED_TABLE_NAME'],
                    'referenced_column' => $fk['REFERENCED_COLUMN_NAME'],
                    'update_rule' => $fk['UPDATE_RULE'],
                    'delete_rule' => $fk['DELETE_RULE']
                ];
            }
        }

        return $foreignKeys;
    }

    private static function getAdditionalInfo($mysqli)
    {
        $info = [
            'uptime' => null,
            'connections' => null,
            'queries' => null,
            'variables' => []
        ];

        // Ambil status
        $statusResult = $mysqli->query("SHOW GLOBAL STATUS LIKE 'Uptime'");
        if ($statusResult && $row = $statusResult->fetch_assoc()) {
            $info['uptime'] = $row['Value'] . ' seconds';
        }

        $statusResult = $mysqli->query("SHOW GLOBAL STATUS LIKE 'Connections'");
        if ($statusResult && $row = $statusResult->fetch_assoc()) {
            $info['connections'] = $row['Value'];
        }

        $statusResult = $mysqli->query("SHOW GLOBAL STATUS LIKE 'Questions'");
        if ($statusResult && $row = $statusResult->fetch_assoc()) {
            $info['queries'] = $row['Value'];
        }

        // Ambil beberapa variable penting
        $variables = [
            'max_connections',
            'max_allowed_packet',
            'wait_timeout',
            'interactive_timeout',
            'innodb_buffer_pool_size',
            'query_cache_size',
            'version_comment'
        ];

        foreach ($variables as $var) {
            $varResult = $mysqli->query("SHOW VARIABLES LIKE '$var'");
            if ($varResult && $row = $varResult->fetch_assoc()) {
                $info['variables'][$var] = $row['Value'];
            }
        }

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
