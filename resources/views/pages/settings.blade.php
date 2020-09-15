@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<settings
    :user="{{ json_encode($user) }}"
    :profile="{{ json_encode($profile) }}"
></settings>
@endsection
