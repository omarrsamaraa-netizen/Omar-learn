<x-layout>
    <div class="section-head">
        <p class="eyebrow">Account</p>
        <h2>Log in</h2>
        <p class="lead">Welcome back to the collection.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="form">
        @csrf

        <div class="field">
            <label for="email" class="field-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   autocomplete="email" class="input" required>
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password" class="field-label">Password</label>
            <input type="password" id="password" name="password"
                   autocomplete="current-password" class="input" required>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-center gap-3">
            <input type="checkbox" name="remember" value="1" class="accent-brass">
            <span class="field-label">Remember me</span>
        </label>

        <div class="form-actions">
            <button type="submit" class="btn">Log in</button>
            <a href="{{ route('register') }}" class="btn btn-espresso">Create an account</a>
        </div>
    </form>
</x-layout>
