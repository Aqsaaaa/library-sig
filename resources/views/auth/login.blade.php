<x-layout title="Login">
    <div class="flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md my-10 mx-4">
            <h1 class="text-2xl font-semibold mb-6 text-center">Login</h1>
            <form method="POST" action="{{ url('/login') }}">
                @csrf
                <x-input label="Email" type="email" name="email" required autofocus />
                <x-input label="Password" type="password" name="password" required />
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <input type="checkbox" name="remember" id="remember" class="mr-1" />
                        <label for="remember" class="text-sm">Remember me</label>
                    </div>
                    <a href="#" class="text-sm text-[#f53003] hover:underline">Forgot your password?</a>
                </div>
                <x-button>Log in</x-button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-[#f53003] hover:underline">Register</a>
            </p>
        </div>
    </div>
</x-layout>

