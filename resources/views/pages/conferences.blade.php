@extends('layouts.app')

@section('title', 'Hallway')

@section('content')
<event-hallway
    :event_slug = "{{ json_encode($slug) }}"
></event-hallway>
@endsection
