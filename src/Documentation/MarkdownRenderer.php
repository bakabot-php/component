<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\Attribute\RegistersParameter;
use Bakabot\Component\Attribute\RegistersService;
use Bakabot\Component\Components;
use MaddHatter\MarkdownTable\Builder as Table;
use ReflectionClass;
use ReflectionException;

final class MarkdownRenderer
{
    /**
     * @var string[]
     */
    public const PARAMETER_HEADERS = ['Name', 'Type', 'Default Value', 'Description'];

    /**
     * @var string[]
     */
    public const SERVICE_HEADERS = ['Name', 'Description'];

    public static function renderParameters(Components $components): string
    {
        $table = new Table();
        $table->headers(self::PARAMETER_HEADERS);

        $rows = [];
        foreach ($components as $component) {
            foreach (Parser::parameters($component) as $parameter) {
                $name = $parameter->name;

                $rows[$name] = [
                    self::backtick($name),
                    self::backtick($parameter->type),
                    self::backtick(self::renderDefault($parameter)),
                    $parameter->description,
                ];
            }
        }
        ksort($rows, SORT_NATURAL);

        $table->rows(array_values($rows));

        return $table->render();
    }

    public static function renderServices(Components $components): string
    {
        $table = new Table();
        $table->headers(self::SERVICE_HEADERS);

        $rows = [];
        foreach ($components as $component) {
            foreach (Parser::services($component) as $service) {
                $rows[$service->name] = [
                    self::renderService($service),
                    $service->description,
                ];
            }
        }
        ksort($rows, SORT_NATURAL);

        $table->rows(array_values($rows));

        return $table->render();
    }

    private static function backtick(string $text): string
    {
        return "`{$text}`";
    }

    /**
     * @param class-string|string $name
     * @param class-string $type
     *
     * @throws ReflectionException
     */
    private static function label(string $name, string $type): string
    {
        $reflection = new ReflectionClass($type);

        return match (true) {
            class_exists($name) && $reflection->isInterface() => 'provides: ',
            $reflection->isInterface() => 'is: ',
            default => '',
        };
    }

    private static function renderDefault(RegistersParameter $parameter): string
    {
        if (!$parameter->hasDefault()) {
            return '*none*';
        }

        // insert raw expressions directly
        if (is_string($parameter->default) && str_starts_with($parameter->default, 'e:')) {
            return substr($parameter->default, 2);
        }

        $asString = var_export($parameter->default, true);
        $asString = stripslashes($asString);

        return str_replace("'", '"', $asString);
    }

    private static function renderService(RegistersService $service): string
    {
        $name = $service->name;
        $type = $service->type;

        if ($name === $type) {
            return self::backtick($name);
        }

        return sprintf(
            '%s (%s%s)',
            class_exists($name) ? self::backtick($name) : '"' . $name . '"',
            self::label($name, $type),
            self::backtick($type),
        );
    }
}
