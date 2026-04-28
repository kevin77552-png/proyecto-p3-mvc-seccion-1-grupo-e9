<x-mail::message>
<div style="text-align:center; margin-bottom: 24px;">
    <img src="{{ asset('images/sittsa logo small.png') }}" alt="Sittsa Logo" style="height: 60px; margin: 0 auto; display: block;">
</div>
{{-- Saludo --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# ¡Ups!
@else
# ¡Hola!
@endif
@endif

{{-- Líneas de introducción --}}
@foreach ($introLines as $line)
{{ __($line) }}

@endforeach

{{-- Botón de acción --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ __($actionText) }}
</x-mail::button>
@endisset

{{-- Líneas finales --}}
@foreach ($outroLines as $line)
{{ __($line) }}

@endforeach

{{-- Despedida --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Saludos,<br>
Sittsa
@endif

{{-- Nota alternativa --}}
@isset($actionText)
<x-slot:subcopy>
Si tienes problemas para hacer clic en el botón "{{ __($actionText) }}", copia y pega la siguiente URL en tu navegador web:
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
