<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Facebook simple authentication</title>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @if (empty($user_data))
                <button onclick="window.location.href='{{ $login_url }}';">Login via Facebook</button>
            @else
                <p>You have authorized!</p>
                @if (empty($user_data['user_info']))
                    <p>Something went wrong, cannot receive user data!</p>
                @else
                    <p>ID: {{ $user_data['user_info']['id'] }}</p>
                    <p>Name: {{ $user_data['user_info']['name'] }}</p>
                @endif
                <button onclick="window.location.href='{{ $user_data['logout_url'] }}';">Logout</button>
            @endif
        </div>
    </body>
</html>
