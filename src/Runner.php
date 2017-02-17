<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

class Runner {

    const RC_OK = 0;
    // OUTDATED PHP VERSION = 1
    const RC_EXT_MISSING = 2;
    const RC_PARAM_ERROR = 3;
    const RC_ERROR = 4;
    const RC_BUG_FOUND = 99;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * Runner constructor.
     *
     * @param Request $request
     * @param Version $version
     */
    public function __construct(Request $request, Version $version, Factory $factory) {
        $this->request = $request;
        $this->version = $version;
        $this->factory = $factory;
    }

    public function run(): int {
        try {
            if ($this->request->isVersionRequest()) {
                $this->showCopyright();

                return self::RC_OK;
            }

            if ($this->request->isHelpRequest()) {
                $this->showCopyright();
                $this->showHelp();

                return self::RC_OK;
            }

            if (!$this->request->isSilentRequest()) {
                $this->showCopyright();
            }

            return $this->factory->getGenerator()->run();
        } catch (RequestException $e) {
            $this->showCopyright();
            $this->showError($e->getMessage());
            $this->showHelp();

            return self::RC_PARAM_ERROR;
        } catch (DirectoryException $e) {
            $this->showCopyright();
            $this->showError($e->getMessage());

            return self::RC_ERROR;
        }
    }

    private function showCopyright() {
        fwrite(STDOUT,
            sprintf(
                "Templado CLI %s by Arne Blankerts and contributors\n\n",
                $this->version->asString()
            )
        );
    }

    private function showError(string $message) {
        fwrite(
            STDERR,
            $message . "\n\n"
        );
    }

    private function showHelp() {
        fwrite(
            STDOUT,
            file_get_contents(__DIR__ . '/help.txt')
        );
    }

}
