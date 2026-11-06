<x-guest-layout>
    <main class="auth-panel">
        <h1>WMS Produksi</h1>
        <p class="muted">Masuk untuk melanjutkan ke sistem gudang.</p>

        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror

            <label class="check-row" for="remember"><input id="remember" type="checkbox" name="remember"> Ingat saya</label>
            <button type="submit">Masuk</button>
        </form>

        <p class="auth-footer">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    </main>
</x-guest-layout>
