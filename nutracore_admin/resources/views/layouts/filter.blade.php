@php
    $search = $_GET['search'] ?? '';
    $category_id = $_GET['category_id'] ?? '';
    $subcategory_id = $_GET['subcategory_id'] ?? '';
    $folder_name = $_GET['folder_name'] ?? '';
    $vendor_id = $_GET['vendor_id'] ?? '';
    $date = $_GET['date'] ?? '';
    $agent_id = $_GET['agent_id'] ?? '';

    $current_url = url()->current();

    $categories = \App\Helpers\CustomHelper::getCategories();
    $vendors = \App\Helpers\CustomHelper::getVendors();

    $subcategories = [];
    if (!empty($category_id)) {
        $subcategories = \App\Helpers\CustomHelper::getSubCategory($category_id);
    }

    $agents = \App\Helpers\CustomHelper::getAgents();

    $is_export = $is_export ?? '';
    $is_import = $is_import ?? '';
@endphp

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <form action="" method="get">
                <div class="card-body">
                    <h5>Filter</h5>
                    <div class="row">

                        @if(!empty($search_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" placeholder="Search..."
                                    value="{{ $search }}">
                            </div>
                        @endif

                        @if(!empty($categories_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="" selected>Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($subcategory_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">SubCategory</label>
                                <select class="form-control" name="subcategory_id" id="subcategory_id">
                                    <option value="" selected>Select SubCategory</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" {{ $subcategory->id == $subcategory_id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($vendor_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Vendor</label>
                                <select class="form-control" name="vendor_id">
                                    <option value="" selected>Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $vendor->id == $vendor_id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($folder_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Folder</label>
                                <select class="form-control" name="folder_name">
                                    <option value="" selected>Select Folder</option>
                                    @foreach($folders as $folder)
                                        <option value="{{ $folder }}" {{ $folder == $folder_name ? 'selected' : '' }}>
                                            {{ ucfirst(basename($folder)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if(!empty($date_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" value="{{ $date }}">
                            </div>
                        @endif

                        @if(!empty($delivery_agents_show))
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Choose Delivery Agent</label>
                                <select class="form-control" name="agent_id">
                                    <option value="" selected>Select Delivery Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ $agent_id == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary">Search</button>
                            <a href="{{ $current_url }}" class="btn btn-danger">Reset</a>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($is_export == 1)
                                <a href="{{ $export_url }}" class="btn btn-warning">Export</a>
                            @endif
                            @if($is_import == 1)
                                <a data-bs-toggle="modal" data-bs-target="#import_modal" class="btn btn-success">Import</a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@if($is_import == 1)
    <!-- Import Modal -->
    <div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="importLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ $import_url }}" method="post" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importLabel">Import</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="file" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endif