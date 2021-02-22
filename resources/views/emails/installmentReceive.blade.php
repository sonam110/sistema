@component('mail::message')
# Introduction

Hemos recibido el pago

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
