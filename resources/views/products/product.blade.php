@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Products</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Image</th>
                            <th>Desc</th>
                            <th>Width</th>
                            <th>Length</th>
                            <th>Size</th>
                            <th>Sale Price</th>
                            <th>Category</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><img src="{{asset($item->image)}}" style="width:100px; height:100px;" alt=""></td>
                                    <td>{{ $item->code }} <br> {{ $item->color }} <br> {{ $item->desc }} </td>
                                    <td>{{ $item->width }} Mtr</td>
                                    <td>{{ $item->length }} Mtr</td>
                                    <td>{{ number_format(getSize($item->width, $item->length),2) }} SQM</td>
                                    <td>{{ number_format($item->price,2) }}</td>
                                    <td>{{ $item->cat }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info " data-bs-toggle="modal"
                                            data-bs-target="#edit_{{ $item->id }}">Edit</button>
                                    </td>
                                </tr>
                                <div id="edit_{{ $item->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Edit - Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('product.update', $item->id) }}" enctype="multipart/form-data" method="post">
                                                @csrf
                                                @method('patch')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-6">
                                                             <div class="form-group">
                                                                <label for="code">Code</label>
                                                                <input type="text" name="code" value="{{$item->code}}" required id="code" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="color">Color</label>
                                                                <input type="text" name="color" value="{{$item->color}}" required id="color" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="desc">Desc</label>
                                                        <input type="text" name="desc" id="desc" value="{{$item->desc}}" class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="length">Size</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="width" value="{{$item->width}}" min="1" placeholder="Width" aria-label="Width">
                                                            <span class="input-group-text">Width</span>
                                                            <input type="number" class="form-control" name="length" value="{{$item->length}}" min="1" placeholder="Length" aria-label="Length">
                                                            <span class="input-group-text">Length</span>
                                                          </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group mt-2">
                                                                <label for="price">Sale Price</label>
                                                                <input type="number" step="any" name="price" required value="{{$item->price}}" min="0" id="price" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group mt-2">
                                                                <label for="cat">Category</label>
                                                                <select name="cat" id="cat" class="form-control">
                                                                    <option value="Carpet" @selected($item->cat == "Carpet")>Carpet</option>
                                                                    <option value="Kaleen" @selected($item->cat == "Kaleen")>Kaleen</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-6">
                                                            <div class="form-group mt-2">
                                                             <label for="image">Image</label>
                                                             <input type="file" class="form-control" name="image">
                                                            </div>
                                                         </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Default Modals -->

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('product.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                 <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" name="code" required id="code" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="text" name="color" required id="color" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="desc">Desc</label>
                            <input type="text" name="desc" id="desc" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="length">Size</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" name="Width" min="1" placeholder="Width" aria-label="Width">
                                <span class="input-group-text">Width</span>
                                <input type="number" class="form-control" name="length" min="1" placeholder="Length" aria-label="Length">
                                <span class="input-group-text">Length</span>

                              </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mt-2">
                                    <label for="price">Sale Price</label>
                                    <input type="number" step="any" name="price" required value="" min="0" id="price" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mt-2">
                                    <label for="cat">Category</label>
                                    <select name="cat" id="cat" class="form-control">
                                        <option value="Carpet">Carpet</option>
                                        <option value="Kaleen">Kaleen</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-6">
                               <div class="form-group mt-2">
                                <label for="image">Image</label>
                                <input type="file" class="form-control" name="image">
                               </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
@endsection
