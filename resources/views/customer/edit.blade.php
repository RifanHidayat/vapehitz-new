@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
<div class="components-preview wide-md mx-auto">
    <div class="nk-block-head nk-block-head-lg wide-sm">
        <div class="nk-block-head-content">
            <div class="nk-block-head-sub"><a class="back-to" href="/customer"><em class="icon ni ni-arrow-left"></em><span>Master Data Customer</span></a></div>
            <h2 class="nk-block-title fw-normal">Edit Data Customer</h2>
        </div>
    </div><!-- .nk-block -->
    <div class="nk-block nk-block-lg">
        <!-- <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Tambah Kategori Barang</h4>
                <div class="nk-block-des">
                    <p>You can alow display form in column as example below.</p>
                </div>
            </div>
        </div> -->
        <div class="card card-bordered">
            <div class="card-inner">
                <!-- <div class="card-head">
                    <h5 class="card-title">Form</h5>
                </div> -->
                <form @submit.prevent="submitForm">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Kode</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="code" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Nama</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Alamat</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="address" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">No. Tlp</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="telephone" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">No. HP/WA</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="handphone" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Email</label>
                                <div class="form-control-wrap">
                                    <input type="text" v-model="email" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="full-name-1">Status</label>
                                <div class="form-control-wrap">
                                    <select v-model="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="email-address-1">Email address</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="email-address-1">
                                </div>
                            </div>
                        </div> -->
                        <div class="col-12">
                            <div class="form-group text-right">
                                <button class="btn btn-primary" type="submit" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span>Simpan</span>
                                </button>
                                <!-- <button type="submit" class="btn btn-lg btn-primary">Simpan</button> -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- .nk-block -->
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el: '#app',
        data: {
            code: '{{ $customer->code }}',
            name: '{{ $customer->name }}',
            address: '{{ $customer->address }}',
            telephone: '{{ $customer->telephone }}',
            handphone: '{{ $customer->handphone }}',
            email: '{{ $customer->email }}',
            status: '{{ $customer->status }}',
            loading: false,
        },
        methods: {
            submitForm: function() {
                this.sendData();
            },
            sendData: function() {
                // console.log('submitted');
                let vm = this;
                vm.loading = true;
                axios.patch('/customer/{{$customer->id}}', {
                        name: this.name,
                        code: this.code,
                        address: this.address,
                        telephone: this.telephone,
                        handphone: this.handphone,
                        email: this.email,
                        status: this.status,
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
                                window.location.href = '/customer';
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
            }
        }
    })
</script>
@endsection