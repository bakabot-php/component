<?php

declare(strict_types = 1);

namespace Bakabot\Component\Command;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateReadmeTest extends TestCase
{
    private vfsStreamDirectory $vfs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfs = vfsStream::setup(
            'root',
            null,
            [
                'README.md.dist' => <<<DIST
{{ parameters }}

{{ services }}
DIST,
                'src' => [
                    'AppComponent.php' => file_get_contents(dirname(__DIR__) . '/AppComponent.php'),
                ],
            ]
        );
    }

    /** @test */
    public function updates_readme_file(): void
    {
        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src'
            ]
        );

        self::assertSame(0, $exitCode);

        /** @var vfsStreamFile $file */
        $file = $this->vfs->getChild('README.md');

        self::assertNotEmpty($file->getContent());
        self::assertStringContainsString('heck', $file->getContent());
    }

    /** @test */
    public function fails_when_dist_file_does_not_exist(): void
    {
        $this->vfs->removeChild('README.md.dist');

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src'
            ]
        );

        self::assertSame(1, $exitCode);
        self::assertFalse($this->vfs->hasChild('README.md'));
        self::assertSame("Dist file [vfs://root/README.md.dist] does not exist.\n", $commandTester->getDisplay());
    }

    /** @test */
    public function fails_when_backup_fails(): void
    {
        vfsStream::newFile('README.md')->at($this->vfs);
        vfsStream::newFile('README.md.bak', 0000)->at($this->vfs);

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src'
            ]
        );

        self::assertSame(1, $exitCode);
        self::assertSame("Unable to back up original output file.\n", $commandTester->getDisplay());
    }

    /** @test */
    public function fails_when_writing_fails(): void
    {
        vfsStream::newFile('README.md', 0400)->at($this->vfs);
        vfsStream::newFile('README.md.bak')->at($this->vfs);

        $commandTester = new CommandTester(new UpdateReadme());
        $exitCode = $commandTester->execute(
            [
                '--' . UpdateReadme::OPT_BASE_DIR => $this->vfs->url(),
                '--' . UpdateReadme::OPT_SEARCH_FOLDERS => 'src'
            ]
        );

        self::assertSame(1, $exitCode);
        self::assertSame("Unable to write to output file.\n", $commandTester->getDisplay());
    }
}
