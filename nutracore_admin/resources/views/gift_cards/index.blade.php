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
                    <li class="breadcrumb-item active" aria-current="page">GiftCard</li>
                </ol>
            </nav>
        </div>
        @include('layouts.filter',['search_show'=>'search_show'])


        <div class="modal fade" id="AddGiftCardModal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Wallet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('gift_card.add', ['back_url' => $BackUrl]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" class="form-control" name="amount">
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label">No of GiftCard</label>
                                    <input type="number" class="form-control" name="no_of_giftcard">
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label class="form-label">Expire (In Months)</label>
                                    <input type="number" class="form-control" name="duration">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All GiftCard</div>

                            <div class="dropdown ms-auto">
                                <a data-bs-toggle="modal" data-bs-target="#AddGiftCardModal" class="btn btn-primary"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Code</th>
                            <th>Amount</th>
                            <th>User</th>
                            <th>Expire Date</th>
                            <th>Purchase Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($giftcards as $attr)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{ $attr->code ?? '' }}</td>
                                <td>{{ $attr->amount ?? '' }}</td>
                                <td>{{ $attr->user_id ?? '' }}</td>
                                <td>{{ $attr->expire_date ?? '' }}</td>
                                <td>{{ $attr->purchase_date ?? '' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getStatusStr($attr->status) }}</td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                               aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a data-bs-toggle="modal"
                                                   data-bs-target="#editGiftCardModal{{$attr->id}}"
                                                   class="dropdown-item">Edit</a>
                                                <a href="{{route('gift_card.delete',$attr->id.'?back_url='.$BackUrl)}}"
                                                   onclick="return confirm('Are you Want To Delete?')"
                                                   class="dropdown-item">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>



                            <div class="modal fade" id="editGiftCardModal{{$attr->id}}" tabindex="-1"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Wallet</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <form action="{{route('gift_card.edit',$attr->id.'?back_url='.$BackUrl)}}"
                                              method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$attr->id}}">
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">Status</label>
                                                        <select class="form-control" name="status">
                                                            <option value="1" {{$attr->status == 1?"selected":""}}>
                                                                Active
                                                            </option>
                                                            <option value="0" {{$attr->status == 0?"selected":""}}>
                                                                InActive
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @endforeach

                        </tbody>
                    </table>

                    {{ $giftcards->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
