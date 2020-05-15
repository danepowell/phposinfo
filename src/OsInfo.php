<?php

declare(strict_types=1);

namespace drupol\phposinfo;

use drupol\phposinfo\Enum\Family;
use drupol\phposinfo\Enum\FamilyName;
use drupol\phposinfo\Enum\Os;
use drupol\phposinfo\Enum\OsName;
use Exception;

use function define;
use function defined;
use function is_string;

use const PHP_OS;
use const PHP_OS_FAMILY;

/**
 * Class OsInfo.
 */
final class OsInfo implements OsInfoInterface
{
    /**
     * {@inheritdoc}
     */
    public static function arch(): string
    {
        return php_uname('m');
    }

    /**
     * {@inheritdoc}
     */
    public static function family(): string
    {
        return sprintf('%s', FamilyName::value(Family::key(self::detectFamily())));
    }

    /**
     * {@inheritdoc}
     */
    public static function hostname(): string
    {
        return php_uname('n');
    }

    /**
     * {@inheritdoc}
     */
    public static function isApple(): bool
    {
        return self::isFamily(Family::DARWIN);
    }

    /**
     * {@inheritdoc}
     */
    public static function isBSD(): bool
    {
        return self::isFamily(Family::BSD);
    }

    /**
     * {@inheritdoc}
     */
    public static function isFamily($family): bool
    {
        $detectedFamily = self::detectFamily();

        if (is_string($family)) {
            $family = self::normalizeConst($family);

            if (!Family::has($family)) {
                return false;
            }

            $family = Family::value($family);
        }

        return $detectedFamily === $family;
    }

    /**
     * {@inheritdoc}
     */
    public static function isOs($os): bool
    {
        $detectedOs = self::detectOs();

        if (is_string($os)) {
            $os = self::normalizeConst($os);

            if (!Os::has($os)) {
                return false;
            }

            $os = Os::value($os);
        }

        return $detectedOs === $os;
    }

    /**
     * {@inheritdoc}
     */
    public static function isUnix(): bool
    {
        return self::isFamily(Family::LINUX);
    }

    /**
     * {@inheritdoc}
     */
    public static function isWindows(): bool
    {
        return self::isFamily(Family::WINDOWS);
    }

    /**
     * {@inheritdoc}
     */
    public static function os(): string
    {
        return sprintf('%s', OsName::value(Os::key(self::detectOs())));
    }

    /**
     * {@inheritdoc}
     */
    public static function register(): void
    {
        $family = self::family();
        $os = self::os();

        if (!defined('PHP_OS_FAMILY')) {
            define('PHP_OS_FAMILY', $family);
        }

        if (!defined('PHP_OS')) {
            define('PHP_OS', $os);
        }

        if (!defined('PHPOSINFO_OS_FAMILY')) {
            define('PHPOSINFO_OS_FAMILY', $family);
        }

        if (!defined('PHPOSINFO_OS')) {
            define('PHPOSINFO_OS', $os);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function release(): string
    {
        return php_uname('r');
    }

    /**
     * {@inheritdoc}
     */
    public static function uuid(): ?string
    {
        switch (self::family()) {
            case FamilyName::LINUX:
                $cmd = '( cat /var/lib/dbus/machine-id /etc/machine-id 2> /dev/null || hostname ) | head -n 1 || :';

                return shell_exec($cmd);
            case FamilyName::DARWIN:
                $output = shell_exec('ioreg -rd1 -c IOPlatformExpertDevice | grep IOPlatformUUID');

                if (!$output) {
                    return null;
                }
                $parts = explode('=', str_replace('"', '', $output));

                return mb_strtolower(trim($parts[1]));
            case FamilyName::WINDOWS:
                $cmd = '%windir%\\System32\\reg query "HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Cryptography" ';
                $cmd .= '/v MachineGuid';

                return shell_exec($cmd);
            case FamilyName::BSD:
                return shell_exec('kenv -q smbios.system.uuid');

            default:
                return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function version(): string
    {
        return php_uname('v');
    }

    /**
     * @param int|null $os
     *
     * @throws Exception
     *
     * @return int
     */
    private static function detectFamily(?int $os = null): int
    {
        $os = $os ?? self::detectOs();

        // Get the last 4 bits.
        $family = $os - (($os >> 16) << 16);

        if (true === Family::isValid($family)) {
            return $family;
        }

        if (defined(PHP_OS_FAMILY)) {
            $phpOsFamily = self::normalizeConst(PHP_OS_FAMILY);

            if (true === Family::has($phpOsFamily)) {
                return (int) Family::value($phpOsFamily);
            }
        }

        throw self::errorMessage();
    }

    /**
     * @throws Exception
     *
     * @return int
     */
    private static function detectOs(): int
    {
        $oss = [
            php_uname('s'),
            PHP_OS,
        ];

        foreach ($oss as $os) {
            $os = self::normalizeConst($os);

            if (Os::has($os)) {
                return (int) Os::value($os);
            }
        }

        throw self::errorMessage();
    }

    /**
     * @throws Exception
     */
    private static function errorMessage(): Exception
    {
        $uname = php_uname();
        $os = php_uname('s');

        $message = <<<EOF
Unable to find a proper information for this operating system.

Please open an issue on https://github.com/drupol/phposinfo and attach the
following information so I can update the library:

---8<---
php_uname(): {$uname}
php_uname('s'): {$os} 
--->8---

Thanks.

EOF;

        throw new Exception($message);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private static function normalizeConst(string $name): string
    {
        $name = (string) preg_replace('/[^a-zA-Z0-9]/', '', $name);

        return mb_strtoupper(
            str_replace('-.', '', $name)
        );
    }
}
