<?php declare(strict_types = 1);
namespace Templado\Cli;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Traversable;

class Directory implements \IteratorAggregate {

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct(string $path) {
        $this->ensureExists($path);
        $this->ensureIsDirectory($path);
        $this->path = $path;
    }

    public function asString(): string {
        return $this->path;
    }

    public function clear() {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->path,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach($files as $file) {
            $realpath = $file->getRealPath();
            $file->isDir() ? rmdir($realpath) : unlink($realpath);
        }
    }

    /**
     * @return Traversable
     */
    public function getIterator(): \Traversable {
        return new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->path,
                RecursiveDirectoryIterator::FOLLOW_SYMLINKS | RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
            )
        );
    }

    private function ensureExists(string $path) {
        if (!file_exists($path)) {
            throw new DirectoryException(
                sprintf('Path "%s" does not exist', $path),
                DirectoryException::PathDoesNotExist
            );
        }
    }

    private function ensureIsDirectory(string $path) {
        if (!is_dir(realpath($path))) {
            throw new DirectoryException(
                sprintf('Path "%s" is not a directory', $path),
                DirectoryException::NotADirectory
            );
        }
    }

}
