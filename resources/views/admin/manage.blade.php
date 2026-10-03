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
                        <th class="py-2 px-4 border-b text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="hover:bg-yellow-50">
                            <td class="py-2 px-4 border-b text-center">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $user->name }}</td>
                            <td class="py-2 px-4 border-b text-left">{{ $user->email }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ $user->usertype }}</td>
                            <td class="py-2 px-4 border-b text-center">
                                <form action="{{ route('user.password-reset', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-yellow-100 px-3 py-2 text-xs font-bold text-yellow-900 transition hover:bg-yellow-200">Send reset link</button>
                                </form>
                            </td>
                            <td class="py-2 px-4 border-b text-center">
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-full hover:bg-red-100 transition" title="Delete">
                                        <!-- Heroicons Trash SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
@endsection