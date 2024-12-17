@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                                <a href="javascript:window.print()" class="btn btn-success ml-4"><i class="ri-printer-line mr-4"></i> Print</a>
                            </div>
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h1>{{projectName()}}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Stock Transfer Vouchar</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12 ">

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-9">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Warehouses</p>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">From :</span> {{$transfer->from->name}}</h5>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">To :</span> {{$transfer->to->name}}</h5>
                                    </div>
                                    <div class="col-3">
                                        <p> <span class="text-muted mb-0 text-uppercase fw-semibold">ID # </span><span class="fs-14 mb-0">{{$transfer->id}}</span></p>
                                        <p> <span class="text-muted mb-0 text-uppercase fw-semibold">Date : </span><span class="fs-14 mb-0">{{date("d M Y" ,strtotime($transfer->date))}}</span></p>
                                        <p> <span class="text-muted mb-0 text-uppercase fw-semibold">Transfered By : </span><span class="fs-14 mb-0">{{$transfer->user->name}}</span></p>
                                    </div>
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                                <thead>
                                                    <tr class="table-active">
                                                        <th scope="col" style="width: 50px;">#</th>
                                                        <th scope="col" class="text-start">Product</th>
                                                        <th scope="col" class="text-end">Unit</th>
                                                        <th scope="col" class="text-end">Size</th>
                                                        <th scope="col" class="text-end">Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="products-list">
                                                   @foreach ($transfer->details as $key => $product)
                                                       <tr>
                                                        <td class="p-1 m-1">{{$key+1}}</td>
                                                        <td class="text-start p-1 m-1">{{$product->product->code}} | {{$product->product->color}} | {{$product->product->desc}}</td>
                                                        <td class="text-end p-1 m-1">{{$product->unit}}</td>
                                                        <td class="text-end p-1 m-1">{{$product->size}} ({{$product->lengthF}}.{{$product->lengthI}}x{{$product->widthF}}.{{$product->widthI}})</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->qty)}}</td>
                                                       </tr>
                                                   @endforeach
                                                </tbody>

                                            </table><!--end table-->
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <p><strong>Notes: </strong>{{$transfer->notes}}</p>
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->

                    </div><!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

@endsection

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
<link href='https://fonts.googleapis.com/css?family=Noto Nastaliq Urdu' rel='stylesheet'>
<style>
    .urdu {
        font-family: 'Noto Nastaliq Urdu';font-size: 12px;
    }
    </style>
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js')}}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
@endsection

