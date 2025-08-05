@extends('layouts.layout')
@section('content')
    @php
        $categories = \App\Models\Category::where('parent_id', 0)->pluck('name', 'id')->toArray();
$categoriesData = [];
$valuesData = [];
foreach ($categories as $categoryId => $categoryName) {
    $categoriesData[] = $categoryName;
    $productCount = \App\Models\Products::where('category_id', $categoryId)->count();
    $valuesData[] = $productCount;
}
$categories = $categoriesData;
$values = $valuesData;
    @endphp
    @php
        $dates = \App\Helpers\CustomHelper::getDates(15);
    @endphp
    <div class="content">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col-lg-12 col-md-12">
                <div class="card widget">
                    <div class="card-header">
                        <h5 class="card-title">Activity Overview</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                    <h5 class="my-3">Total User</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 100%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_user??0}}</h3>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-receipt text-warning"></i>
                                    </div>
                                    <h5 class="my-3">Orders</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_order??0}}</h3>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-cart text-info"></i>
                                    </div>
                                    <h5 class="my-3">Delivery Boys</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_delivery_boy??0}} </h3>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-collection text-success"></i>
                                    </div>
                                    <h5 class="my-3">Products</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_product??0}}</h3>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12">
                <div class="card widget h-100">
                    <div class="card-header d-flex">
                        <h6 class="card-title">
                            Sales Chart
                            <a href="#" class="bi bi-question-circle ms-1 small" data-bs-toggle="tooltip"
                               title="Daily orders and sales"></a>
                        </h6>
                        <div class="d-flex gap-3 align-items-center ms-auto">
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-sm" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-md-flex align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="display-7 me-3">
                                    <i class="bi bi-bag-check me-2 text-success"></i> ₹ {{$total_sales??0}}
                                </div>
                            </div>
                        </div>
                        <div id="sales-chart-new"></div>
                        <div class="d-flex justify-content-center gap-4 align-items-center ms-auto mt-3 mt-lg-0">
                            <div>
                                <i class="bi bi-circle-fill mr-2 text-primary me-1 small"></i>
                                <span>Sales</span>
                            </div>
                            <div>
                                <i class="bi bi-circle-fill mr-2 text-success me-1 small"></i>
                                <span>Order</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="card widget h-100">
                    <div class="card-header d-flex">
                        <h6 class="card-title">
                            Category Wise Product's
                            <a href="#" class="bi bi-question-circle ms-1 small" data-bs-toggle="tooltip"
                               title="Channels where your products are sold"></a>
                        </h6>
                        <div class="d-flex gap-3 align-items-center ms-auto">
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-sm" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="category-product-channels"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="display-7">
                                <i class="bi bi-basket"></i>
                            </div>
                            <div class="dropdown ms-auto">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-sm" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                </div>
                            </div>
                        </div>
                        <h4 class="mb-3">Orders</h4>
                        <div class="d-flex mb-3">
                            <div class="display-7">310</div>
                            <div class="ms-auto" id="total-orders"></div>
                        </div>
                        <div class="text-success">
                            Over last month 1.4% <i class="small bi bi-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="display-7">
                                <i class="bi bi-credit-card-2-front"></i>
                            </div>
                            <div class="dropdown ms-auto">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-sm" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                </div>
                            </div>
                        </div>
                        <h4 class="mb-3">Sales</h4>
                        <div class="d-flex mb-3">
                            <div class="display-7">$3.759,00</div>
                            <div class="ms-auto" id="total-sales"></div>
                        </div>
                        <div class="text-danger">
                            Over last month 2.4% <i class="small bi bi-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h6 class="card-title">Recent Reviews</h6>
                            <div class="dropdown ms-auto">
                                <a href="#">View All</a>
                            </div>
                        </div>
                        <div class="summary-cards">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar me-3">
                                        <img src="{{url('public')}}/assets/images/user/women_avatar5.jpg"
                                             class="rounded-circle" alt="image">
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Amara Keel</h5>
                                        <ul class="list-inline ms-auto mb-0">
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-muted"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">(4)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div>I love your products. It is very easy and fun to use this panel.</div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar me-3">
                                        <span class="avatar-text bg-indigo rounded-circle">J</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Johnath Siddeley</h5>
                                        <ul class="list-inline ms-auto mb-0">
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">(5)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div>Very nice glasses. I ordered for my friend.</div>
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar me-3">
                                        <span class="avatar-text bg-yellow rounded-circle">D</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">David Berks</h5>
                                        <ul class="list-inline ms-auto mb-0">
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </li>
                                            <li class="list-inline-item mb-0">(5)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div>I am very satisfied with this product.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-4">
                            <h6 class="card-title mb-0">Customer Rating</h6>
                            <div class="dropdown ms-auto">
                                <a href="#" data-bs-toggle="dropdown" class="btn btn-sm" aria-haspopup="true"
                                   aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="display-6">3.0</div>
                            <div class="d-flex justify-content-center gap-3 my-3">
                                <i class="bi bi-star-fill icon-lg text-warning"></i>
                                <i class="bi bi-star-fill icon-lg text-warning"></i>
                                <i class="bi bi-star-fill icon-lg text-warning"></i>
                                <i class="bi bi-star-fill icon-lg text-muted"></i>
                                <i class="bi bi-star-fill icon-lg text-muted"></i>
                                <span>(318)</span>
                            </div>
                        </div>
                        <div class="text-muted d-flex align-items-center justify-content-center">
                        <span class="text-success me-3 d-block">
                            <i class="bi bi-arrow-up me-1 small"></i>+35
                        </span> Point from last month
                        </div>
                        <div class="row my-4">
                            <div class="col-md-6 m-auto">
                                <div id="customer-rating"></div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-outline-primary btn-icon">
                                <i class="bi bi-download"></i> Download Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 bg-purple">
                    <div class="card-body text-center">
                        <div class="text-white-50">
                            <div class="bi bi-box-seam display-6 mb-3"></div>
                            <div class="display-8 mb-2">Products Sold</div>
                            <h5>89 Sold</h5>
                        </div>
                        <div id="products-sold"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card widget h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            Your Top Countries
                            <a href="#" class="bi bi-question-circle ms-1 small" data-bs-toggle="tooltip"
                               title="Sales performance revenue based by country"></a>
                        </h5>
                        <a href="#">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex flex-grow-1 align-items-center">
                                    <img width="45" class="me-3"
                                         src="{{url('public')}}/assets/flags/united-states-of-america.svg" alt="...">
                                    <span>United States</span>
                                </div>
                                <span>$1.671,10</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex flex-grow-1 align-items-center">
                                    <img width="45" class="me-3" src="{{url('public')}}/assets/flags/venezuela.svg"
                                         alt="...">
                                    <span>Venezuela</span>
                                </div>
                                <span>$1.064,75</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex flex-grow-1 align-items-center">
                                    <img width="45" class="me-3" src="{{url('public')}}/assets/flags/salvador.svg"
                                         alt="...">
                                    <span>Salvador</span>
                                </div>
                                <span>$1.055,98</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex flex-grow-1 align-items-center">
                                    <img width="45" class="me-3" src="{{url('public')}}/assets/flags/russia.svg"
                                         alt="...">
                                    <span>Russia</span>
                                </div>
                                <span>$1.042,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="card widget">
                    <div class="card-header">
                        <h5 class="card-title">Activity Overview</h5>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-truck text-secondary"></i>
                                    </div>
                                    <h5 class="my-3">Delivered</h5>
                                    <div class="text-muted">15 New Packages</div>
                                    <div class="progress mt-3" style="height: 5px">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 25%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-receipt text-warning"></i>
                                    </div>
                                    <h5 class="my-3">Ordered</h5>
                                    <div class="text-muted">72 New Items</div>
                                    <div class="progress mt-3" style="height: 5px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 67%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-bar-chart text-info"></i>
                                    </div>
                                    <h5 class="my-3">Reported</h5>
                                    <div class="text-muted">50 Support New Cases</div>
                                    <div class="progress mt-3" style="height: 5px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-cursor text-success"></i>
                                    </div>
                                    <h5 class="my-3">Arrived</h5>
                                    <div class="text-muted">34 Upgraded Boxed</div>
                                    <div class="progress mt-3" style="height: 5px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 55%"
                                             aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12">
                <div class="card widget">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title">Recent Products</h5>
                        <div class="dropdown ms-auto">
                            <a href="#" data-bs-toggle="dropdown" class="btn btn-sm btn-floating" aria-haspopup="true"
                               aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">Action</a>
                                <a href="#" class="dropdown-item">Another action</a>
                                <a href="#" class="dropdown-item">Something else here</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Products added today. Click <a href="#">here</a> for more details</p>
                        <div class="table-responsive">
                            <table class="table table-custom mb-0" id="recent-products">
                                <thead>
                                <tr>
                                    <th>
                                        <input class="form-check-input select-all" type="checkbox"
                                               data-select-all-target="#recent-products" id="defaultCheck1">
                                    </th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox">
                                    </td>
                                    <td>
                                        <a href="#">
                                            <img src="{{url('public')}}/assets/images/products/10.jpg" class="rounded"
                                                 width="40" alt="...">
                                        </a>
                                    </td>
                                    <td>Cookie</td>
                                    <td>
                                        <span class="text-danger">Out of Stock</span>
                                    </td>
                                    <td>$10,50</td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item">Action</a>
                                                    <a href="#" class="dropdown-item">Another action</a>
                                                    <a href="#" class="dropdown-item">Something else here</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox">
                                    </td>
                                    <td>
                                        <a href="#">
                                            <img src="{{url('public')}}/assets/images/products/7.jpg" class="rounded"
                                                 width="40" alt="...">
                                        </a>
                                    </td>
                                    <td>Glass</td>
                                    <td>
                                        <span class="text-success">In Stock</span>
                                    </td>
                                    <td>$70,20</td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item">Action</a>
                                                    <a href="#" class="dropdown-item">Another action</a>
                                                    <a href="#" class="dropdown-item">Something else here</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox">
                                    </td>
                                    <td>
                                        <a href="#">
                                            <img src="{{url('public')}}/assets/images/products/8.jpg" class="rounded"
                                                 width="40" alt="...">
                                        </a>
                                    </td>
                                    <td>Headphone</td>
                                    <td>
                                        <span class="text-success">In Stock</span>
                                    </td>
                                    <td>$870,50</td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item">Action</a>
                                                    <a href="#" class="dropdown-item">Another action</a>
                                                    <a href="#" class="dropdown-item">Something else here</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input" type="checkbox">
                                    </td>
                                    <td>
                                        <a href="#">
                                            <img src="{{url('public')}}/assets/images/products/9.jpg" class="rounded"
                                                 width="40" alt="...">
                                        </a>
                                    </td>
                                    <td>Perfume</td>
                                    <td>
                                        <span class="text-success">In Stock</span>
                                    </td>
                                    <td>$170,50</td>
                                    <td class="text-end">
                                        <div class="d-flex">
                                            <div class="dropdown ms-auto">
                                                <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" class="dropdown-item">Action</a>
                                                    <a href="#" class="dropdown-item">Another action</a>
                                                    <a href="#" class="dropdown-item">Something else here</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

    <script>
        $(document).ready(function () {

            // salesChannels();
        });

        // category-product-channels

        function salesChannels() {
            var categories = @json($categories);
            var values = @json($values);

            var options = {
                series: values,
                chart: {
                    width: 400,
                    type: 'donut',
                    height: '600px',  // Adjust height if needed
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270
                    }
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'gradient',
                },
                legend: {
                    position: 'bottom', // Position legend at the bottom
                    horizontalAlign: 'center', // Align horizontally at the center
                    floating: false, // Disable floating legend
                    margin: 10, // Space between chart and legend
                    formatter: function (val, opts) {
                        let index = opts.seriesIndex;
                        let category = categories[index];
                        let value = values[index];
                        let level = value > 50 ? 'High' : value > 20 ? 'Medium' : 'Low';
                        return category + " - " + value;
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value, opts) {
                            let index = opts.seriesIndex;
                            let category = categories[index];
                            let level = value > 50 ? 'High' : value > 20 ? 'Medium' : 'Low';
                            return category + ": " + value;
                        }
                    }
                },
                title: {
                    text: ''
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#category-product-channels"), options);
            chart.render();
        }

    </script>
    <script>

        const options = {
            series: [
                {
                    name: "Sales",
                    data: [
                        @foreach($dates as $date)
                            @php
                                $order_amount = \App\Models\Order::where('delivery_date',$date)->where('status','COMPLETED')->sum('total_amount');
                            @endphp
                            {{$order_amount}},
                        @endforeach
                    ]
                },
                {
                    name: 'Orders',
                    data: [
                        @foreach($dates as $date)
                            @php
                                $order_amount = \App\Models\Order::where('delivery_date',$date)->count();
                            @endphp
                            {{$order_amount}},
                        @endforeach
                    ]
                }
            ],
            theme: {
                mode: $('body').hasClass('dark') ? 'dark' : 'light',
            },
            chart: {
                height: 500,
                type: 'line',
                foreColor: '#c9c7c7',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#ff6e40', '#05b171'],
            stroke: {
                width: 4,
                curve: 'smooth',
            },
            legend: {
                show: false
            },
            markers: {
                size: 0,
                hover: {
                    sizeOffset: 6
                }
            },
            xaxis: {
                categories: [
                    @foreach($dates as $date)
                        '{{date('d M',strtotime($date))}}',
                    @endforeach
                ],
            },
            tooltip: {
                y: [
                    {
                        title: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    },
                    {
                        title: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    },
                    {
                        title: {
                            formatter: function (val) {
                                return val;
                            }
                        }
                    }
                ]
            },
            grid: {
                borderColor: '#ededed',
            }
        };

        var chart = new ApexCharts(document.querySelector("#sales-chart-new"), options);
        chart.render();
    </script>
@endsection
