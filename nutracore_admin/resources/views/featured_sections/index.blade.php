@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

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
                    <li class="breadcrumb-item active" aria-current="page">Featured Section</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Featured Section</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('featured_section.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Seller</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th>Categories</th>
                            <th>Products</th>
                            <th>Style</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($featured_sections)){
                            $i = 1;
                        foreach ($featured_sections as $section) {
                            $image = $section->image ?? '';
                            $images = explode(",", $image);
                            ?>
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{ \App\Helpers\CustomHelper::getVendorName($section->vendor_id??'') }}</td>
                            <td>{{$section->title??''}}</td>
                            <td>
                                    <?php if (!empty($images)) {
                                foreach ($images as $ima => $value) {
                                    $image = \App\Helpers\CustomHelper::getImageUrl('featured_section', $value);
                                    ?>
                                <a href="{{$image}}" target="_blank"><img src="{{$image}}" height="50px"
                                                                          width="50px"></a>
                                <?php }
                                } ?>
                            </td>
                            <td>{{$section->priority??''}}</td>
                            <td>{{$section->type??''}}</td>
                            <td>
                                    <?php if (!empty($section->category_ids)) {
                                    $category_ids = explode(",", $section->category_ids);
                                    foreach ($category_ids as $key => $cat) {
                                    $category = \App\Helpers\CustomHelper::getCategoryDetails($cat);
                                    ?>
                                {{$key+1}}. {{$category->name??''}}<br>
                                <?php }
                                } ?>


                            </td>
                            <td>
                                    <?php if (!empty($section->product_ids)) {
                                    $product_ids = explode(",", $section->product_ids);
                                foreach ($product_ids as $key => $cat) {
                                    $category = \App\Helpers\CustomHelper::getProductDeatils($cat);
                                    ?>
                                {{$key+1}}. {{$category->name??''}}<br>
                                <?php }
                                } ?>
                            </td>
                            <td>{{$section->style??''}}</td>

                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($section->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('featured_section.edit',$section->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('featured_section.delete',$section->id.'?back_url='.$BackUrl)}}"
                                               onclick="return confirm('Are you Want To Delete?')"
                                               class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        } ?>

                        </tbody>
                    </table>

                    {{ $featured_sections->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
