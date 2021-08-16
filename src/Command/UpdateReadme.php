<?php

declare(strict_types = 1);

namespace Bakabot\Component\Command;

use Bakabot\Component\Component;
use Bakabot\Component\Documentation\MarkdownRenderer;
use Bakabot\Component\Finder as ComponentFinder;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Throwable;

final class UpdateReadme extends Command
{
    private ?string $baseDir;

    public const NAME = 'update-readme';
    public const OPT_BASE_DIR = 'base-dir';
    public const OPT_RECURSIVE = 'recursive';
    public const OPT_SEARCH_FOLDERS = 'search-folders';

    public function __construct(?string $baseDir = null)
    {
        $this->baseDir = $baseDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->addOption(
                self::OPT_BASE_DIR,
                null,
                InputOption::VALUE_REQUIRED,
                'The base directory in which to look for a readme file',
                $this->baseDir
            )
            ->addOption(
                self::OPT_RECURSIVE,
                null,
                InputOption::VALUE_NEGATABLE,
                'Update readme with parameters and services in *all* components',
                true
            )
            ->addOption(
                self::OPT_SEARCH_FOLDERS,
                null,
                InputOption::VALUE_REQUIRED,
                'Glob expression for folders in which to look for components',
                ComponentFinder::SEARCH_FOLDERS
            )
        ;
    }

    /**
     * @param string $distFile
     * @param string $outFile
     * @param Component[] $components
     * @param bool $recursive
     */
    private function updateReadme(string $distFile, string $outFile, array $components, bool $recursive): void
    {
        clearstatcache();

        if (!file_exists($distFile)) {
            throw new InvalidArgumentException(sprintf('Dist file [%s] does not exist.', $distFile));
        }

        $distContents = file_get_contents($distFile);

        $placeholderMap = [
            'parameters' => [MarkdownRenderer::class, 'renderParameters'],
            'services' => [MarkdownRenderer::class, 'renderServices'],
        ];

        foreach ($placeholderMap as $placeholder => $renderer) {
            $placeholder = "{{ $placeholder }}";

            /** @var string $section */
            $section = $renderer($components, $recursive);
            $distContents = str_replace($placeholder, $section, $distContents);
        }

        if (file_exists($outFile)) {
            try {
                copy($outFile, "$outFile.bak");
            } catch (Throwable $ex) {
                throw new RuntimeException('Unable to back up original output file.', 0, $ex);
            }
        }

        try {
            file_put_contents($outFile, $distContents);
        } catch (Throwable $ex) {
            throw new RuntimeException('Unable to write to output file.', 0, $ex);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $baseDir */
        $baseDir = $input->getOption(self::OPT_BASE_DIR);
        $baseDir = rtrim($baseDir, "\\/");
        $distFile = $baseDir . '/README.md.dist';
        $recursive = (bool) $input->getOption(self::OPT_RECURSIVE);
        $outFile = $baseDir . '/README.md';

        $finder = new Finder();

        /** @var string $searchFolders */
        $searchFolders = $input->getOption(self::OPT_SEARCH_FOLDERS);
        $finder->in($baseDir . '/' . $searchFolders);

        try {
            $components = ComponentFinder::getInstances($finder);
            $this->updateReadme($distFile, $outFile, $components, $recursive);
        } catch (Throwable $ex) {
            $output->writeln('<error>' . $ex->getMessage() . ' Exiting...</error>');
            return 1;
        }

        return 0;
    }
}
