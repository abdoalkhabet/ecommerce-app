<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document urrrrrrrrr</title>
</head>
<body>
    <h1>profile page</h1>
    <form action="{{ route('api.profile.update') }}" method="post" enctype="multipart/form-data" >
        @csrf
        {{-- @method('PUT/') --}}
        <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="">
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male" >male</option>
                <option value="female" >female</option>
            </select>
            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo">

            <button type="submit">Update Profile</button>

    </form>
    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif
</body>
</html>