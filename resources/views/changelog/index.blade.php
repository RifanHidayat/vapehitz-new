@extends('layouts.app')

@section('title', 'Vapehitz')
@section('pagestyle')
<style>
    #customers tr th,
    #customers tr td {
        font-size: 0.875rem;
    }
</style>
@endsection
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Changelog</h3>
            <div class="nk-block-des text-soft">
                <p>List perubahan aplikasi</p>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div>
<div class="nk-block nk-block-lg">
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div class="row">
                <div class="col-md-3">
                    <h4 class="text-primary">27 Sep 2021</h4>
                </div>
                <div class="col-md-9">
                    <h5>Pre-alpha v0.0.1</h5>
                    <ul style="list-style: initial;">
                        <li>
                            <span>Initial release</span>
                        </li>
                        <li>
                            <span>Base features</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div><!-- .nk-block -->
@endsection
@section('pagescript')

@endsection