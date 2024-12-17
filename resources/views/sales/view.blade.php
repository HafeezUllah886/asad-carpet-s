@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9" id="content">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                                <a onclick="sendMsg()" class="btn btn-success ml-4"><i class="ri-whatsapp-line mr-4"></i> Send </a>
                                <a href="javascript:window.print()" class="btn btn-primary ml-4"><i class="ri-printer-line mr-4"></i> Print</a>
                            </div>

                            <div class="card-header border-bottom-dashed p-4">
                                @include('layout.header')
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12 ">
                            <div class="row">
                                <div class="col-4"></div>
                                <div class="col-4 text-center"><h2>SALES INVOICE</h2></div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Inv #</p>
                                        <h5 class="fs-14 mb-0">{{$sale->id}}</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Customer</p>
                                        <h5 class="fs-14 mb-0">{{$sale->customer->title}}</h5>
                                        <h6 class="fs-14 mb-0">{{$sale->customerID != 2 ? $sale->customer->address : $sale->customerName}}</h6>
                                    </div>
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0">{{date("d M Y" ,strtotime($sale->date))}}</h5>
                                    </div>
                                    <!--end col-->
                                    <!--end col-->
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
                                                        <th scope="col" class="text-start">Category</th>
                                                        <th scope="col" class="text-end">Size (Mtr)</th>
                                                        <th scope="col" class="text-end">Size (Feet)</th>
                                                        <th scope="col" class="text-end">Price</th>
                                                        <th scope="col" class="text-end">Qty</th>
                                                        <th scope="col" class="text-end">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="products-list">
                                                   @foreach ($sale->details as $key => $product)
                                                       <tr>
                                                        <td class="p-1 m-1">{{$key+1}}</td>
                                                        <td class="text-start p-1 m-1">{{$product->product->code}} | {{$product->product->color}} | {{$product->product->desc}}</td>
                                                        <td class="text-start p-1 m-1">{{$product->product->cat}}</td>
                                                        <td class="text-end p-1 m-1">{{$product->size}} ({{$product->width}}x{{$product->length}})</td>
                                                        <td class="text-end p-1 m-1">{{squareMetersToSquareFeet($product->size)}} ({{metersToFeet($product->width)}}x{{metersToFeet($product->length)}})</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->price,2)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->qty)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->amount,2)}}</td>
                                                       </tr>
                                                   @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="7" class="text-end m-0 p-0">Total</th>
                                                        <th class="text-end m-0 p-0">{{number_format($sale->details->sum('amount'), 2)}}</th>
                                                    </tr>

                                                    <tr>
                                                        <th colspan="7" class="text-end m-0 p-0">Delivery Charges</th>
                                                        <th class="text-end m-0 p-0">{{number_format($sale->dc, 2)}}</th>
                                                    </tr>


                                                    @if ($sale->discount > 0)
                                                    <tr>
                                                        <th colspan="7" class="text-end m-0 p-0">Discount</th>
                                                        <th class="text-end m-0 p-0">{{number_format($sale->discount, 2)}}</th>
                                                    </tr>
                                                    @endif

                                                    <tr>
                                                        <th colspan="7" class="text-end m-0 p-0">Net Payale</th>
                                                        <th class="text-end m-0 p-0 border-2 border-dark border-start-0 border-end-0">{{number_format($sale->net, 2)}}</th>
                                                    </tr>

                                                </tfoot>
                                            </table><!--end table-->
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <p><strong>Notes: </strong>{{$sale->notes}}</p>
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
        #content {
            display: ;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #content, #content * {
                visibility: visible;
            }
            #content {
                position: absolute;
                top: 0;
                left: 0;
            }
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
    <script>
      function sendMsg()
      {
        Toastify({
        text: "Generating PDF",
        className: "info",
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "center", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, #01CB3E, #96c93d)",
        }
        }).showToast();
        $.ajax({
                url: "{{ url('sales/share/') }}/{{$sale->id}}",
                method: "GET",
                success: function(msg) {
                    if(msg == "Out of Balance")
                    {
                        Toastify({
                        text: "Out of Balance",
                        className: "info",
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "linear-gradient(to right, #FF5733, #E70000)",
                        }
                        }).showToast();
                    }
                    else
                    {
                        const message = encodeURIComponent(
                        `*Nafis Carpets and Rugs*\n\nHi, please check out the Sale Invoice. You can save it as PDF from the link below.\n\n` +
                        `{{url('/pdf/')}}/` + msg
                            );
                    const whatsappURL = `https://web.whatsapp.com/send?phone={{$sale->customer->contact}}&text=${message}`;
                    window.open(whatsappURL, '_self');
                    }

                }
       });
      }

    </script>
@endsection

