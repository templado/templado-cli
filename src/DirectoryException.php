<?php declare(strict_types = 1);
namespace Templado\Cli;

class DirectoryException extends Exception {

    const PathDoesNotExist = 1;
    const NotADirectory = 2;

}
