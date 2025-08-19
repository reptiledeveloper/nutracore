@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();

    $faqs_id = $faqs->id ?? '';
    $question = $faqs->question ?? '';
    $type = $faqs->type ?? '';
    $answer = $faqs->answer ?? '';
    $status = $faqs->status ?? 1;
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
                    <li class="breadcrumb-item active" aria-current="page">{{$page_heading}}</li>
                </ol>
            </nav>
        </div>
        @include('snippets.errors')
        @include('snippets.flash')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">{{$page_heading}}</div>
                            <?php if (request()->has('back_url')){
                                $back_url = request('back_url'); ?>
                            <div class="dropdown ms-auto">
                                <a href="{{ url($back_url) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <div class="card mt-3">
                    <div class="card-body pt-0">
                        <form class="card-body" action="" method="post" accept-chartset="UTF-8"
                              enctype="multipart/form-data" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" value="{{ $faqs_id }}">

                            <div class="row">
                                 <div class="form-group col-md-6">
                                    <label for="validationCustom01" class="form-label">Type</label>
                                  <select class="form-control" name="type">
                                        <option value="" selected>Select</option>
                                        <option value="subscription" {{ $type == 'subscription' ?"selected":"" }}>Subscription</option>
                                        <option value="refer" {{ $type == 'refer' ?"selected":"" }}>Refer & Earn</option>
                                        <option value="nc_cash" {{ $type == 'nc_cash' ?"selected":"" }}>NC Cash</option>

                                  </select>

                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="validationCustom01" class="form-label">Question</label>
                                    <textarea name="question" class="editor">{{old('question',$question)}}</textarea>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="validationCustom01" class="form-label">Answer</label>
                                    <textarea name="answer" class="editor">{{old('answer',$answer)}}</textarea>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label for="userName" class="form-label">Status<span
                                            class="text-danger">*</span></label>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="1"
                                               <?php echo $status == '1' ? 'checked' : ''; ?> checked>
                                        <label class="form-check-label"
                                               for="customRadioBox1">Active</label>
                                    </div>

                                    <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                        <input type="radio" class="form-check-input" name="status"
                                               value="0" <?php echo strlen($status) > 0 && $status == '0' ? 'checked' : ''; ?>>
                                        <label class="form-check-label"
                                               for="customRadioBox1">InActive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
