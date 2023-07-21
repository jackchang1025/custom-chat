<?php

namespace App\Http\Services\ContentDecorator;

interface ContentDecoratorInterface
{
    function getNormalizedContent(string $html): string;

    function getCrawledPageTitle(string $html): ?string;

    function extractLinks(string $html, string $rootUrl): array;

}
