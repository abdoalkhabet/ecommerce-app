<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TestAuth</title>
</head>
<body>
    <h1>test Auth</h1>
    <h2>Register</h2>
    <form action="{{ route('api.register') }}" method="POST">
        @csrf
        <label for="reg_name">Name:</label>
        <input type="text" id="reg_name" name="name" required>

        <label for="reg_email">Email:</label>
        <input type="email" id="reg_email" name="email" required>

        <label for="reg_password">Password:</label>
        <input type="password" id="reg_password" name="password" required>


        <button type="submit">Register</button>
    </form>

    
</body>
</html>