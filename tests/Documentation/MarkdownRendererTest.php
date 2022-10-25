<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\AppComponent;
use Bakabot\Component\Components;
use Bakabot\Component\DependencyDummy;
use Bakabot\Component\DependentDummy;
use PHPUnit\Framework\TestCase;

class MarkdownRendererTest extends TestCase
{
    /** @test */
    public function can_render_component_parameters(): void
    {
        $component = new DependencyDummy();

        $rawParameters = Parser::parameters($component);
        $renderedParameters = MarkdownRenderer::renderParameters(new Components($component));

        foreach ($rawParameters as $parameter) {
            self::assertStringContainsString($parameter->name, $renderedParameters);
            self::assertStringContainsString($parameter->type, $renderedParameters);
        }
    }

    /** @test */
    public function can_render_component_parameters_recursively(): void
    {
        $appComponent = new AppComponent();

        $rawParameterSets = [
            Parser::parameters(new DependencyDummy()),
            Parser::parameters(new DependentDummy()),
            Parser::parameters($appComponent)
        ];
        $renderedParameters = MarkdownRenderer::renderParameters(new Components($appComponent));

        foreach ($rawParameterSets as $parameterSet) {
            foreach ($parameterSet as $parameter) {
                self::assertStringContainsString($parameter->name, $renderedParameters);
                self::assertStringContainsString($parameter->type, $renderedParameters);
            }
        }
    }

    /** @test */
    public function can_render_component_services(): void
    {
        $component = new DependencyDummy();

        $rawServices = Parser::services($component);
        $renderedServices = MarkdownRenderer::renderServices(new Components($component));

        foreach ($rawServices as $service) {
            self::assertStringContainsString($service->type, $renderedServices);
        }
    }

    /** @test */
    public function can_render_component_services_recursively(): void
    {
        $appComponent = new AppComponent();

        $rawServiceSets = [
            Parser::services(new DependencyDummy()),
            Parser::services(new DependentDummy()),
            Parser::services($appComponent)
        ];
        $renderedServices = MarkdownRenderer::renderServices(new Components($appComponent));

        foreach ($rawServiceSets as $serviceSet) {
            foreach ($serviceSet as $service) {
                self::assertStringContainsString($service->type, $renderedServices);
            }
        }
    }
}
