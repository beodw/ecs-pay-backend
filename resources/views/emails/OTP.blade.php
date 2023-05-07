@php
    $app_name = config('app.name');
@endphp

@component('mail::message')
# Welcome to the ECS Pay Platform

Dear {{$user_name}},

Welcome to {{$app_name}}. Our platform enables you to send money overseas from Africa quickly, safely, and affordably.

With {{$app_name}}, you can avoid high fees, slow processing times, and complicated procedures associated with sending money abroad. Plus, we offer 24/7 customer support to help you every step of the way.

Join {{$app_name}} now and start sending money overseas from Africa with ease.

Best regards,<br>
The {{$app_name}} Team.

@endcomponent