<?php

namespace nightmare;

use Ramsey\Uuid\Uuid as uuid2;

class uuid
{
    public static function v4() {
        return uuid2::uuid4();
    }
    public static function v7() {
        return uuid2::uuid7();
    }
}
