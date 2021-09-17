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

        $rawParameters = Parser::parseParameters($component);
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
            Parser::parseParameters(new DependencyDummy()),
            Parser::parseParameters(new DependentDummy()),
            Parser::parseParameters($appComponent)
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

        $rawServices = Parser::parseServices($component);
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
            Parser::parseServices(new DependencyDummy()),
            Parser::parseServices(new DependentDummy()),
            Parser::parseServices($appComponent)
        ];
        $renderedServices = MarkdownRenderer::renderServices(new Components($appComponent));

        foreach ($rawServiceSets as $serviceSet) {
            foreach ($serviceSet as $service) {
                self::assertStringContainsString($service->type, $renderedServices);
            }
        }
    }
}
