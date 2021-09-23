@extends('layouts.app')

@section('title', 'Vapehitz')
@section('content')
@php $permission = json_decode(Auth::user()->group->permission);@endphp
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">User</h3>
            <div class="nk-block-des text-soft">
                <p>Manage User</p>
            </div>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-download-cloud"></em>
                                <span>Export</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-white btn-dim btn-outline-primary disabled" data-toggle="tooltip" data-placement="top" title="On Development">
                                <em class="icon ni ni-reports"></em>
                                <span>Reports</span>
                            </a>
                        </li>
                        @if(in_array("add_data_user", $permission))
                        <li>
                            <a href="#userModal" data-toggle="modal" data-target="#userModal" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em>
                                <span>New User</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div>
<div class="nk-block nk-block-lg">
    <div class="card card-bordered">
        <div class="card-inner">
            <table class="table table-stripped" id="user-table">
                <thead>
                    <tr class="text-center">
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Group</th>
                        <th>Action</th>
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

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Nama</label>
                                <span class="form-note">Kolom ini wajib di isi</span>
                                <div class="form-control-wrap">
                                    <input type="text" name="name" class="form-control">
                                </div>
                            </div>


                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Username</label>
                                <span class="form-note">Kolom ini wajib di isi</span>
                                <div class="form-control-wrap">
                                    <input type="text" name="username" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Password</label><span class="form-note">Kolom ini wajib di isi</span>
                                <div class="form-control-wrap">
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Konfirmasi Password</label>
                                <span class="form-note">Kolom ini wajib di isi</span>
                                <div class="form-control-wrap">
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Group</label>
                                <span class="form-note">Kolom ini wajib di isi</span>
                                <div class="form-control-wrap">
                                    <select name="group" class="form-control">
                                        @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group col-md-6">
                                <label class="form-label" for="full-name-1">Email</label>
                                <span class="form-note">&nbsp;</span>
                                <div class="form-control-wrap">
                                    <input type="email" name="email" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                    <!-- <div class="col-lg-5">
                                <div class="form-group">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <span class="form-note">Kolom ini wajib di isi</span>
                                </div>
                            </div> -->

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
        NioApp.DataTable.init = function() {
            NioApp.DataTable('#user-table', {
                processing: true,
                serverSide: true,
                destroy: true,
                autoWidth: false,
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
                        name: 'action',
                        className: 'text-center',
                    },
                ]
            })
            $.fn.DataTable.ext.pager.numbers_length = 7;
        }

        NioApp.DataTable.init();

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
                    userTable.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Data has been deleted',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // window.location.reload();

                        }
                    })
                }
            })
        });
    });
</script>
@endsection