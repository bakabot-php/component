<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Bakabot\Component\Component;
use Bakabot\Component\DependentComponent;
use MaddHatter\MarkdownTable\Builder as Table;

final class MarkdownRenderer
{
    private static function backtick(string $text): string
    {
        return "`$text`";
    }

    private static function renderParameter(RegistersParameter $parameter): string
    {
        if ($parameter->getDefaultValue() === RegistersParameter::DEFAULT_VALUE) {
            return '*none*';
        }

        $asString = stripslashes(var_export($parameter->getDefaultValue(), true));

        return str_replace("'", '"', $asString);
    }

    private static function renderService(RegistersService $service): string
    {
        $name = $service->getName();
        $type = $service->getType();

        if ($name === $type) {
            return self::backtick($name);
        }

        return sprintf(
            '"%s" (%s)',
            $name,
            self::backtick($type)
        );
    }

    /**
     * @param Component[] $components
     * @param bool $recursive
     * @return Component[]
     */
    private static function resolveDependencies(array $components, bool $recursive): array
    {
        if (!$recursive) {
            return $components;
        }

        foreach ($components as $component) {
            if ($component instanceof DependentComponent) {
                recurse:
                foreach ($component->getDependencies() as $dependency) {
                    $dependency = new $dependency();
                    array_unshift($components, $dependency);

                    if ($dependency instanceof DependentComponent) {
                        $component = $dependency;
                        goto recurse;
                    }
                }
            }
        }

        return array_unique($components);
    }

    /**
     * @param Component[] $components
     * @param bool $recursive
     * @return string
     * @throws \ReflectionException
     */
    public static function renderParameters(array $components, bool $recursive = false): string
    {
        $table = new Table();
        $table->headers(['Name', 'Type', 'Default Value', 'Description']);

        $rows = [];
        foreach (self::resolveDependencies($components, $recursive) as $component) {
            foreach (Parser::parseParameters($component) as $parameter) {
                $name = $parameter->getName();

                $rows[$name] = [
                    self::backtick($name),
                    self::backtick($parameter->getType()),
                    self::backtick(self::renderParameter($parameter)),
                    $parameter->getDescription()
                ];
            }
        }
        ksort($rows, SORT_NATURAL);

        $table->rows(array_values($rows));

        return $table->render();
    }

    /**
     * @param Component[] $components
     * @param bool $recursive
     * @return string
     * @throws \ReflectionException
     */
    public static function renderServices(array $components, bool $recursive = false): string
    {
        $table = new Table();
        $table->headers(['Type', 'Description', 'Aliases']);

        $rows = [];
        foreach (self::resolveDependencies($components, $recursive) as $component) {
            foreach (Parser::parseServices($component) as $service) {
                $rows[$service->getName()] =  [
                    self::renderService($service),
                    $service->getDescription()
                ];
            }
        }
        ksort($rows, SORT_NATURAL);

        $table->rows(array_values($rows));

        return $table->render();
    }
}
