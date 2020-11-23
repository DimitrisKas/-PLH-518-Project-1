<?php

function getRandomString(int $length, string $prefix):string
{
    $str = $prefix[0];
    for ($i = 0; $i < $length; $i++) {

        $str = $str . strval(mt_rand(0,9));
    }

    return $str;
}
