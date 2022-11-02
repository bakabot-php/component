<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Generator;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_ as ClassDefinition;
use PhpParser\Node\Stmt\Namespace_ as NamespaceDeclaration;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class Finder
{
    private SymfonyFinder $finder;

    private Parser $parser;

    /**
     * @var string
     */
    public const SEARCH_FOLDERS = '{src,vendor/bakabot}';

    public function __construct(?SymfonyFinder $finder = null, ?Parser $parser = null)
    {
        if (!$finder) {
            $finder = new SymfonyFinder();
            $finder->in(self::SEARCH_FOLDERS);
            $finder->name('*.php');
        }

        $this->finder = $finder;

        if (!$parser) {
            $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
        }

        $this->parser = $parser;
    }

    /**
     * @return Generator<Component>
     */
    private function find(): Generator
    {
        foreach ($this->finder as $fileInfo) {
            $statements = $this->parser->parse($fileInfo->getContents());

            if (!$statements) {
                // ignore empty files
                continue;
            }

            if ($class = $this->reflect($statements)) {
                yield $class;
            }
        }
    }

    /**
     * @param Stmt[] $statements
     */
    private function reflect(array $statements, string $namespace = ''): ?Component
    {
        foreach ($statements as $stmt) {
            if ($stmt instanceof NamespaceDeclaration) {
                return $this->reflect($stmt->stmts, (string) $stmt->name);
            }

            if ($stmt instanceof ClassDefinition) {
                $class = (string) $stmt->name;

                if ($namespace !== '') {
                    $class = sprintf('%s\\%s', $namespace, $class);
                }

                /** @var class-string $class */
                $reflection = new ReflectionClass($class);

                if (
                    !$reflection->implementsInterface(Component::class)
                    || !$reflection->isInstantiable()
                ) {
                    continue;
                }

                $component = $reflection->newInstance();
                assert($component instanceof Component);

                return $component;
            }
        }

        return null;
    }

    public function collect(): Components
    {
        $components = new Components();

        foreach ($this->find() as $component) {
            $components->add($component);
        }

        return $components;
    }
}
