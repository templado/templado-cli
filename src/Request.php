<?php declare(strict_types = 1);
namespace Templado\Cli;

class Request {

    /**
     * @var array
     */
    private $rawArgv;

    /**
     * @var bool
     */
    private $parsed = false;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var array
     */
    private $switches = [
        'help'    => false,
        'version' => false,
        'clean'   => false,
        'silent'  => false
    ];

    /**
     * @var array
     */
    private $shortMap = [
        'h' => 'help',
        'v' => 'version',
        'c' => 'clean',
        's' => 'silent'
    ];

    /**
     * Request constructor.
     *
     * @param array $argv
     */
    public function __construct(array $argv) {
        $this->rawArgv = $argv;
    }

    public function getSourceDirectory(): Directory {
        $this->parse();

        return new Directory($this->arguments[0]);
    }

    public function getSnippetDirectory(): Directory {
        $this->parse();

        return new Directory($this->arguments[1]);
    }

    public function getTargetDirectory(): Directory {
        $this->parse();

        return new Directory($this->arguments[2]);
    }

    public function isSilentRequest(): bool {
        $this->parse();

        return $this->switches['silent'];
    }

    public function isHelpRequest(): bool {
        $this->parse();

        return $this->switches['help'];
    }

    public function isVersionRequest(): bool {
        $this->parse();

        return $this->switches['version'];
    }

    public function clearFirst(): bool {
        $this->parse();

        return $this->switches['clean'];
    }

    private function parse() {
        if ($this->parsed) {
            return;
        }
        $this->parsed = true;

        array_shift($this->rawArgv);
        if (count($this->rawArgv) === 0) {
            $this->switches['help'] = true;

            return;
        }

        foreach($this->rawArgv as $argv) {
            if ($argv[0] !== '-') {
                $this->arguments[] = $argv;
                continue;
            }

            $switch = trim($argv, '-');
            if (strlen($switch) === 1) {
                if (!isset($this->shortMap[$switch])) {
                    throw new RequestException(
                        sprintf('Invalid or unsupported switch "%s"', $switch),
                        RequestException::UnsupportedShortSwitch
                    );
                }
                $switch = $this->shortMap[$switch];
            }

            if (!isset($this->switches[$switch])) {
                throw new RequestException(
                    sprintf('Invalid or unsupported switch "%s"', $switch),
                    RequestException::UnsupportedLongSwitch
                );
            }
            $this->switches[$switch] = true;
        }

        $count = count($this->arguments);
        if ($count !== 3 && !$this->isHelpRequest() && !$this->isVersionRequest()) {
            throw new RequestException(
                sprintf('Unexpected amount of arguments: Got %d, expected 3', $count),
                RequestException::WrongArgumentCount
            );
        }
    }

}
