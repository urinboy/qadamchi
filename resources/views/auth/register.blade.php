@extends('layouts.app')

@section('container_class', 'auth')

@section('content')
    <div class="card auth-card">
        <div class="auth-brand">
            <img src="/assets/favicon.svg" alt="Qadamchi">
            <span>Qadamchi</span>
        </div>
        <h1>Ro'yxatdan o'tish</h1>
        <p class="sub">Hisob yarating va dashboard'ga kiring.</p>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="field">
                <label>Ism</label>
                <div class="input-wrap">
                    <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Ismingiz">
                    <svg class="input-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                @if (isset($errors['name'])) <div class="error">{{ $errors['name'][0] }}</div> @endif
            </div>

            <div class="field">
                <label>Email</label>
                <div class="input-wrap">
                    <input type="email" name="email" inputmode="email" value="{{ old('email') }}" autocomplete="email" placeholder="email@example.com">
                    <svg class="input-icon" viewBox="0 0 24 24"><path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5-8-5h16zm0 12H4V8l8 5 8-5v10z"/></svg>
                </div>
                @if (isset($errors['email'])) <div class="error">{{ $errors['email'][0] }}</div> @endif
            </div>

            <div class="field">
                <label>Parol</label>
                <div class="input-wrap pwd-wrap">
                    <input type="password" name="password" autocomplete="new-password" placeholder="••••••••">
                    <svg class="input-icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3-9H9V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2z"/></svg>
                    <button type="button" class="pwd-toggle" aria-label="Parolni ko'rsatish" tabindex="-1">
                        <svg class="eye-open" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.51.51C1.81 8.33.73 10.06 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65a3 3 0 0 0 3 3c.22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53a5 5 0 0 1-5-5c0-.79.2-1.53.53-2.2z"/></svg>
                    </button>
                </div>
                @if (isset($errors['password'])) <div class="error">{{ $errors['password'][0] }}</div> @endif
            </div>

            <div class="field">
                <label>Parol tasdiqlash</label>
                <div class="input-wrap pwd-wrap">
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••">
                    <svg class="input-icon" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3-9H9V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2z"/></svg>
                    <button type="button" class="pwd-toggle" aria-label="Parolni ko'rsatish" tabindex="-1">
                        <svg class="eye-open" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.51.51C1.81 8.33.73 10.06 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65a3 3 0 0 0 3 3c.22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53a5 5 0 0 1-5-5c0-.79.2-1.53.53-2.2z"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn block">Ro'yxatdan o'tish</button>
        </form>
        <p class="muted auth-foot">Hisobingiz bormi? <a href="{{ route('login') }}">Kirish</a></p>
    </div>
@endsection