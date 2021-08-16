<?php

declare(strict_types = 1);

namespace Bakabot\Component\Documentation;

use Bakabot\Component\AppComponent;
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
        $renderedParameters = MarkdownRenderer::renderParameters([$component]);

        foreach ($rawParameters as $parameter) {
            self::assertStringContainsString($parameter->getName(), $renderedParameters);
            self::assertStringContainsString($parameter->getType(), $renderedParameters);
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
        $renderedParameters = MarkdownRenderer::renderParameters([$appComponent], true);

        foreach ($rawParameterSets as $parameterSet) {
            foreach ($parameterSet as $parameter) {
                self::assertStringContainsString($parameter->getName(), $renderedParameters);
                self::assertStringContainsString($parameter->getType(), $renderedParameters);
            }
        }
    }
    
    /** @test */
    public function can_render_component_services(): void
    {
        $component = new DependencyDummy();

        $rawServices = Parser::parseServices($component);
        $renderedServices = MarkdownRenderer::renderServices([$component]);

        foreach ($rawServices as $service) {
            self::assertStringContainsString($service->getType(), $renderedServices);
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
        $renderedServices = MarkdownRenderer::renderServices([$appComponent], true);

        foreach ($rawServiceSets as $serviceSet) {
            foreach ($serviceSet as $service) {
                self::assertStringContainsString($service->getType(), $renderedServices);
            }
        }
    }
}
