@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
@php
$userLoginPermissions = [];
if (request()->session()->has('userLoginPermissions')) {
$userLoginPermissions = request()->session()->get('userLoginPermissions');
}
@endphp
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="{{url('/user')}}"><em class="icon ni ni-arrow-left"></em><span>Data User</span></a></div>
            <h3 class="nk-block-title fw-normal">Edit User</h3>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target=".bd-example-modal-lg">Ganti Password</button>
    </div>
    <div class="card card-bordered mt-3">
        <div class="card-inner">
            <form action="/user/{{$user->id}}" method="post">
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
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Nama Lengkap</label>
                        <div class="form-control-wrap">
                            <input type="text" name="name" value="{{$user->name}}" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Username</label>
                        <div class="form-control-wrap">
                            <input type="text" name="username" value="{{$user->username}}" class="form-control">
                        </div>
                    </div>
                </div>
                <p></p>
                @if(in_array("add_data_user", $userLoginPermissions))
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Group</label>
                        <div class="form-control-wrap">
                            <select name="group" class="form-control">
                                @foreach($group as $groups)
                                <option value="{{$groups->id}}">{{$groups->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Group</label>
                        <div class="form-control-wrap">
                            <select name="group" class="form-control">
                                <option value="{{$user->group->id}}">
                                    {{$user->group->name}}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif
                <p></p>
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="full-name-1">Email</label>
                        <div class="form-control-wrap">
                            <input type="email" name="email" value="{{$user->email}}" class="form-control">
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