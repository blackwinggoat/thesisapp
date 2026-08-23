<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemMailSettings
{
    const GROUP = 'mail';

    protected static $keys = [
        'enabled',
        'driver',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
    ];

    public static function defaults()
    {
        return [
            'enabled' => false,
            'driver' => Config::get('mail.driver', 'smtp'),
            'host' => Config::get('mail.host'),
            'port' => Config::get('mail.port', 587),
            'username' => Config::get('mail.username'),
            'password' => null,
            'encryption' => Config::get('mail.encryption', 'tls'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name', 'Thesis App FIKOM UMI'),
        ];
    }

    public static function all()
    {
        $settings = self::defaults();

        if (!self::tableReady()) {
            return $settings;
        }

        $rows = DB::table('system_settings')
            ->where('group', self::GROUP)
            ->whereIn('key', self::$keys)
            ->get();

        foreach ($rows as $row) {
            $key = $row->key;
            $value = $row->value;

            if ($row->is_encrypted && $value !== null && $value !== '') {
                try {
                    $value = Crypt::decrypt($value);
                } catch (\Exception $exception) {
                    $value = null;
                }
            }

            if ($key === 'enabled') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $settings[$key] = $value;
        }

        return $settings;
    }

    public static function apply()
    {
        $settings = self::all();

        if (empty($settings['enabled'])) {
            return;
        }

        Config::set('mail.driver', $settings['driver'] ?: 'smtp');
        Config::set('mail.host', $settings['host']);
        Config::set('mail.port', (int) ($settings['port'] ?: 587));
        Config::set('mail.username', $settings['username']);
        Config::set('mail.password', $settings['password']);
        Config::set('mail.encryption', $settings['encryption'] ?: null);
        Config::set('mail.from.address', $settings['from_address']);
        Config::set('mail.from.name', $settings['from_name'] ?: 'Thesis App FIKOM UMI');
    }

    public static function update(array $data)
    {
        $current = self::all();
        $password = trim((string) ($data['password'] ?? ''));
        $settings = [
            'enabled' => !empty($data['enabled']) ? '1' : '0',
            'driver' => trim((string) ($data['driver'] ?? 'smtp')),
            'host' => trim((string) ($data['host'] ?? '')),
            'port' => trim((string) ($data['port'] ?? '587')),
            'username' => trim((string) ($data['username'] ?? '')),
            'password' => $password !== '' ? $password : ($current['password'] ?? null),
            'encryption' => trim((string) ($data['encryption'] ?? 'tls')),
            'from_address' => trim((string) ($data['from_address'] ?? '')),
            'from_name' => trim((string) ($data['from_name'] ?? 'Thesis App FIKOM UMI')),
        ];

        foreach ($settings as $key => $value) {
            $encrypted = $key === 'password';
            $storedValue = $encrypted && $value !== null && $value !== ''
                ? Crypt::encrypt($value)
                : $value;

            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'group' => self::GROUP,
                    'value' => $storedValue,
                    'is_encrypted' => $encrypted,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        self::apply();
    }

    public static function maskedPassword()
    {
        $settings = self::all();

        return empty($settings['password']) ? '' : '********';
    }

    public static function isReady()
    {
        $settings = self::all();

        return !empty($settings['enabled'])
            && !empty($settings['host'])
            && !empty($settings['port'])
            && !empty($settings['from_address']);
    }

    protected static function tableReady()
    {
        try {
            return Schema::hasTable('system_settings');
        } catch (\Exception $exception) {
            return false;
        }
    }
}
