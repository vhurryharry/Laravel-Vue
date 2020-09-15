@extends('layouts.app')

@section('title', 'Events')

@section('content')
<event :slug="{{ json_encode($slug) }}"></event>
@endsection
