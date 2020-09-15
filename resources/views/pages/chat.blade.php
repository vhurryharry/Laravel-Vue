@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<chat
    :recipient="{{ json_encode($recipient) }}"
></chat>
@endsection
