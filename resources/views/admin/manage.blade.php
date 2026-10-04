@extends('layouts.navbar')
@section('content')
    <div class="container mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4 text-center">User Management</h2>
        <p class="mb-4 text-center text-sm text-gray-600">Passwords are securely hashed and cannot be viewed. Send users a
            reset link if they need access.</p>
        @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
        @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>@endif
        @if (session('warning'))
        <div class="mb-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">{{ session('warning') }}</div>@endif
        <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5">
            <h3 class="mb-1 text-lg font-bold">Quick sign up: User</h3>
            <p class="mb-4 text-sm text-gray-600">A password setup link will be sent to the new user.</p>
            <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @csrf
                <div>
                    <label for="user-name" class="mb-1 block text-sm font-semibold">Name</label>
                    <input id="user-name" name="name" value="{{ old('name') }}" required maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="user-email" class="mb-1 block text-sm font-semibold">Email</label>
                    <input id="user-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full rounded-lg bg-yellow-500 px-4 py-2 font-bold text-white hover:bg-yellow-600">Create
                        user</button>
                </div>
            </form>
        </section>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="py-2 px-4 border-b text-center">Num</th>
                        <th class="py-2 px-4 border-b text-left">User Name</th>
                        <th class="py-2 px-4 border-b text-left">Gmail Address</th>
                        <th class="py-2 px-4 border-b text-center">POC</th>
                        <th class="py-2 px-4 border-b text-center">Password access</th>
                        <th class="py-2 px-4 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="hover:bg-yellow-50">
                            <td class="py-2 px-4 border-b text-center">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $user->name }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $user->email }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ $user->usertype }}</td>
                            <td class="py-2 px-4 border-b text-center text-sm text-gray-600">Hashed; cannot be viewed</td>
                            <td class="py-2 px-4 border-b text-center">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <a href="{{ route('user.edit', $user) }}"
                                        class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700 transition hover:bg-gray-100">Edit</a>
                                    <form action="{{ route('user.password-reset', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-lg bg-yellow-100 px-3 py-2 text-xs font-bold text-yellow-900 transition hover:bg-yellow-200">Reset
                                            key</button>
                                    </form>
                                    <form action="{{ route('user.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                                            title="Delete user">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection