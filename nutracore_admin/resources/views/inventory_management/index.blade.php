@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $vendor_id = $_GET['vendor_id'] ?? 0;

    $vendors = \App\Helpers\CustomHelper::getVendors();
            ?>


    <div class="content ">

        <div class="mb-4">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <i class="bi bi-globe2 small me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Inventory</li>
                </ol>
            </nav>
        </div>
    @include('layouts.filter',['categories_show'=>'categories_show','subcategory_show'=>'subcategory_show','vendor_show'=>'vendor_show','is_export'=>'1','export_url'=>route('export.stock_data',$_GET),'is_import'=>'1','import_url'=>route('export.stock_data_import')])


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Inventory</div>

                            <div class="dropdown ms-auto">
                              
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                            <tr class="text-center">
                                <th>Category</th>
                                <th>SubCategory</th>
                                <th>Product Name</th>
                                <th>Varient</th>
                                <th>Image</th>
                                <th>Stock Available</th>
                                <th>Action</th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php if (!empty($products)) {
        foreach ($products as $product) {
            $image = \App\Helpers\CustomHelper::getImageUrl('products', $product->image);
            $varients = \App\Helpers\CustomHelper::getProductVarients($product->id ?? '');
            if (!empty($varients)) {
                foreach ($varients as $varient) {
                    $stock_avail = \App\Helpers\CustomHelper::getNoOfStock($product->id, $varient->id, $vendor_id);
                                            ?>
                            <tr class="text-center">
                                <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->category_id ?? '') ?? '' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getCategoryName($product->subcategory_id ?? '') ?? '' }}</td>
                                <td>{{$product->name ?? ''}}</td>
                                <td>
                                    {{$varient->unit ?? ''}} 
                                </td>

                                <td><a href="{{$image}}" target="_blank"><img height="50px" width="50px" src="{{$image}}"
                                            alt="" /></a>
                                </td>
                                {{-- <td>{{ CustomHelper::getVendorName($product->vendor_id) }}</td>--}}
                                <td> {{$stock_avail}}</td>
                                <td>
                                    <a title="Stock Out"
                                        onclick="open_modal('stockOutModal','{{$product->id}}','{{$varient->id}}','{{$product->name}}')"
                                        class="btn btn-danger"><i class="fa fa-arrow-up"></i></a>
                                    <a title="Stock In"
                                        onclick="open_modal('stockInModal','{{$product->id}}','{{$varient->id}}','{{$product->name}}')"
                                        class="btn btn-primary"><i class="fa fa-arrow-down"></i></a>


                                    <a title="Stock Transfer"
                                        onclick="open_modal('stockTransferModal','{{$product->id}}','{{$varient->id}}','{{$product->name}}')"
                                        class="btn btn-warning"><i class="fa fa-arrow-right"></i></a>

                                </td>
                            </tr>


                            <?php            }
            }
        }
    }
                                        ?>
                        </tbody>
                    </table>
                    {{ $products->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>















    <div class="modal fade" id="stockInModal"
         role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Stock In - <span id="product_name"></span></h4>
                </div>
                <form action="{{ route('inventory_management.stock_in') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="product_id" id="stock_in_product_id">
                            <input type="hidden" name="varient_id" id="stock_in_varient_id">
                            <div class="col-md-12">
                                <label>Choose Vendor</label>
                                <select class="form-control" name="vendor_id">
                                    <option value="0">Warehouse</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{$vendor->id??''}}">{{$vendor->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>No of Stock</label>
                                <input type="text" class="form-control"
                                       name="no_of_stock" value=""
                                       placeholder="Enter No of Stock">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Remarks</label>
                                <input type="text" class="form-control"
                                       name="remarks" value=""
                                       placeholder="Enter remarks">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer px-4">
                        <button type="button" class="btn btn-secondary btn-pill"
                                data-bs-dismiss="modal">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-pill">Update
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <div class="modal fade" id="stockOutModal"
         role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Stock Out - <span id="product_name_stock_out"></span></h4>
                </div>
                <form action="{{ route('inventory_management.stock_out') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="product_id" id="stock_out_product_id">
                            <input type="hidden" name="varient_id" id="stock_out_varient_id">
                            <div class="col-md-12">
                                <label>Choose Vendor</label>
                                <select class="form-control" name="vendor_id">
                                    <option value="0">Warehouse</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{$vendor->id??''}}">{{$vendor->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>No of Stock</label>
                                <input type="text" class="form-control"
                                       name="no_of_stock" value=""
                                       placeholder="Enter No of Stock">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Remarks</label>
                                <input type="text" class="form-control"
                                       name="remarks" value=""
                                       placeholder="Enter remarks">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer px-4">
                        <button type="button" class="btn btn-secondary btn-pill"
                                data-bs-dismiss="modal">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-pill">Update
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <div class="modal fade" id="stockTransferModal"
         role="dialog" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Stock Transfer - <span id="product_name_stock_transfer"></span></h4>
                </div>
                <form action="{{ route('inventory_management.stock_transfer') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="product_id" id="stock_transfer_product_id">
                            <input type="hidden" name="varient_id" id="stock_transfer_varient_id">
                            <div class="col-md-12 mt-3">
                                <label>Choose From Vendor</label>
                                <select class="form-control" name="from_vendor_id">
                                    <option value="0">Warehouse</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{$vendor->id??''}}">{{$vendor->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Choose To Vendor</label>
                                <select class="form-control" name="to_vendor_id">
                                    <option value="0">Warehouse</option>
                                    @foreach($vendors as $vendor)
                                        <option
                                            value="{{$vendor->id??''}}">{{$vendor->name??''}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>No of Stock</label>
                                <input type="text" class="form-control"
                                       name="no_of_stock" value=""
                                       placeholder="Enter No of Stock">
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Remarks</label>
                                <input type="text" class="form-control"
                                       name="remarks" value=""
                                       placeholder="Enter remarks">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer px-4">
                        <button type="button" class="btn btn-secondary btn-pill"
                                data-bs-dismiss="modal">Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-pill">Update
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>



    <script>
        function open_modal(modal, product_id, varient_id, product_name) {
            $("#product_name").html(product_name);
            $("#product_name_stock_out").html(product_name);
            $("#product_name_stock_transfer").html(product_name);

            $("#stock_in_product_id").val(product_id);
            $("#stock_in_varient_id").val(varient_id);
            $("#stock_out_product_id").val(product_id);
            $("#stock_out_varient_id").val(varient_id);

            $("#stock_transfer_product_id").val(product_id);
            $("#stock_transfer_varient_id").val(varient_id);

            $(`#${modal}`).modal('show');
        }
    </script>


@endsection