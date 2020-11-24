<?php

function logger($msg)
{
    $msg = date("[d/m] h:i:sa") . ": ". $msg . "\n";
    file_put_contents('./log.txt', $msg, FILE_APPEND);
}
