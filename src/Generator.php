<?php declare(strict_types = 1);
namespace Templado\Cli;

use Templado\Engine\SnippetListCollection;
use Templado\Engine\SnippetLoader;
use Templado\Engine\SnippetLoaderException;
use Templado\Engine\FileName;
use Templado\Engine\Templado;

class Generator {

    /**
     * @var Directory
     */
    private $srcDirectory;

    /**
     * @var Directory
     */
    private $snippetsDirectory;

    /**
     * @var Directory
     */
    private $targetDirectory;

    /**
     * @var bool
     */
    private $clearFirst;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(GeneratorConfig $config, Logger $logger) {

        $this->srcDirectory    = $config->getSourceDirectory();
        $this->snippetsDirectory = $config->getSnippetDirectory();
        $this->targetDirectory = $config->getTargetDirectory();
        $this->clearFirst      = $config->clearFirst();
        $this->logger          = $logger;
    }

    public function run(): int {
        $snippets = $this->loadSnippets();

        if ($this->clearFirst) {
            $this->logger->log(
                sprintf('Cleaning existing files from target directory "%s"', $this->targetDirectory->asString())
            );
            $this->targetDirectory->clear();
        }

        $this->logger->log(
            sprintf('Processing files from directory "%s"', $this->srcDirectory->asString())
        );

        foreach($this->srcDirectory as $src) {
            /** @var \SplFileInfo $src */
            if (!$src->isFile()) {
                continue;
            }
            $this->processFile($src, $snippets);
        }

        return Runner::RC_OK;
    }

    private function loadSnippets(): SnippetListCollection {
        $snippets = new SnippetListCollection();
        $loader = new SnippetLoader();

        $this->logger->log(
            sprintf('Loading snippets from directory "%s"', $this->snippetsDirectory->asString())
        );
        foreach($this->snippetsDirectory as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isFile()) {
                continue;
            }

            try {
                $snippets->addSnippet($loader->load(new FileName($file->getRealPath())));
                $this->logger->log(
                    sprintf('🗸 %s', $file->getPathname())
                );
            } catch (SnippetLoaderException $e) {
                $this->logger->log(
                    sprintf('🗴 %s: %s',  $file->getPathname(), $e->getMessage())
                );
            }
        }

        return $snippets;
    }

    /**
     * @param \SplFileInfo        $src
     * @param SnippetListCollection $snippets
     */
    private function processFile(\SplFileInfo $src, SnippetListCollection $snippets) {
        $page = Templado::loadHtmlFile(new FileName($src->getPathname()));
        $page->applySnippets($snippets);
        $target = $this->targetPath($src);
        @mkdir(dirname($target), 0777, true);
        file_put_contents($target, $page->asString());
        $this->logger->log('🗸 ' . $src->getPathname());
    }

    private function targetPath(\SplFileInfo $file) {
        $target = substr($file->getPathname(), strlen($this->srcDirectory->asString()));

        return $this->targetDirectory->asString() . '/' . $target;
    }

}
