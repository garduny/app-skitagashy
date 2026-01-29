<?php
class RateLimiter
{
    private static $ip;

    public static function init()
    {
        self::$ip = $_SERVER['REMOTE_ADDR'];
        // Garbage Collector: 1% chance to clean old rows
        if (rand(1, 100) === 1) {
            execute(" DELETE FROM system_rate_limits WHERE reset_time < " . time());
        }
    }

    /**
     * Check if request is allowed
     * @param string $key Context (e.g., 'login', 'api_global')
     * @param int $maxRequests Max requests allowed
     * @param int $seconds Time window in seconds
     */
    public static function check($key, $maxRequests, $seconds)
    {
        if (!self::$ip) self::init();

        $now = time();
        $ip = self::$ip;

        // 1. Get current status
        $row = findQuery(" SELECT requests, reset_time FROM system_rate_limits WHERE ip_address='$ip' AND endpoint='$key' ");

        if ($row) {
            // Record exists
            if ($now > $row['reset_time']) {
                // Window expired, reset count
                execute(" UPDATE system_rate_limits SET requests=1, reset_time=($now + $seconds) WHERE ip_address='$ip' AND endpoint='$key' ");
                return true;
            } else {
                // Window active, increment count
                if ($row['requests'] >= $maxRequests) {
                    return false; // BLOCKED
                }
                execute(" UPDATE system_rate_limits SET requests=requests+1 WHERE ip_address='$ip' AND endpoint='$key' ");
                return true;
            }
        } else {
            // First request
            execute(" INSERT INTO system_rate_limits (ip_address, endpoint, requests, reset_time) VALUES ('$ip', '$key', 1, ($now + $seconds)) ");
            return true;
        }
    }
}
