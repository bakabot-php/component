<?php

declare(strict_types = 1);

namespace Bakabot\Component\Command;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 */
final class UpdateReadmeTest extends TestCase
{
    private vfsStreamDirectory $vfs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfs = vfsStream::setup(
            'root',
            null,
            [
                'README.md.dist' => <<<'DIST'
{{ parameters }}

{{ services }}
DIST,
                'src' => [
                    'AppComponent.php' => file_get_contents(dirname(__DIR__) . '/AppComponent.php'),
                    'DependencyDummy.php' => file_get_contents(dirname(__DIR__) . '/DependencyDummy.php'),
                    'DependentDummy.php' => file_get_contents(dirname(__DIR__) . '/DependentDummy.php'),
                ],
            ],
        );
    }

    /**
     * @test
     */
    public function dry_run_fails_when_out_file_does_not_exist(): void
    {
        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_DRY_RUN => true,
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(1, $exitCode);
        self::assertFalse($this->vfs->hasChild('README.md'));
        self::assertSame("README.md has not been updated before committing. Exiting...\n", $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function dry_run_returns_when_file_is_up_to_date(): void
    {
        $commandTester = new CommandTester(new UpdateReadme());
        $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_DRY_RUN => true,
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(0, $exitCode);
    }

    /**
     * @test
     */
    public function fails_on_backup_error(): void
    {
        vfsStream::newFile('README.md')->at($this->vfs);
        vfsStream::newFile('README.md.bak', 0000)->at($this->vfs);

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(1, $exitCode);
        self::assertSame("Unable to back up original output file. Exiting...\n", $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function fails_on_write_error(): void
    {
        vfsStream::newFile('README.md', 0400)->at($this->vfs);
        vfsStream::newFile('README.md.bak')->at($this->vfs);

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(1, $exitCode);
        self::assertSame("Unable to write to output file. Exiting...\n", $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function fails_when_dist_file_does_not_exist(): void
    {
        $this->vfs->removeChild('README.md.dist');

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(1, $exitCode);
        self::assertFalse($this->vfs->hasChild('README.md'));
        self::assertSame("Dist file [vfs://root/README.md.dist] does not exist. Exiting...\n", $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function updates_readme_file(): void
    {
        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src',
            ],
        );

        self::assertSame(0, $exitCode);

        /** @var vfsStreamFile $file */
        $file = $this->vfs->getChild('README.md');

        self::assertNotEmpty($file->getContent());
        self::assertStringContainsString('heck', $file->getContent());
    }
}
