<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cinema4me POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <login-screen 
            v-if="currentScreen === 'login'" 
            @login="handleLogin"
        ></login-screen>

        <location-selection 
            v-if="currentScreen === 'location'" 
            :employee="employee"
            @location-select="handleLocationSelect"
            @logout="logout"
        ></location-selection>

        <pos-main 
            v-if="currentScreen === 'pos'" 
            :employee="employee"
            :location="location"
            @cart-update="updateCart"
            @logout="logout"
        ></pos-main>
    </div>
</body>
</html>

