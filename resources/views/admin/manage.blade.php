@extends('layouts.navbar')
@section('content')
    <div class="container mx-auto mt-8">
        <h2 class="text-2xl font-bold mb-4 text-center">User Management</h2>
        <p class="mb-4 text-center text-sm text-gray-600">Passwords are securely hashed and cannot be viewed. Send users a reset link if they need access.</p>
        @if (session('success'))<div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-800">{{ session('error') }}</div>@endif
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
                                    <a href="{{ route('user.edit', $user) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700 transition hover:bg-gray-100">Edit</a>
                                    <form action="{{ route('user.password-reset', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-yellow-100 px-3 py-2 text-xs font-bold text-yellow-900 transition hover:bg-yellow-200">Reset key</button>
                                    </form>
                                    <form action="{{ route('user.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100" title="Delete user">Delete</button>
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