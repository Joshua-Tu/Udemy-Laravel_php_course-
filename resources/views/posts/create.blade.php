@extends('layouts.app')

@section('content')
  <form method='POST' action="/posts">
    @csrf
    <input type='text' name='title' placeholder="Enter title">
    <input type='submit' name='submit'>
  </form>


@stop

@section('footer')

@stop