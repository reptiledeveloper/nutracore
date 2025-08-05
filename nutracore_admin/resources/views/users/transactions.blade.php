@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $users_id = $users->id ?? '';
    $name = $users->name ?? '';
    $email = $users->email ?? '';
    $phone = $users->phone ?? '';
    $status = $users->status ?? 1;

    $image = \App\Helpers\CustomHelper::getImageUrl('users', $users->image ?? '');
    ?>
    @include('users.common',['users'=>$users])

    @include('snippets.errors')
    @include('snippets.flash')

    <div class="card mt-3">
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-custom table-lg mb-0" id="orders">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Txn No</th>
                        <th>Wallet Type</th>
                        <th>AMount</th>
                        <th>Type</th>
                        <th>Note</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($transactions)){
                        $i = 1;
                    foreach ($transactions as $transaction) {

                        ?>
                    <tr>
                        <td># {{ $i++ ?? '' }}</td>
                        <td>{{$transaction->txn_no??''}}</td>
                        <td>{{$transaction->wallet_type??''}}</td>
                        <td>{{$transaction->amount??''}}</td>
                        <td>{{$transaction->type??''}}</td>
                        <td>{{$transaction->note??''}}</td>
                        <td>{{ date('d M Y h:i A',strtotime($transaction->created_at)) }}</td>

                    </tr>
                    <?php }
                    } ?>

                    </tbody>
                </table>

                {{ $transactions->appends(request()->input())->links('pagination') }}


            </div>
        </div>
    </div>


    </div>

@endsection
