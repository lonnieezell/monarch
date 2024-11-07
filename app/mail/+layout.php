<!DOCTYPE html>
<m-html>
    <m-head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <x-slot name="title">
            <m-title>Reset Password</m-title>
        </x-slot>
    </m-head>
    <m-body style="background-color: #eaeaea;">
        <m-container style="background-color: #ffffff; border: 1px solid #c8c8c8; padding: 1rem 2rem;">
            <m-header style="">
                <x-slot name="header"></x-slot>
            </m-header>
            <x-slot name="body" style="padding-top: 2rem;"></x-slot>
        </m-container>
        <x-slot></x-slot>
    </m-body>
</m-html>
