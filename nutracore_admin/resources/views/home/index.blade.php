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
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                    <h5 class="my-3">Active Orders</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-secondary" role="progressbar"
                                             style="width: 100%"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_user??0}}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-receipt text-warning"></i>
                                    </div>
                                    <h5 class="my-3">Pending Dispatch</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: 100%"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_order??0}}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-cart text-info"></i>
                                    </div>
                                    <h5 class="my-3">Express Orders Pending</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_delivery_boy??0}}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-collection text-success"></i>
                                    </div>
                                    <h5 class="my-3">Express Orders Delivered</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: 100%"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_product??0}}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <div class="display-5">
                                        <i class="bi bi-collection text-success"></i>
                                    </div>
                                    <h5 class="my-3">Return Request Pending</h5>
                                    <div class="progress mt-2 mb-2" style="height: 2px">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: 100%"></div>
                                    </div>
                                    <h3 class="text-muted">{{$total_product??0}}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-12 col-md-12">
                <div class="card widget">
                    <div class="card-header">
                        <h5 class="card-title">Sales</h5>
                    </div>
                    <div class="row g-4">
                       @for($i=0;$i<=11;$i++)
                            <div class="col-md-2">
                                <div class="card border-0">
                                    <div class="card-body text-center">
                                        <div class="display-10">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>
                                        <h5 class="my-3">Active Orders</h5>
                                        <div class="progress mt-2 mb-2" style="height: 2px">
                                            <div class="progress-bar bg-secondary" role="progressbar"
                                                 style="width: 100%"></div>
                                        </div>
                                        <h3 class="text-muted">{{$total_user??0}}</h3>
                                    </div>
                                </div>
                            </div>
                       @endfor

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
