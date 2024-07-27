<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h2>Login</h2>
    <form action="{{ route('api.login') }}" method="POST">
        @csrf
        <label for="login_email">Email:</label>
        <input type="email" id="login_email" name="email" required>

        <label for="login_password">Password:</label>
        <input type="password" id="login_password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>