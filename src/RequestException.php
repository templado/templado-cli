<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class RequestException extends Exception {

    const UnsupportedLongSwitch = 1;
    const UnsupportedShortSwitch = 2;
    const WrongArgumentCount = 3;

}
