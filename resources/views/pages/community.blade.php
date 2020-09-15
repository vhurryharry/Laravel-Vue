@extends('layouts.app')

@section('title', 'Community')

@section('content')
<community
    :slug="{{ json_encode($slug) }}"
></community>
@endsection
