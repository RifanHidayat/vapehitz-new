@extends('layouts.app')

@section('title', 'Vapehitz')
<style>
    .dataTables_filter {
        text-align: right;
        width: 90%;
    }

    table tr th {
        font-size: 15px;
        /* color: black; */
    }

    table tr td {
        font-size: 13px;
        /* color: black; */
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
        <h4 class="nk-block-title fw-normal">Permintaan Barang Dari Retail</h4>
    </div>
</div>
<div class="nk-block nk-block-lg">
    <div class="card card-bordered">
        <div class="card-inner overflow-hidden">
            <div class="table-responsive">
                <table class="table table-striped" id="approve-studio">
                    <thead class="text-center">
                        <tr>
                            <th>Nomor Proses</th>
                            <th>Tanggal Proses</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>
    let app = new Vue({
        el = 'app',
        methods: {

        }
    })
</script>
<script>
    $(function() {
        const requestToRetailTable = $('#approve-studio').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            autoWidth: false,
            dom: '<"pull-left"f><"pull-right"l>ti<"bottom"p>',
            ajax: {
                url: '/datatables/approve-central-studio',
                type: 'GET',
            },
            columns: [{
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
    });
</script>
@endsection