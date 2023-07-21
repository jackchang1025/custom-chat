<?php

namespace App\Http\Services\ContentDecorator;

 use DOMDocument;

 class DefaultContentDecorator implements ContentDecoratorInterface
{

     public function __construct(protected readonly DOMDocument $document)
     {
     }

     public function getNormalizedContent(string $html): string
     {
         // Remove inline script and style tags and their contents
         $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
         $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $html);

         // Remove all HTML tags except for line break and paragraph tags
         $html = strip_tags($html, '<br><p>');

         // Replace line breaks and paragraphs with new lines
         $html = preg_replace('/<(br|p)[^>]*>/i', "\n", $html);

         // Remove extra whitespace and normalize new lines
         $html = preg_replace('/\s+/', ' ', $html);
         $html = preg_replace('/\n\s*\n/', "\n", $html);

         // Trim leading and trailing whitespace
         return trim($html);
     }

     public function getCrawledPageTitle(string $html): ?string
     {
         libxml_use_internal_errors(true); // Disable error reporting for invalid HTML
         @$this->document->loadHTML($html);
         libxml_clear_errors();

         $titleElements = $this->document->getElementsByTagName('title');
         if ($titleElements->length > 0) {
             $title = $titleElements->item(0)->textContent;
             $title = trim($title);

             // Decode any HTML entities in the title
             return html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
         }

         return null; // Return null if no title element was found
     }

     public function extractLinks(string $html, string $rootUrl): array
     {
         // Use regular expressions or an HTML parsing library
         // to extract the URLs from the HTML content.
         // Here's a simple example using regular expressions:

         $pattern = '/<a\s(?:[^>]*)href="([^"]*)"/i';
         preg_match_all($pattern, $html, $matches);

         // Extract the URLs from the matches
         $urls = $matches[1];

         $links = [];

         foreach ($urls as $url) {
             if (str_starts_with($url, 'http')) {
                 // Full URL starting with "http"
                 $links[] = $url;
             } elseif (str_starts_with($url, '/')) {
                 // URL starting with "/"
                 $parsedRoot = parse_url($rootUrl);
                 $rootHost = $parsedRoot['scheme'] . '://' . $parsedRoot['host'];
                 $links[] = $rootHost . $url;
             }
         }

         // Remove any duplicate URLs
         $links = array_unique($links);

         // Remove any URL that does not belong to the same root URL host
         return array_filter($links, function ($url) use ($rootUrl) {
             $urlHost = $this->removeWwwPrefix(parse_url($url, PHP_URL_HOST));
             $rootHost = $this->removeWwwPrefix(parse_url($rootUrl, PHP_URL_HOST));
             return $urlHost === $rootHost;
         });
     }

     /**
      * 这个方法用于从主机名中移除 "www." 前缀
      * @param $host
      * @return array|string|null
      */
     protected function removeWwwPrefix($host): array|string|null
     {
         return preg_replace('/^www\./', '', $host);
     }
 }
