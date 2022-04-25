@extends('layouts.app')

@section('head')
<link href="{{ asset('vendor/fonts/circular-std/style.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('libs/css/timeline.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />

<!-- <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/css/buttons.bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/css/select.bootstrap4.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/css/fixedHeader.bootstrap4.css') }}"> -->

@endsection
@section('title', 'Vapehitz')
@section('pagestyle')
<style>
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        /* padding: 5px 10px; */
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ff407b;

    }

    .notification-badge .number {
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 12px;
        color: white;
    }

    .input-date-bs:read-only {
        background-color: #fff;
    }

    .input-group-text {
        line-height: 0.5;
    }
</style>

@endsection

@section('content')
@php
$userLoginPermissions = [];
if (request()->session()->has('userLoginPermissions')) {
$userLoginPermissions = request()->session()->get('userLoginPermissions');
}
@endphp
<!-- ============================================================== -->
<!-- wrapper  -->
<!-- ============================================================== -->
<div class="dashboard-wrapper">
    <div class="dashboard-ecommerce">
        <div class="container-fluid dashboard-content ">
            <!-- ============================================================== -->
            <!-- pageheader  -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="page-header">
                        <h2 class="pageheader-title">Import Product</h2>
                        <p class="pageheader-text">Nulla euismod urna eros, sit amet scelerisque torton lectus vel mauris facilisis faucibus at enim quis massa lobortis rutrum.</p>
                        <div class="page-breadcrumb">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/" class="breadcrumb-link">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="/company" class="breadcrumb-link">Attendace</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Upload</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end pageheader  -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    
                    <div class="card">
                    <div class="card-body">
                        
                        <h1 class="text-center"><i class="fas fa-upload fa-2x"></i></h1>
                        <h3 class="card-title text-center">Pilih & Upload File (.xlsx)</h3>
                        <p class="text-muted text-center">Format file yang diupload harus sesuai dengan template</p>
                        <form autocomplete="off" enctype="multipart/form-data" @submit.prevent="submitForm">
                            
                            <div class="my-5 text-center">
                                <!--<div class="custom-file">-->
                                <!--  <input type="file" ref="file" accept=".xlsx, .xls" v-on:change="handleFileUpload" class="custom-file-input" id="customFile">-->
                                <!--  <label class="custom-file-label" for="customFile">Choose file</label>-->
                                <!--</div>-->
                                <input type="file" ref="file" accept=".xlsx, .xls" v-on:change="handleFileUpload">
                            </div>
                            <div class="mt-3 text-center">
                                <!--<button type="submit" class="btn btn-primary px-5" v-bind:disabled="loading || !file"><span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Upload</button>-->
                                <button type="submit" class="btn btn-primary px-5" >Upload</button>
                            </div>
                        </form>
                        <div v-if="loading" class="progress mt-5">
                          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- end wrapper  -->
<!-- ============================================================== -->
@endsection


@section('pagescript')
<script>
 
    let app = new Vue({
        el: '#app',
        data: {
            loading: false,
            file: '',
        },
        methods: {
            handleFileUpload: function() {
                this.file = this.$refs.file.files[0];
            },
            previewFiles(event) {
                this.file = event.target.files[0];
            },
            submitForm: function() {
                console.log("tes");
                let vm = this;
                vm.loading = true;

                let data = {
                    file: vm.file,
                }

                let formData = new FormData();
                for (var key in data) {
                    formData.append(key, data[key]);
                }

                axios.post('product/do-upload-update', formData)
                    .then(function(response) {
                        vm.loading = false;
                        Swal.fire(
                            'Success',
                            'Your data has been saved',
                            'success'
                        ).then((result) => {
                            // if (result.isConfirmed) {
                            //     window.location.href = vm.url;
                            // }
                        })
                        console.log(response);
                    })
                    .catch(function(error) {
                        vm.loading = false;
                        Swal.fire(
                            'Oops!',
                            'Something wrong, make sure you already choose a file',
                            'error'
                        )
                    });
            },
        }
    })
</script>

<script>
    $(function() {

    })
</script>
@endsection