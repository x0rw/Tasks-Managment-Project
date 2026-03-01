@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="card-title">Register</h1>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <label class="form-control w-full">
                        <span class="label-text">Name</span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="input input-bordered w-full"
                            required
                            autofocus
                        >
                    </label>

                    <label class="form-control w-full">
                        <span class="label-text">Email</span>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input input-bordered w-full"
                            required
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

                    <label class="form-control w-full">
                        <span class="label-text">Confirm password</span>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="input input-bordered w-full"
                            required
                        >
                    </label>

                    <button type="submit" class="btn btn-primary w-full">Register</button>
                </form>

                <p class="text-sm text-base-content/70 mt-2">
                    Already have an account?
                    <a href="{{ route('login') }}" class="link link-primary">Login</a>
                </p>
            </div>
        </div>
    </div>
@endsection
