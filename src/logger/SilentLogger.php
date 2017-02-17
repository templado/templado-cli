<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class SilentLogger implements Logger {

    /**
     * @var \string[]
     */
    private $backlog = [];

    public function log(string $message) {
        $this->backlog[] = $message;
    }

    /**
     * @return \string[]
     */
    public function getBacklog(): array {
        return $this->backlog;
    }

}
