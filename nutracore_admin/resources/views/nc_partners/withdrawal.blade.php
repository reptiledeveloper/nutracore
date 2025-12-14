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
                    <li class="breadcrumb-item active" aria-current="page"> Withdraw Requests</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-md-flex gap-4 align-items-center">
                            <div class="d-none d-md-flex">All Withdraw Requests</div>

                            <div class="dropdown ms-auto">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="withdrawals">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner</th>
                            <th>Amount</th>
                            <th>Withdrawal Type</th>
                            <th>Giftcard / Bank Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if(!empty($withdrawals))
                            @foreach($withdrawals as $row)
                                <tr>
                                    <td>#{{ $row->id }}</td>

                                    <td>
                                        {{ \App\Helpers\CustomHelper::getPartner($row->partner_id ?? '')->full_name??'' }}
                                    </td>

                                    <td>₹ {{ number_format($row->amount, 2) }}</td>

                                    <td>
                        <span class="badge bg-info">
                            {{ ucfirst($row->withdrawal_type) }}
                        </span>
                                    </td>

                                    <td class="text-wrap">
                                        @if($row->withdrawal_type === 'giftcard')
                                            <strong>Giftcard:</strong> {{ $row->selected_giftcard }}<br>
                                            <strong>Type:</strong> {{ $row->selected_type }}
                                        @else
                                            <strong>Bank:</strong> {{ $row->bank_name }}<br>
                                            <strong>A/C:</strong> {{ $row->account_number }}<br>
                                            <strong>IFSC:</strong> {{ $row->ifsc_code }}
                                        @endif
                                    </td>

                                    <td>
                                        @if($row->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($row->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ date('d M Y h:i A', strtotime($row->created_at)) }}
                                    </td>

                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-floating" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">

                                                @if($row->status === 'pending')
                                                    <a href="{{ route('nc_partners.with_draw_approve', $row->id) }}"
                                                       class="dropdown-item">Approve</a>

                                                    <a href="{{ route('nc_partners.with_draw_reject', $row->id) }}"
                                                       onclick="return confirm('Reject this withdrawal?')"
                                                       class="dropdown-item text-danger">Reject</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>


                {{ $withdrawals->appends(request()->input())->links('pagination') }}
            </div>
        </div>
    </div>

@endsection
