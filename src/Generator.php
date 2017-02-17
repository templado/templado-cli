<?php declare(strict_types = 1);
namespace TheSeer\Templado\Cli;

use TheSeer\Templado\AssetListCollection;
use TheSeer\Templado\AssetLoader;
use TheSeer\Templado\AssetLoaderException;
use TheSeer\Templado\FileName;
use TheSeer\Templado\Templado;

class Generator {

    /**
     * @var Directory
     */
    private $srcDirectory;

    /**
     * @var Directory
     */
    private $assetsDirectory;

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
        $this->assetsDirectory = $config->getAssetDirectory();
        $this->targetDirectory = $config->getTargetDirectory();
        $this->clearFirst      = $config->clearFirst();
        $this->logger          = $logger;
    }

    public function run(): int {
        $assets = $this->loadAssets();

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
            $this->processFile($src, $assets);
        }

        return Runner::RC_OK;
    }

    private function loadAssets(): AssetListCollection {
        $assets = new AssetListCollection();
        $loader = new AssetLoader();

        $this->logger->log(
            sprintf('Loading assets from directory "%s"', $this->assetsDirectory->asString())
        );
        foreach($this->assetsDirectory as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isFile()) {
                continue;
            }

            try {
                $assets->addAsset($loader->load(new FileName($file->getRealPath())));
                $this->logger->log('🗸 ' . $file->getPathname());
            } catch (AssetLoaderException $e) {
                $this->logger->log('🗴 ' . $e->getMessage());
            }
        }

        return $assets;
    }

    /**
     * @param \SplFileInfo        $src
     * @param AssetListCollection $assets
     */
    private function processFile(\SplFileInfo $src, AssetListCollection $assets) {
        $page = Templado::loadFile(new FileName($src->getPathname()));
        $page->applyAssets($assets);
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
