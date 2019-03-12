<?php

namespace Tests\Helper;

use App\Base\Helper\Password;
use Tests\Base\BaseCase;

class PasswordTest extends BaseCase
{
    public function testPassword()
    {
        $data = [
            'plain' => '',
            'hashed' => '',
        ];
        $hash = Password::hash($data['plain']);
        // return null if no password given
        $this->assertEquals(null, $hash);

        $data = [
            'plain' => 'testpassword',
            'hashed' => '$2y$10$mDjRk9dNMHiGT1WH/ZtoGOGblNIDO37pLByaxjkm6E/gLaaxr1tPm',
        ];
        $hash = Password::hash($data['plain']);

        $this->assertEquals(true, Password::verify($data['plain'], $hash));

        // test only hash password if not hashed before
        $hash = Password::hash($hash);
        $this->assertEquals(true, Password::verify($data['plain'], $hash));
    }
}
