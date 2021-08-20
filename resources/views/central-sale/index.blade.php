@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<a href="/central-sale/create" class="btn btn-primary">Tambah</a>
@foreach($centralSale as $centralSales)
{{$centralSales->code}}
@endforeach
@endsection