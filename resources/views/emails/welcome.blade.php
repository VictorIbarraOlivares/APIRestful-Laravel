Hola {{ $user->name }},
Gracias por crear una cuenta. Por favor verificala usando el siguiente enlace:

{{ route('verify', $user->verification_token) }}