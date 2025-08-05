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
                    <li class="breadcrumb-item active" aria-current="page">App Settings</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">App Settings</div>

                            <div class="dropdown ms-auto">
                                <a href="{{ route('app_settings.add', ['back_url' => $BackUrl]) }}"
                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Products</th>
                            <th>Categories</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($app_settings)){
                        foreach ($app_settings as $app_setting) {
                            $image = \App\Helpers\CustomHelper::getImageUrl('app_setting', $app_setting->image);
                            $category_ids = $app_setting->category_ids ?? '';
                            $category_ids = explode(",", $category_ids);
                            $product_ids = $app_setting->product_ids ?? '';
                            $product_ids = explode(",", $product_ids);
                            ?>
                        <tr>
                            <td>{{ $app_setting->title ?? '' }}</td>
                            <td>{{ $app_setting->type ?? '' }}</td>
                            <td>
                                @if(!empty($product_ids))
                                    @foreach($product_ids as $key => $value)
                                        @php
                                            $product_name = \App\Helpers\CustomHelper::getProductName($value);
                                        @endphp
                                        <li>{{$product_name??''}}</li>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if(!empty($category_ids))
                                    @foreach($category_ids as $key => $value)
                                        @php
                                            $category_name = \App\Helpers\CustomHelper::getCategoryName($value);
                                        @endphp
                                        <li>{{$category_name??''}}</li>
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $app_setting->priority ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getStatusStr($app_setting->status) }}</td>
                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('app_settings.edit',$app_setting->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">Edit</a>
                                            <a href="{{route('app_settings.delete',$app_setting->id.'?back_url='.$BackUrl)}}"
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
                    {{ $app_settings->appends(request()->input())->links('pagination') }}
                </div>
            </div>
        </div>
    </div>

@endsection
