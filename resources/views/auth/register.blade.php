<x-layout>
    <div class="section-head">
        <p class="eyebrow">Account</p>
        <h2>Create an account</h2>
        <p class="lead">Register to place orders from the collection.</p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="form">
        @csrf

        <div class="field">
            <label for="name" class="field-label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   autocomplete="name" class="input" required>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="email" class="field-label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   autocomplete="email" class="input" required>
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password" class="field-label">Password</label>
            <input type="password" id="password" name="password"
                   autocomplete="new-password" class="input" required>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation" class="field-label">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   autocomplete="new-password" class="input" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Register</button>
        </div>
    </form>
</x-layout>
