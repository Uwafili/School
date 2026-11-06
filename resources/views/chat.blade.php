@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
    <livewire:order-chat :order="$order" :contact="$contact" />
</div>
@endsection
