<?php

use Illuminate\Support\Facades\Log;

function reportError(Throwable $exception, int $exceptionLevel = 1) : void
{
    Log::error(
        $exception->getMessage()
        . PHP_EOL . 'IN LINE: ' . $exception->getLine()
        . PHP_EOL . 'IN FILE: ' . $exception->getFile()
    );
}
