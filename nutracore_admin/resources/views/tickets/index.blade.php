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
                    <li class="breadcrumb-item active" aria-current="page">Support Tickets</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Support Tickets</div>

                            <div class="dropdown ms-auto">
{{--                                <a href="{{ route('banners.add', ['back_url' => $BackUrl]) }}"--}}
{{--                                   class="btn btn-primary"><i class="fa fa-plus"></i></a>--}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="products">
                        <thead>
                        <tr>
                            <th>Ticket Type</th>
                            <th>User Name</th>
                            <th>Subject</th>
                            <th>Email</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($tickets)){
                        foreach ($tickets as $ticket) {
                            ?>
                        <tr>
                            <td>{{ $ticket->type ?? '' }}</td>
                            <td>{{ \App\Helpers\CustomHelper::getUserDetails($ticket->user_id)->name?? '' }}</td>
                            <td>{{ $ticket->subject ?? '' }}</td>
                            <td>{{ $ticket->email ?? '' }}</td>
                            <td>{{ $ticket->description ?? '' }}</td>
                            <td>

                                   <button class="btn btn-primary">
                                           <?php if ($ticket->status == 0){ ?>
                                       PENDING
                                       <?php }if ($ticket->status == 1) { ?>

                                       <?php }if ($ticket->status == 2){ ?>
                                       OPEN
                                       <?php }if ($ticket->status == 3){ ?>
                                       RESOLVE
                                       <?php }if ($ticket->status == 4){ ?>
                                       CLOSE
                                       <?php }if ($ticket->status == 5){ ?>
                                       REOPEN
                                       <?php } ?>
                                   </button>


                            </td>

                            <td class="text-end">
                                <div class="d-flex">
                                    <div class="dropdown ms-auto">
                                        <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                           aria-haspopup="true" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{route('support_tickets.edit',$ticket->id.'?back_url='.$BackUrl)}}"
                                               class="dropdown-item">View</a>
                                            <a href="{{route('support_tickets.delete',$ticket->id.'?back_url='.$BackUrl)}}"
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

                    {{ $tickets->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
