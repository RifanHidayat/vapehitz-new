@extends('layouts.app')

@section('title', 'Vapehitz')

@section('content')
@if (session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Berhasil!</strong>&nbsp; {{session('status')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Dashboard</h3>
            <div class="nk-block-des text-soft">
                <p>Halaman ini hanya preview untuk dashboard</p>
            </div>
        </div><!-- .nk-block-head-content -->
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-white btn-dim btn-outline-light disabled" data-toggle="dropdown"><em class="d-none d-sm-inline icon ni ni-calender-date"></em><span><span class="d-none d-md-inline">Last</span> 30 Days</span><em class="dd-indc icon ni ni-chevron-right"></em></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="#"><span>Last 30 Days</span></a></li>
                                        <li><a href="#"><span>Last 6 Months</span></a></li>
                                        <li><a href="#"><span>Last 1 Years</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="nk-block-tools-opt"><a href="#" class="btn btn-primary disabled"><em class="icon ni ni-reports"></em><span>Reports</span></a></li>
                    </ul>
                </div>
            </div>
        </div><!-- .nk-block-head-content -->
    </div><!-- .nk-block-between -->
</div><!-- .nk-block-head -->
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-xxl-6">
            <div class="card card-bordered h-100">
                <div class="card-inner">
                    <div class="card-title-group align-start gx-3 mb-3">
                        <div class="card-title">
                            <h6 class="title">Penjualan</h6>
                            <p>Penjualan <span style="color: #798bff;">Pusat</span>, <span style="color: #eb4d4b;">Retail</span>, dan <span style="color: #f9ca24">Studio</span> bulan ini</p>
                        </div>
                        <div class="card-tools">
                            <div class="dropdown">
                                <a href="#" class="btn btn-primary btn-dim d-none d-sm-inline-flex disabled" data-toggle="dropdown"><em class="icon ni ni-download-cloud"></em><span><span class="d-none d-md-inline">Download</span> Report</span></a>
                                <a href="#" class="btn btn-icon btn-primary btn-dim d-sm-none" data-toggle="dropdown"><em class="icon ni ni-download-cloud"></em></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <ul class="link-list-opt no-bdr">
                                        <li><a href="#"><span>Download Mini Version</span></a></li>
                                        <li><a href="#"><span>Download Full Version</span></a></li>
                                        <li class="divider"></li>
                                        <li><a href="#"><em class="icon ni ni-opt-alt"></em><span>More Options</span></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                        <!-- <div class="nk-sale-data">
                            <span class="amount">Rp 82.232.030</span>
                        </div> -->
                        <!-- <div class="nk-sale-data">
                            <span class="amount sm">1,937 <small>Subscribers</small></span>
                        </div> -->
                    </div>
                    <div class="nk-sales-ck large pt-4">
                        <canvas class="sales-overview-chart" id="salesOverview"></canvas>
                    </div>
                </div>
            </div><!-- .card -->
        </div><!-- .col -->
        <div class="col-xxl-8">
            <div class="card card-bordered card-full">
                <div class="card-inner">
                    <div class="card-title-group">
                        <div class="card-title">
                            <h6 class="title"><span class="mr-2">Overdue Invoice</span></h6>
                        </div>
                        <div class="card-tools">
                            <!-- <ul class="card-tools-nav">
                                <li><a href="#"><span>Paid</span></a></li>
                                <li><a href="#"><span>Pending</span></a></li>
                                <li class="active"><a href="#"><span>All</span></a></li>
                            </ul> -->
                        </div>
                    </div>
                </div>
                <div class="card-inner p-0 border-top">
                    <div class="nk-tb-list nk-tb-orders">

                        <div class="nk-tb-item nk-tb-head">
                            <div class="nk-tb-col"><span>No.</span></div>
                            <div class="nk-tb-col tb-col-sm"><span>Customer</span></div>
                            <div class="nk-tb-col tb-col-md"><span>Date</span></div>
                            <div class="nk-tb-col tb-col-lg"><span>Due Date</span></div>
                            <div class="nk-tb-col"><span>Total</span></div>
                            <div class="nk-tb-col"><span class="d-none d-sm-inline">Paid</span></div>
                            <!-- <div class="nk-tb-col"><span>&nbsp;</span></div> -->
                        </div>
                        @foreach($overdue_invoices as $invoice)
                        <div class="nk-tb-item">
                            <div class="nk-tb-col">
                                <span class="tb-lead"><a href="/central-sale/show/{{ $invoice->id }}" target="_blank">#{{ $invoice->code }}</a></span>
                            </div>
                            <div class="nk-tb-col tb-col-sm">
                                <div class="user-card">
                                    <!-- <div class="user-avatar user-avatar-sm bg-purple">
                                        <span>AB</span>
                                    </div> -->
                                    <div class="user-name">
                                        @if($invoice->customer !== null)
                                        <span class="tb-lead">{{ $invoice->customer->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="nk-tb-col tb-col-md">
                                <span class="tb-sub">{{ date("d/m/Y", strtotime($invoice->date)) }}</span>
                            </div>
                            <div class="nk-tb-col tb-col-lg">
                                <span class="tb-sub text-danger">{{ date("d/m/Y", strtotime($invoice->due_date)) }}</span>
                            </div>
                            <div class="nk-tb-col">
                                <span class="tb-sub tb-amount"><span>Rp</span> {{ number_format($invoice->net_total) }}</span>
                            </div>
                            <div class="nk-tb-col">
                                <span class="tb-sub tb-amount"><span>Rp</span> {{ number_format($invoice->total_paid) }}</span>
                            </div>
                            <!-- <div class="nk-tb-col nk-tb-col-action">
                                <div class="dropdown">
                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                        <ul class="link-list-plain">
                                            <li><a href="#">View</a></li>
                                            <li><a href="#">Invoice</a></li>
                                            <li><a href="#">Print</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-inner-sm border-top text-center d-sm-none">
                    <a href="#" class="btn btn-link btn-block">See History</a>
                </div>
            </div><!-- .card -->
        </div><!-- .col -->
    </div><!-- .row -->
</div><!-- .nk-block -->
@endsection
@section('pagescript')
<script>
    var salesOverview = {
        labels: JSON.parse('{!! json_encode($dates) !!}'),
        dataUnit: "IDR",
        lineTension: 0.1,
        datasets: [{
                label: "Penjualan Pusat",
                color: "#798bff",
                background: 'transparent',
                data: JSON.parse('{!! json_encode($central_sales) !!}'),
            },
            {
                label: "Penjualan Retail",
                color: "#eb4d4b",
                background: "transparent",
                data: JSON.parse('{!! json_encode($retail_sales) !!}'),
            },
            {
                label: "Penjualan Studio",
                color: "#f9ca24",
                background: "transparent",
                data: JSON.parse('{!! json_encode($studio_sales) !!}'),
            },
        ],
    };

    function lineSalesOverview(selector, set_data) {
        var $selector = selector ? $(selector) : $(".sales-overview-chart");
        $selector.each(function() {
            var $self = $(this),
                _self_id = $self.attr("id"),
                _get_data =
                typeof set_data === "undefined" ? eval(_self_id) : set_data;

            var selectCanvas = document
                .getElementById(_self_id)
                .getContext("2d");
            var chart_data = [];

            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    tension: _get_data.lineTension,
                    backgroundColor: _get_data.datasets[i].background,
                    borderWidth: 2,
                    borderColor: _get_data.datasets[i].color,
                    pointBorderColor: "transparent",
                    pointBackgroundColor: "transparent",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: _get_data.datasets[i].color,
                    pointBorderWidth: 2,
                    pointHoverRadius: 3,
                    pointHoverBorderWidth: 2,
                    pointRadius: 3,
                    pointHitRadius: 3,
                    data: _get_data.datasets[i].data,
                });
            }

            var chart = new Chart(selectCanvas, {
                type: "line",
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    legend: {
                        display: _get_data.legend ? _get_data.legend : false,
                        labels: {
                            boxWidth: 30,
                            padding: 20,
                            fontColor: "#6783b8",
                        },
                    },
                    maintainAspectRatio: false,
                    tooltips: {
                        enabled: true,
                        rtl: NioApp.State.isRTL,
                        callbacks: {
                            title: function title(tooltipItem, data) {
                                return data["labels"][tooltipItem[0]["index"]];
                            },
                            label: function label(tooltipItem, data) {
                                return (
                                    data.datasets[tooltipItem.datasetIndex][
                                        "data"
                                    ][tooltipItem["index"]] +
                                    " " +
                                    _get_data.dataUnit
                                );
                            },
                        },
                        backgroundColor: "#eff6ff",
                        titleFontSize: 13,
                        titleFontColor: "#6783b8",
                        titleMarginBottom: 6,
                        bodyFontColor: "#9eaecf",
                        bodyFontSize: 12,
                        bodySpacing: 4,
                        yPadding: 10,
                        xPadding: 10,
                        footerMarginTop: 0,
                        displayColors: false,
                    },
                    scales: {
                        yAxes: [{
                            display: true,
                            stacked: _get_data.stacked ?
                                _get_data.stacked : false,
                            position: NioApp.State.isRTL ? "right" : "left",
                            ticks: {
                                beginAtZero: true,
                                fontSize: 11,
                                fontColor: "#9eaecf",
                                padding: 10,
                                callback: function callback(
                                    value,
                                    index,
                                    values
                                ) {
                                    return "Rp " + Intl.NumberFormat('De-de').format(value);
                                },
                                min: 100,
                                // stepSize: 3000,
                            },
                            gridLines: {
                                color: NioApp.hexRGB("#526484", 0.2),
                                tickMarkLength: 0,
                                zeroLineColor: NioApp.hexRGB(
                                    "#526484",
                                    0.2
                                ),
                            },
                        }, ],
                        xAxes: [{
                            display: true,
                            stacked: _get_data.stacked ?
                                _get_data.stacked : false,
                            ticks: {
                                fontSize: 9,
                                fontColor: "#9eaecf",
                                source: "auto",
                                padding: 10,
                                reverse: NioApp.State.isRTL,
                            },
                            gridLines: {
                                color: "transparent",
                                tickMarkLength: 0,
                                zeroLineColor: "transparent",
                            },
                        }, ],
                    },
                },
            });
        });
    } // init chart

    NioApp.coms.docReady.push(function() {
        lineSalesOverview();
    });
</script>
@endsection