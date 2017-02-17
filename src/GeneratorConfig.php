<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class GeneratorConfig {

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function getSourceDirectory(): Directory {
        return $this->request->getSourceDirectory();
    }

    public function getAssetDirectory(): Directory {
        return $this->request->getAssetDirectory();
    }

    public function getTargetDirectory(): Directory {
        return $this->request->getTargetDirectory();
    }

    public function clearFirst(): bool {
        return $this->request->clearFirst();
    }

}
