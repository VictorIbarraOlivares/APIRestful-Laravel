@component('mail::message')
# Hola {{ $user->name }},

Gracias por crear una cuenta. Por favor verificala usando el siguiente botón:

@component('mail::button', ['url' => route('verify', $user->verification_token) ])
Confirmar mi cuenta
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent