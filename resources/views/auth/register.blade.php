<x-guest-layout>
    <main class="auth-panel">
        <h1>Buat akun WMS</h1>
        <p class="muted">Isi data untuk membuat akun baru.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Ulangi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
            <button type="submit">Daftar</button>
        </form>

        <p class="auth-footer">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </main>
</x-guest-layout>
