<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class StaticVersion implements Version {

    /**
     * @var string
     */
    private $versionString;

    /**
     * StaticVersion constructor.
     *
     * @param string $versionString
     */
    public function __construct(string $versionString) {
        $this->versionString = $versionString;
    }

    public function asString(): string {
        return $this->versionString;
    }
}
