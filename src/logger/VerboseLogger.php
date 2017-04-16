<?php declare(strict_types = 1);
namespace Templado\Cli;

class VerboseLogger implements Logger {

    public function log(string $message) {
        fwrite(
            STDOUT,
            sprintf(
                "[%s] %s\n",
                date('d.m.Y H:i:s'),
                $message
            )
        );
    }

}
