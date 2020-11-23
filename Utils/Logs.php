<?php

function logger($msg)
{
    file_put_contents('./log.txt', $msg, FILE_APPEND);
}
