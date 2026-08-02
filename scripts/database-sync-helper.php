<?php

function databaseSyncFail($message)
{
    fwrite(STDERR, "DATABASE SYNC BLOCKED: {$message}\n");
    exit(1);
}

function databaseSyncIsLoopbackHost($host)
{
    return in_array(strtolower($host), ['127.0.0.1', 'localhost', '::1'], true);
}

function databaseSyncBootstrap()
{
    $projectRoot = dirname(__DIR__);

    require $projectRoot . '/vendor/autoload.php';
    $app = require $projectRoot . '/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    if (config('database.default') !== 'mysql') {
        databaseSyncFail('Only the MySQL connection is supported.');
    }

    return $app;
}

function databaseSyncConnectionConfig($expectedEnvironment)
{
    databaseSyncBootstrap();

    $environment = (string) config('app.env');
    $connection = (array) config('database.connections.mysql');
    $host = (string) ($connection['host'] ?? '');
    $database = (string) ($connection['database'] ?? '');

    if ($environment !== $expectedEnvironment) {
        databaseSyncFail(
            "Expected APP_ENV={$expectedEnvironment}; actual environment is {$environment}."
        );
    }

    if ($expectedEnvironment === 'local' && !databaseSyncIsLoopbackHost($host)) {
        databaseSyncFail("Local database host is not loopback: {$host}");
    }

    if ($database === '') {
        databaseSyncFail('Database name is empty.');
    }

    foreach (['host', 'port', 'username', 'password', 'database'] as $key) {
        $value = (string) ($connection[$key] ?? '');
        if (strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
            databaseSyncFail("Database setting contains a newline: {$key}");
        }
    }

    return [
        'environment' => $environment,
        'host' => $host,
        'port' => (string) ($connection['port'] ?? '3306'),
        'username' => (string) ($connection['username'] ?? ''),
        'password' => (string) ($connection['password'] ?? ''),
        'database' => $database,
    ];
}

function databaseSyncMysqlOptionValue($value)
{
    return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $value) . '"';
}

function databaseSyncWriteClientConfig($path, $expectedEnvironment)
{
    $config = databaseSyncConnectionConfig($expectedEnvironment);
    $directory = dirname($path);

    if (!is_dir($directory) || is_link($directory)) {
        databaseSyncFail("Client-config directory is unsafe: {$directory}");
    }

    $handle = @fopen($path, 'x');
    if ($handle === false) {
        databaseSyncFail("Client config already exists or cannot be created: {$path}");
    }

    $contents = "[client]\n"
        . 'host=' . databaseSyncMysqlOptionValue($config['host']) . "\n"
        . 'port=' . databaseSyncMysqlOptionValue($config['port']) . "\n"
        . 'user=' . databaseSyncMysqlOptionValue($config['username']) . "\n"
        . 'password=' . databaseSyncMysqlOptionValue($config['password']) . "\n"
        . "default-character-set=utf8mb4\n";

    if (fwrite($handle, $contents) === false || !fclose($handle) || !chmod($path, 0600)) {
        @unlink($path);
        databaseSyncFail("Unable to publish client config: {$path}");
    }
}

function databaseSyncQuoteIdentifier($identifier)
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function databaseSyncViewNames($expectedEnvironment)
{
    $config = databaseSyncConnectionConfig($expectedEnvironment);
    $views = DB::select(
        'SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME',
        [$config['database']]
    );

    foreach ($views as $view) {
        echo $view->TABLE_NAME . PHP_EOL;
    }
}

function databaseSyncViewSql($expectedEnvironment)
{
    $config = databaseSyncConnectionConfig($expectedEnvironment);
    $views = DB::select(
        'SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME',
        [$config['database']]
    );

    echo "SET FOREIGN_KEY_CHECKS=0;\n";
    foreach ($views as $view) {
        $name = $view->TABLE_NAME;
        $result = (array) DB::select('SHOW CREATE VIEW ' . databaseSyncQuoteIdentifier($name))[0];
        $create = array_values($result)[1];
        echo 'DROP VIEW IF EXISTS ' . databaseSyncQuoteIdentifier($name) . ";\n";
        echo $create . ";\n";
    }
    echo "SET FOREIGN_KEY_CHECKS=1;\n";
}

function databaseSyncCounts($expectedEnvironment, $allowErrors)
{
    databaseSyncConnectionConfig($expectedEnvironment);
    $objects = DB::select('SHOW FULL TABLES');
    $errors = 0;

    foreach ($objects as $object) {
        $name = array_values((array) $object)[0];
        try {
            $result = DB::select(
                'SELECT COUNT(*) AS aggregate FROM ' . databaseSyncQuoteIdentifier($name)
            );
            echo $name . '|' . $result[0]->aggregate . PHP_EOL;
        } catch (Throwable $error) {
            echo $name . '|ERROR' . PHP_EOL;
            $errors++;
        }
    }

    if ($errors > 0 && !$allowErrors) {
        databaseSyncFail("{$errors} database objects could not be read.");
    }
}

function databaseSyncNormalizeValue($value, $type)
{
    if ($value === null) {
        return null;
    }

    $value = (string) $value;
    if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)/i', $type)) {
        return (string) (int) $value;
    }

    if (preg_match('/^(decimal|numeric|double|float|real)/i', $type)) {
        if (stripos($value, 'e') !== false) {
            $value = sprintf('%.14F', (float) $value);
        }
        if (strpos($value, '.') !== false) {
            $value = rtrim(rtrim($value, '0'), '.');
        }

        return $value === '-0' ? '0' : $value;
    }

    return $value;
}

function databaseSyncDigests($expectedEnvironment)
{
    databaseSyncConnectionConfig($expectedEnvironment);
    $tables = [
        'trt_bimbingan',
        'trt_hasil',
        'trt_jadwal_ujian',
        'trt_jadwal_ujian_per_mhs',
        'trt_penguji',
        'trt_reg',
        'trt_syarat_ujian',
        'users',
    ];
    $pdo = DB::connection()->getPdo();

    foreach ($tables as $table) {
        $columns = [];
        $types = [];
        foreach ($pdo->query('SHOW COLUMNS FROM ' . databaseSyncQuoteIdentifier($table)) as $column) {
            $columns[] = $column['Field'];
            $types[$column['Field']] = $column['Type'];
        }

        $primary = [];
        $keys = $pdo->query(
            "SHOW KEYS FROM " . databaseSyncQuoteIdentifier($table) . " WHERE Key_name = 'PRIMARY'"
        );
        foreach ($keys as $key) {
            $primary[(int) $key['Seq_in_index']] = $key['Column_name'];
        }
        ksort($primary);
        $order = $primary ?: $columns;
        $sql = 'SELECT * FROM ' . databaseSyncQuoteIdentifier($table)
            . ' ORDER BY ' . implode(', ', array_map('databaseSyncQuoteIdentifier', $order));
        $statement = $pdo->query($sql);
        $hash = hash_init('sha256');
        $rows = 0;

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            hash_update($hash, "R;");
            foreach ($columns as $column) {
                $value = databaseSyncNormalizeValue($row[$column], $types[$column]);
                hash_update(
                    $hash,
                    $value === null ? "N;" : 'S' . strlen($value) . ':' . $value . ';'
                );
            }
            $rows++;
        }

        echo $table . '|' . $rows . '|' . hash_final($hash) . PHP_EOL;
    }
}

function databaseSyncLocalizeDump($source, $target)
{
    if (!is_file($source) || $source === $target || file_exists($target)) {
        databaseSyncFail('Unsafe dump localization paths.');
    }

    $input = @gzopen($source, 'rb');
    $output = @gzopen($target, 'wb9');
    if ($input === false || $output === false) {
        databaseSyncFail('Unable to open a compressed database dump.');
    }

    $replacements = 0;
    while (!gzeof($input)) {
        $line = gzgets($input);
        if ($line === false) {
            break;
        }
        $line = preg_replace(
            '/DEFINER=`[^`]+`@`[^`]+`/',
            'DEFINER=CURRENT_USER',
            $line,
            -1,
            $lineReplacements
        );
        $replacements += $lineReplacements;
        if (gzwrite($output, $line) === false) {
            databaseSyncFail('Unable to write the localized database dump.');
        }
    }

    gzclose($input);
    gzclose($output);
    chmod($target, 0600);
    echo $replacements . PHP_EOL;
}

function databaseSyncMain(array $arguments)
{
    $command = $arguments[1] ?? '';

    switch ($command) {
        case 'metadata':
            $field = $arguments[2] ?? '';
            $config = databaseSyncConnectionConfig($arguments[3] ?? '');
            if (!array_key_exists($field, $config) || $field === 'password') {
                databaseSyncFail("Unavailable metadata field: {$field}");
            }
            echo $config[$field] . PHP_EOL;
            break;
        case 'client-config':
            databaseSyncWriteClientConfig($arguments[2] ?? '', $arguments[3] ?? '');
            break;
        case 'view-names':
            databaseSyncViewNames($arguments[2] ?? '');
            break;
        case 'view-sql':
            databaseSyncViewSql($arguments[2] ?? '');
            break;
        case 'counts':
            databaseSyncCounts(
                $arguments[2] ?? '',
                in_array('--allow-errors', $arguments, true)
            );
            break;
        case 'digests':
            databaseSyncDigests($arguments[2] ?? '');
            break;
        case 'localize':
            databaseSyncLocalizeDump($arguments[2] ?? '', $arguments[3] ?? '');
            break;
        default:
            databaseSyncFail("Unknown helper command: {$command}");
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    databaseSyncMain($argv);
}
