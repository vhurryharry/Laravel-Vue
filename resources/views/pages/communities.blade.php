@extends('layouts.app')

@section('title', 'Communities')

@section('content')
<communities
:interests="{{ $interests }}"
></communities>
@endsection
