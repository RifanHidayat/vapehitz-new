@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
@php
$userLoginPermissions = [];
if (request()->session()->has('userLoginPermissions')) {
$userLoginPermissions = request()->session()->get('userLoginPermissions');
}
@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between g-3">
        <div class="nk-block-head-content">
            <a href="{{url('/user')}}" class="btn btn-round btn-outline-success d-none d-sm-inline-flex"><em class="icon ni ni-arrow-left"></em><span>Back</span></a>
        </div>
        <div class="nk-block-head-content">
            <button type="button" class="btn btn-round btn-outline-danger" data-toggle="modal" data-target=".bd-example-modal-lg"><em class="icon ni ni-setting"></em><span>Ganti Password</span></button>
        </div>
    </div>
</div>
<div class="card card-bordered mt-3">
    <div class="card-inner">
        <div class="card-head">
            <h5 class="card-title">Edit User : {{$user->username}}</h5>
        </div>
        <form action="/user/{{$user->id}}" class="gy-3" method="post">
            @method('patch')
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
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <!-- <span class="form-note">(*)</span> -->
                        <label class="form-label" for="site-name">Nama Lengkap</label>
                        <span class="form-note">Kolom ini wajib di isi</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" name="name" value="{{$user->name}}" class="form-control" id="site-name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <span class="form-note">Kolom ini wajib di isi</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" name="username" value="{{$user->username}}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            @if(in_array("add_data_user", $userLoginPermissions))
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">Group</label>
                        <span class="form-note">Kolom ini wajib di isi</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <select name="group" class="form-control">
                                @foreach($group as $groups)
                                <option value="{{$groups->id}}">{{$groups->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">Group</label>
                        <span class="form-note">Kolom ini wajib di isi</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <select name="group" class="form-control">
                                <option value="{{$user->group->id}}">
                                    {{$user->group->name}}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <span class="form-note">Kolom ini wajib di isi</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="email" name="email" value="{{$user->email}}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-lg-12 text-right">
                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-primary"><em class="icon ni ni-save"></em>&nbsp; Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Halaman Ganti Password</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="submitForm">
                <div class="card">
                    <div class="card-inner">
                        <div v-if="errors.length" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong v-for="error in errors">
                                @{{ error }}
                            </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="full-name-1">Username</label>
                                    <div class="form-control-wrap">
                                        <input type="text" v-model="username" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="full-name-1">Password Baru</label>
                                    <div class="form-control-wrap">
                                        <input type="password" v-model="password" class="form-control" placeholder="Password Baru">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-white text-right">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- EndModal -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            username: '{{$user->username}}',
            password: '',
            errors: [],
        },
        methods: {
            submitForm: function() {
                if (this.password) {
                    return this.sendData();
                }
                this.errors = [];
                if (!this.password) {
                    this.errors.push('Password Tidak Boleh Kosong !');
                }
            },
            sendData: function() {
                console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.patch('/user/change/{{$user->id}}', {
                        password: this.password,
                    })
                    .then(function(response) {
                        vm.loading = false;
                        Swal.fire({
                            title: 'Success',
                            text: 'Data has been saved',
                            icon: 'success',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/user/edit/{{$user->id}}';
                            }
                        })
                        // console.log(response);
                    })
                    .catch(function(error) {
                        vm.loading = false;
                        console.log(error);
                        Swal.fire(
                            'Oops!',
                            'Something wrong',
                            'error'
                        )
                    });
            },
        }
    });
</script>
@endsection