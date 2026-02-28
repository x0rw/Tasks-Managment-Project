@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title">Login</h1>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <label class="form-control w-full">
                        <span class="label-text">Email</span>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input input-bordered w-full"
                            required
                            autofocus
                        >
                    </label>

                    <label class="form-control w-full">
                        <span class="label-text">Password</span>
                        <input
                            type="password"
                            name="password"
                            class="input input-bordered w-full"
                            required
                        >
                    </label>

                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="remember" value="1" class="checkbox checkbox-sm" {{ old('remember') ? 'checked' : '' }}>
                        <span class="label-text">Remember me</span>
                    </label>

                    <button type="submit" class="btn btn-primary w-full">Login</button>
                </form>

                <p class="text-sm text-base-content/70 mt-2">
                    No account?
                    <a href="{{ route('register') }}" class="link link-primary">Register</a>
                </p>
            </div>
        </div>
    </div>
@endsection
