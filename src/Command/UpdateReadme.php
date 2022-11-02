<?php

declare(strict_types = 1);

namespace Bakabot\Component\Command;

use Bakabot\Component\Components;
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
    /**
     * @var string
     */
    public const NAME = 'update-readme';

    /**
     * @var string
     */
    public const OPT_BASE_DIR = 'base-dir';

    /**
     * @var string
     */
    public const OPT_DRY_RUN = 'dry-run';

    /**
     * @var string
     */
    public const OPT_SEARCH_FOLDERS = 'search-folders';

    public function __construct(private readonly ?string $baseDir = null)
    {
        parent::__construct();
    }

    private function updateReadme(
        Components $components,
        string $distFile,
        string $outFile,
        bool $dryRun,
    ): void {
        clearstatcache();

        if (!file_exists($distFile)) {
            throw new InvalidArgumentException(sprintf('Dist file [%s] does not exist.', $distFile));
        }

        $rendererMap = [
            'parameters' => MarkdownRenderer::renderParameters(...),
            'services' => MarkdownRenderer::renderServices(...),
        ];

        $distContents = file_get_contents($distFile);
        foreach ($rendererMap as $placeholder => $renderer) {
            $distContents = str_replace("{{ {$placeholder} }}", $renderer($components), $distContents);
        }

        $outFileExists = file_exists($outFile);

        if ($dryRun) {
            if ($outFileExists) {
                $outFileContents = file_get_contents($outFile);

                if ($distContents === $outFileContents) {
                    return;
                }
            }

            throw new RuntimeException(sprintf('%s has not been updated before committing.', basename($outFile)));
        }

        if ($outFileExists) {
            try {
                copy($outFile, "{$outFile}.bak");
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

    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->addOption(
                self::OPT_BASE_DIR,
                null,
                InputOption::VALUE_REQUIRED,
                'The base directory in which to look for a readme file',
                $this->baseDir,
            )
            ->addOption(
                self::OPT_DRY_RUN,
                null,
                InputOption::VALUE_NONE,
                'Validate that the README file is up-to-date',
            )
            ->addOption(
                self::OPT_SEARCH_FOLDERS,
                null,
                InputOption::VALUE_REQUIRED,
                'Glob expression for folders in which to look for components',
                ComponentFinder::SEARCH_FOLDERS,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $baseDir */
        $baseDir = $input->getOption(self::OPT_BASE_DIR);
        $baseDir = rtrim($baseDir, '\\/');

        $distFile = $baseDir . '/README.md.dist';
        $outFile = $baseDir . '/README.md';

        /** @var string $searchFolders */
        $searchFolders = $input->getOption(self::OPT_SEARCH_FOLDERS);

        $finder = new Finder();
        $finder->in($baseDir . '/' . $searchFolders);

        try {
            $components = (new ComponentFinder($finder))->collect();
            $this->updateReadme($components, $distFile, $outFile, (bool) $input->getOption(self::OPT_DRY_RUN));
        } catch (Throwable $ex) {
            $output->writeln('<error>' . $ex->getMessage() . ' Exiting...</error>');

            return 1;
        }

        return 0;
    }
}
