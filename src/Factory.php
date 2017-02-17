<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class Factory {
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Version
     */
    private $version;

    /**
     * Factory constructor.
     */
    public function __construct(Request $request, Version $version) {
        $this->request = $request;
        $this->version = $version;
    }

    public function getRunner(): Runner {
        return new Runner($this->request, $this->version, $this);
    }

    public function getGenerator() {
        return new Generator(
            new GeneratorConfig($this->request),
            $this->getLogger()
        );
    }

    private function getLogger(): Logger {
        if ($this->request->isSilentRequest()) {
            return new SilentLogger();
        }

        return new VerboseLogger();
    }
}
