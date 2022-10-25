<?php

declare(strict_types = 1);

namespace Bakabot\Component;

use Generator;
use PhpParser\Error as ParseError;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_ as ClassDefinition;
use PhpParser\Node\Stmt\Namespace_ as NamespaceDeclaration;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class Finder
{
    private SymfonyFinder $finder;
    private Parser $parser;

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
            try {
                $statements = $this->parser->parse($fileInfo->getContents());
            } catch (ParseError) {
                // ignore unparsable files
                continue;
            }

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
     * @return Component|null
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
                    $class = sprintf("%s\\%s", $namespace, $class);
                }

                try {
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
                } catch (ReflectionException) {
                    continue;
                }
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
