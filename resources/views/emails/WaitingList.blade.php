@php
    $app_name = config('app.name');
@endphp

@component('mail::message')
# Welcome to the ECS Platform

Hey there,

Welcome to {{$app_name}}. Our platform enables you to connect with fellow Africans that can help you get yoour money to loved ones.

With {{$app_name}}, you can avoid high fees, slow processing times, and complicated procedures associated with sending money abroad. Plus, we offer 24/7 customer support to help you every step of the way.

Thanks for joining the {{$app_name}} waiting list. You will be contacted once the platform goes live.

Best regards,<br>
The {{$app_name}} Team.

@endcomponent