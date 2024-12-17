@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9">
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
                                <div class="col-4 text-center"><h2>ACCOUNT STATEMENT</h2></div>
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Account Title</p>
                                        <h5 class="fs-14 mb-0">{{ $account->title }}</h5>
                                        <h5 class="fs-14 mb-0">{{ $account->type }}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Dates</p>
                                        <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">From </small><span id="invoice-date">{{ date("d M Y", strtotime($from)) }}</span> </h5>
                                        <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small><span id="invoice-date">{{ date("d M Y", strtotime($to)) }}</span> </h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Balance</p>
                                        <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">Current &nbsp;</small><span id="invoice-date">Rs. {{ number_format($cur_balance) }}</span> </h5>
                                        <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">Previous </small><span id="invoice-date">Rs. {{ number_format($pre_balance) }}</span> </h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">{{ date("d M Y") }}</span></h5>
                                        {{-- <h5 class="fs-14 mb-0"><span id="total-amount">{{ \Carbon\Carbon::now()->format('h:i A') }}</span></h5> --}}
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" style="width: 50px;">Ref#</th>
                                                <th scope="col">Date</th>
                                                <th scope="col" class="text-start">Notes</th>
                                                <th scope="col" class="text-end">Credit</th>
                                                <th scope="col" class="text-end">Debit</th>
                                                <th scope="col" class="text-end">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                            @php
                                                $balance = $pre_balance;
                                            @endphp
                                        @foreach ($transactions as $key => $trans)
                                        @php
                                            $balance += $trans->cr;
                                            $balance -= $trans->db;
                                        @endphp
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $trans->refID }}</td>
                                                <td>{{ date('d M Y', strtotime($trans->date)) }}</td>
                                                <td class="text-start">{{ $trans->notes }}</td>
                                                <td class="text-end">{{ number_format($trans->cr) }}</td>
                                                <td class="text-end">{{ number_format($trans->db) }}</td>
                                                <td class="text-end">{{ number_format($balance) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table><!--end table-->
                                </div>

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
                  url: "{{ url('statement/share/') }}/{{$account->id}}/{{$from}}/{{$to}}",
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
                        `*Nafis Carpets and Rugs*\n\nHi, please check out your account statment from {{$from}} to {{$to}}. You can save it as PDF from the link below.\n\n` +
                        `{{url('/pdf/')}}/` + msg
                            );
                    const whatsappURL = `https://web.whatsapp.com/send?phone={{$account->contact}}&text=${message}`;
                    window.open(whatsappURL, '_self');
                    }
                  }
         });
        }

      </script>
@endsection

