@extends('layout.popups')
@section('content')
<script>
    var existingProducts = [];
    @foreach ($sale->details as $product)
        @php
            $productID = $product->productID;
        @endphp
        existingProducts.push({{$productID}});
    @endforeach
</script>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Edit Sale </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('sale.update', $sale->id) }}" method="post">
                        @csrf
                        @method("PUT")
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value="0"></option>
                                        @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->code }} | {{ $product->color }} | {{ $product->desc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="20%">Item</th>
                                        <th width="10%" class="text-center">Warehouse</th>
                                        <th class="text-center">Width X Length</th>
                                        <th class="text-center">Size (SQM)</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Amount</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($sale->details as $product)
                                        @php
                                            $id = $product->product->id;
                                        @endphp
                                        <tr id="row_{{$id}}">
                                        <td class="no-padding">{{$product->product->code}} | {{$product->product->color}} | {{$product->product->desc}} | {{$product->product->cat}}</td>
                                        <td class="no-padding">
                                            <select name="warehouse[]" class="form-control text-center no-padding" id="warehouse_{{$id}}">
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{$warehouse->id}}" @selected($warehouse->id == $product->warehouseID) >{{$warehouse->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="no-padding"><div class="input-group"> <input type="number" name="width[]" oninput="updateChanges({{$id}})" class="form-control no-padding text-center sizes_{{$id}}" value="{{$product->width}}" id="width_{{$id}}" min="0"  placeholder="Width"> <input type="number" name="length[]" value="{{$product->length}}" id="length_{{$id}}" oninput="updateChanges({{$id}})" class="form-control no-padding text-center sizes_{{$id}}" min="1" placeholder="Length"></div></td>
                                        <td class="no-padding"><input type="number" name="size[]" required step="any" value="{{$product->size}}" readonly min="1" class="form-control text-center no-padding" id="size_{{$id}}"></td>
                                        <td class="no-padding"><input type="number" name="price[]" oninput="updateChanges({{$id}})" required step="any" value="{{$product->price}}" min="1" class="form-control text-center no-padding" id="price_{{$id}}"></td>
                                        <td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges({{$id}})" min="1" required value="{{$product->qty}}" class="form-control text-center no-padding" id="qty_{{$id}}"></td>
                                        <td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="{{$product->amount}}" class="form-control text-center no-padding" id="amount_{{$id}}"></td>
                                        <td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow({{$id}})">X</span> </td>
                                        <input type="hidden" name="id[]" value="{{$id}}">
                                        <input type="hidden" id="cat_{{$id}}" value="{{$product->product->cat}}">
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" class="text-end">Total</th>

                                            <th class="text-end" id="totalAmount">0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-3">

                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="number" name="discount" oninput="updateTotal()" value="{{$sale->discount}}" min="0" id="discount" value="0"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="dc">Delivery Charges</label>
                                    <input type="number" oninput="updateTotal()" name="dc" value="{{$sale->dc}}" min="0" id="dc" value="0"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="total">Total Bill</label>
                                    <input type="number" name="total" min="0" id="total" value="{{$sale->net}}" readonly value="0"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d', strtotime($sale->date)) }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customerID" id="customer" class="selectize1">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected($customer->id == $sale->customerID)>{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="account">Account</label>
                                    <select name="accountID" id="account" class="selectize1">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="status">Payment Status</label>
                                    <select name="status" id="status" class="selectize1">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5">{{$sale->notes}}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Update Sale</button>
                            </div>
                </div>
            </form>
            </div>

        </div>
        <!--end card-->
    </div>
    <!--end col-->
    </div>
    <!--end row-->
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
    <style>
        .no-padding {
            padding: 5px 5px !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(".selectize1").selectize();
        $(".selectize").selectize({
            onChange: function(value) {
                if (!value.length) return;
                if (value != 0) {
                    getSingleProduct(value);
                    this.clear();
                    this.focus();
                }

            },
        });
        var warehouses = @json($warehouses);

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('sales/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {

                    } else {
                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.code + ' | ' + product.color + ' | ' + product.desc + ' | ' + product.cat +'</td>';
                        html += '<td class="no-padding"><select name="warehouse[]" class="form-control text-center no-padding" id="warehouse_' + id + '">';
                            warehouses.forEach(function(warehouse) {
                                html += '<option value="' + warehouse.id + '" >' + warehouse.name + '</option>';
                            });
                        html += '<td class="no-padding"><div class="input-group"> <input type="number" name="width[]" oninput="updateChanges(' + id + ')" class="form-control no-padding text-center sizes_'+id+'" value="'+product.width+'" id="width_'+ id +'" min="0"  placeholder="Width"> <input type="number" name="length[]" value="'+product.length+'" id="length_'+ id +'" oninput="updateChanges(' + id + ')" class="form-control no-padding text-center sizes_'+id+'" min="1" placeholder="Length"></div></td>';
                        html += '<td class="no-padding"><input type="number" name="size[]" required step="any" value="" readonly min="1" class="form-control text-center no-padding" id="size_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.price+'" min="1" class="form-control text-center no-padding" id="price_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' + id + ')" min="1" required value="1" class="form-control text-center no-padding" id="qty_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' + id + '"></td>';
                        html += '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" id="cat_'+ id +'" value="' + product.cat + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        existingProducts.push(id);
                        updateChanges(id);
                    }
                }
            });
        }

        function updateChanges(id) {

            var qty = parseFloat($('#qty_' + id).val());
            var price = parseFloat($('#price_' + id).val());
            var width = parseFloat($('#width_' + id).val());
            var length = parseFloat($('#length_' + id).val());
            var cat = $('#cat_' + id).val();

            var size = width * length;

            $("#size_" + id).val(size.toFixed(2));

            if(cat == "Kaleen")
            {
                var amount = price * qty;
                $(".sizes_"+id).prop('readonly', 'true');
            }
            else
            {
                var amount = size * qty * price;
                $(".sizes_"+id).removeAttr('readonly');
            }
            $("#amount_" + id).val(amount.toFixed(2));
            updateTotal();
            }
            updateTotal();
            function updateTotal() {
            var total = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                total += parseFloat(inputValue);
            });

            $("#totalAmount").html(total.toFixed(2));

            var discount = parseFloat($("#discount").val());
            var dc = parseFloat($("#dc").val());

            $("#total").val(total - discount + dc);

        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_'+id).remove();
            updateTotal();
        }





    </script>
@endsection
