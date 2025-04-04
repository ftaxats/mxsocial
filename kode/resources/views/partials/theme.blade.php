@php
    $primaryRgba =  hexa_to_rgba(site_settings('primary_color'));
    $secondaryRgba =  hexa_to_rgba(site_settings('secondary_color'));
    $primary_light = "rgba(".$primaryRgba.",0.09)";
    $primary_light2 = "rgba(".$primaryRgba.",0.2)";
    $primary_light3 = "rgba(".$primaryRgba.",0.03)";
    $secondary_light = "rgba(".$secondaryRgba.",0.09)";
@endphp
<style nonce="{{ csp_nonce() }}">

:root{
    --color-primary:  {{ site_settings('primary_color') }} !important;
    --color-primary-light: {{$primary_light}} !important;
    --color-primary-light-2: {{$primary_light2}} !important;
    --color-primary-light-3: {{$primary_light3}} !important;
    --color-secondary: {{site_settings('secondary_color') }} !important;
    --color-secondary-light:{{$secondary_light}} !important;
    --text-primary:{{site_settings('text_primary') }} !important;
    --text-secondary:{{site_settings('text_secondary') }} !important;
    --color-primary-text: {{ site_settings('btn_text_primary') }} !important;
    --color-secondary-text: {{ site_settings('btn_text_secondary') }} !important;
}
</style>
