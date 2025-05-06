<x-layout title="Register">
    <div class="flex items-center justify-center">
        <div class="max-w-md w-full bg-white p-4 rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold mb-6">Register</h1>
            <form method="POST" action="{{ url('/register') }}">
                @csrf
                <x-input label="Name" type="text" name="name" required autofocus />
                <x-input label="Email" type="email" name="email" required />
                <x-input label="Password" type="password" name="password" required />
                <x-input label="Confirm Password" type="password" name="password_confirmation" required />
                <x-button>Register</x-button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                Already have an account?
                <a href="{{ route('login') }}" class="text-[#f53003] hover:underline">Log in</a>
            </p>
        </div>
    </div>
</x-layout>
