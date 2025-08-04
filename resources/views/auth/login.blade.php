<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Entrar</h1>

    @if ($errors->any())
        <div>
            <ul style="color:red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email:
            <input type="email" name="email" required>
        </label><br>
        <label>Senha:
            <input type="password" name="password" required>
        </label><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
