@extends('layouts.app')

@section('title', 'Meet N Mingle')

@section('content')
<home :banned_user="{{ json_encode(session('user')) }}"></home>
@endsection