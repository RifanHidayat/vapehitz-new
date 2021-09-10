@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="{{url('/user')}}"><em class="icon ni ni-arrow-left"></em><span>Pengeluaran Badstock</span></a></div>
            <h3 class="nk-block-title fw-normal">Tambah User</h3>
        </div>
    </div>
    <div class="card card-bordered">
        <div class="card-inner">
            <form action="{{ route('register') }}" method="post">
                @csrf
                @if(session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Something it's wrong:
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Nama Lengkap</label>
                        <div class="form-control-wrap">
                            <input type="text" name="name" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Username</label>
                        <div class="form-control-wrap">
                            <input type="text" name="username" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Group</label>
                        <div class="form-control-wrap">
                            <select name="group" class="form-control">
                                @foreach($groups as $group)
                                <option value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Email</label>
                        <div class="form-control-wrap">
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Password</label>
                        <div class="form-control-wrap">
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Konfirmasi Password</label>
                        <div class="form-control-wrap">
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection