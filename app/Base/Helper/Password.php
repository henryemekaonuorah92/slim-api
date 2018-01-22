<?php

namespace App\Base\Helper;

class Password
{
    /**
     * @param $password
     * @return bool|string
     */
    public static function hash($password)
    {
        $hashInfo = password_get_info($password);

        if ($hashInfo['algo'] == 0) {
            return password_hash($password, PASSWORD_DEFAULT);
        } else {
            return $password;
        }
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

}