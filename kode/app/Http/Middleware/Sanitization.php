<?php

namespace App\Http\Middleware;

use App\Policies\CustomCspPolicy;
use Closure;
use Illuminate\Http\Request;
use HTMLPurifier;
use HTMLPurifier_Config;
class Sanitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        (new CustomCspPolicy())->configure();

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        $input = $request->all();
        $sanitizedInput = $this->removeScripts($input);

        if ($input != $sanitizedInput) {
            $request->session()->flash('error', translate(" Your Input Contained Potentially Harmful Content And Has Been Sanitized!!"));
        }
        
        $request->replace($sanitizedInput);
        
        $response = $next($request);

        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;

    }

    protected function removeScripts(array $input)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->removeScripts($value);
            } elseif (is_string($value)) {
                $input[$key] = $this->sanitizeString($value);
            }
        }
        return $input;
    }
    
    protected function sanitizeHtml(string $value)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($value, LIBXML_HTML_NODEFDTD);
        $scriptTags = $dom->getElementsByTagName('script');
        foreach ($scriptTags as $scriptTag) {
            $scriptTag->parentNode->removeChild($scriptTag);
        }

        $cleanContent = $dom->saveHTML();
        return $cleanContent;
    }

    protected function sanitizeString(string $value)
    {
        $cleanValue = htmlspecialchars_decode($value);
        $cleanValue = preg_replace("/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i", '', $cleanValue);
        $cleanValue = preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", '', $cleanValue);
        return $cleanValue;

    }
}
