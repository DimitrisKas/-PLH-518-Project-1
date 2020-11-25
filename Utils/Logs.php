<?php

function logger($msg)
{
    $msg = date("[d/m] h:i:sa") . ": ". $msg . "\n";
    file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/Utils/Logs/log.txt', $msg, FILE_APPEND);
}
