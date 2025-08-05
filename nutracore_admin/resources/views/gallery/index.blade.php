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
                    <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                </ol>
            </nav>
        </div>


        @include('layouts.filter',['folder_show'=>'folder_show','search_show'=>'search_show','folders'=>$folders])
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Gallery</div>
                            <div class="dropdown ms-auto">
                                <a href="{{ route('gallery.add', ['back_url' => $BackUrl]) }}" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Folder Name</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($files as $file)
                            @php
                                $path = $file->getPathname();
                                $path_val = \App\Helpers\CustomHelper::getImagePath($path);
                                $file_url = env('IMAGE_URL').'/'.$path_val;
                            @endphp
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{basename($folder_name)}}</td>
                                <td>{{ $path_val ?? '' }}</td>
                                <td>
                                    <a href="<?php echo $file_url?>" target="_blank" class="ml-3"><img
                                            src="<?php echo $file_url?>" height="70px" width="70px"></a>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                               aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{route('gallery.delete',['file_name'=>$path])}}"
                                                   onclick="return confirm('Are you Want To Delete?')"
                                                   class="dropdown-item">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    @if(!empty($files))
                        {{ $files->appends(request()->input())->links('pagination') }}
                    @endif

                </div>

            </div>
        </div>
    </div>

@endsection
