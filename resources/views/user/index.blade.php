@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .dataTables_filter {
        text-align: right;
        width: 90%;
    }

    table tr th {
        font-size: 15px;
        color: black;
    }

    table tr td {
        font-size: 13px;
        color: black;
    }

    .pull-left {
        float: left !important;
    }

    .pull-right {
        float: right !important;
        margin-bottom: 20px;
    }

    .bottom {
        float: right !important;
    }
</style>
@section('content')
<div class="nk-block-head nk-block-head-lg wide-sm">
    <div class="nk-block-head-content">
        <!-- <div class="nk-block-head-sub"><a class="back-to" href="html/components.html"><em class="icon ni ni-arrow-left"></em><span>Manage</span></a></div> -->
        <h4 class="nk-block-title fw-normal">Data User</h4>
    </div>
</div><!-- .nk-block -->
@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
    {{ session('status') }}
</div>
@endif
<div class="nk-block nk-block-lg">
    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#userModal">
        <em class="fas fa-plus"></em> &nbsp; Tambah User
    </button>
    <p></p>
    <div class="card card-bordered">
        <div class="card-inner">
            <table class="table table-stripped" id="user-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Group</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modals -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">×</span>
                    </button>
                    <h5>Tambah User</h5>
                </div>
                <div class="card-inner">
                    <form action="{{ route('register') }}" method="post">
                        @csrf
                        @if(session('errors'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Something it's wrong:
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
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
    </div>
</div>
<!-- End Modals -->
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        methods: {

        }
    })
</script>
<script>
    $(function() {
        const userTable = $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            autoWidth: false,
            dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
            ajax: {
                url: '/datatables/user',
                type: 'GET',
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'group.name',
                    name: 'group.name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
        $('#user-table').on('click', 'tr .btn-delete', function(e) {
            e.preventDefault();
            // alert('click');
            const id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "The data will be deleted",
                icon: 'warning',
                reverseButtons: true,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.delete('/user/' + id)
                        .then(function(response) {
                            console.log(response.data);
                        })
                        .catch(function(error) {
                            console.log(error.data);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops',
                                text: 'Something wrong',
                            })
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been deleted',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();

                        }
                    })
                }
            })
        });
    });
</script>
@endsection