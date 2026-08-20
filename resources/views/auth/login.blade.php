<x-guest-layout>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="alert-status">
            <i class="fas fa-circle-check"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-wrap">
                <i class="fas fa-envelope input-icon"></i>
                <input id="email" type="email" name="email"
                       class="form-input"
                       value="{{ old('email') }}"
                       placeholder="admin@sekolah.sch.id"
                       required autofocus autocomplete="username">
            </div>
            @error('email')
                <p class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock input-icon"></i>
                <input id="password" type="password" name="password"
                       class="form-input"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                <span class="toggle-pw" onclick="togglePassword()" id="toggleIcon">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
            @error('password')
                <p class="error-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="row-options">
            <label class="remember-label">
                <input id="remember_me" type="checkbox" class="remember-check" name="remember">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login" id="btnLogin">
            <span class="btn-text">
                <i class="fas fa-right-to-bracket" id="btnIcon"></i>
                <span id="btnLabel">Masuk ke Dashboard</span>
            </span>
        </button>
    </form>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn   = document.getElementById('btnLogin');
            const icon  = document.getElementById('btnIcon');
            const label = document.getElementById('btnLabel');
            btn.disabled = true;
            btn.style.opacity = '.8';
            icon.className  = 'fas fa-circle-notch fa-spin';
            label.textContent = 'Memproses...';
        });
    </script>

</x-guest-layout>
