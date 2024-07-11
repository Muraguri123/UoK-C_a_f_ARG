<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Password Reset</title>
</head>
<body>
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <input type="email" name="email" required placeholder="Email">
        <button type="submit">Send Password Reset Link</button>
    </form>
    @if (session('status'))
        <div>{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>
