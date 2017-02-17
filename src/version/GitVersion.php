<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class GitVersion implements Version  {

    /**
     * @var string
     */
    private $defaultVersion;

    /**
     * @var string
     */
    private $version;

    /**
     * GitVersion constructor.
     *
     * @param string $defaultVersion
     */
    public function __construct(string $defaultVersion) {
        $this->defaultVersion = $defaultVersion;
    }

    public function asString(): string {
        return $this->getGitVersion();
    }

    /**
     * @return string
     */
    private function getGitVersion(): string
    {
        if ($this->version !== null) {
            return $this->version;
        }

        $workDir = __DIR__ . '/../../.git';

        if (!is_dir($workDir)) {
            $this->version = $this->defaultVersion;
            return $this->version;
        }

        $process = proc_open(
            'git describe --tags',
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $workDir
        );
        if (!is_resource($process)) {
            $this->version = $this->defaultVersion;
            return $this->version;
        }

        $this->version = trim(stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);
        if ($returnCode !== 0) {
            $this->version = $this->defaultVersion;
            return $this->version;
        }

        return $this->version;
    }

}
