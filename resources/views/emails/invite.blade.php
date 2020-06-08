@component('mail::message')
@if ($company)
##  {{ Auth::user()->name }} Te a invitado a unirte a AdMuni
@else
##  {{ Auth::user()->name }} Te a invitado a unirte a AdMuni
@endif


Hola {{ $name }},

**AdMuni** Es un enfoque sensible al trabajo y colaboración para desarrollo local.
Haz clic en el siguiente link para crear tu cuenta:

@component('mail::button', ['url' => url('register/' . $token)])
Crear Cuenta
@endcomponent

@endcomponent