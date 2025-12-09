@extends('layouts.layout')
@section('content')

    <?php
    $BackUrl = \App\Helpers\CustomHelper::BackUrl();
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $vendors = \App\Helpers\CustomHelper::getVendors();
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
                    <li class="breadcrumb-item active" aria-current="page">Expense</li>
                </ol>
            </nav>
        </div>
        <div class="modal fade" id="addExpense" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{route('pos.add_expense')}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <!-- Expense Amount -->
                                <div class="col-md-12 mt-3">
                                    <label for="expense_amount" class="form-label">Amount</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="amount" id="expense_amount" class="form-control" placeholder="Enter Amount" required>
                                    </div>
                                </div>
                                <!-- Expense Description -->
                                <div class="col-md-12 mt-3">
                                    <label for="expense_description" class="form-label">Description</label>
                                    <textarea name="description" id="expense_description" class="form-control" rows="2" placeholder="Enter Description"></textarea>
                                </div>

                                <!-- Payment Method -->
                                <div class="col-md-6 mt-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="UPI">UPI</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- Store / Vendor -->
                                <div class="col-md-6 mt-3">
                                    <label for="store_id" class="form-label">Store</label>
                                    <select name="store_id" id="store_id" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" >Save</button>
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
                            <div class="d-none d-md-flex">All Expense</div>

                            <div class="dropdown ms-auto">
                                <a data-bs-toggle="modal"
                                   data-bs-target="#addExpense" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="expenses">
                        <thead>
                        <tr>
                            <th>Title / Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Store</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>
                                    {{ $expense->description ?? 'N/A' }}
                                </td>
                                <td>{{ $expense->category ?? 'N/A' }}</td>
                                <td>₹{{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->payment_method ?? 'Cash' }}</td>
                                <td>{{ \App\Helpers\CustomHelper::getVendorName($expense->store_id) ?? 'N/A' }}</td>
                                <td>{{ date('d-m-Y', strtotime($expense->expense_date)) }}</td>
                                <td>
                                    @if($expense->amount > 0)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex">
                                        <div class="dropdown ms-auto">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-floating"
                                               aria-haspopup="true" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a data-bs-toggle="modal"
                                                   data-bs-target="#editExpense{{$expense->id}}"
                                                   class="dropdown-item">Edit</a>
                                                <a href="{{ route('pos.delete_expense', $expense->id.'?back_url='.$BackUrl) }}"
                                                   onclick="return confirm('Are you sure you want to delete this expense?')"
                                                   class="dropdown-item">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="editExpense{{$expense->id}}" tabindex="-1" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add Expense</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <form action="{{ route('pos.edit_expense', ['id' => $expense->id]) }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Expense Amount -->
                                                    <div class="col-md-12 mt-3">
                                                        <label for="expense_amount" class="form-label">Amount</label>
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="amount" id="expense_amount" class="form-control"
                                                                   placeholder="Enter Amount" required
                                                                   value="{{ old('amount', $expense->amount) }}">
                                                        </div>
                                                    </div>

                                                    <!-- Expense Description -->
                                                    <div class="col-md-12 mt-3">
                                                        <label for="expense_description" class="form-label">Description</label>
                                                        <textarea name="description" id="expense_description" class="form-control" rows="2"
                                                                  placeholder="Enter Description">{{ old('description', $expense->description) }}</textarea>
                                                    </div>

                                                    <!-- Payment Method -->
                                                    <div class="col-md-6 mt-3">
                                                        <label for="payment_method" class="form-label">Payment Method</label>
                                                        <select name="payment_method" id="payment_method" class="form-control" required>
                                                            @php
                                                                $methods = ['Cash', 'Card', 'UPI', 'Other'];
                                                            @endphp
                                                            @foreach($methods as $method)
                                                                <option value="{{ $method }}"
                                                                    {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>
                                                                    {{ $method }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <!-- Store / Vendor -->
                                                    <div class="col-md-6 mt-3">
                                                        <label for="store_id" class="form-label">Store</label>
                                                        <select name="store_id" id="store_id" class="form-control" required>
                                                            <option value="">Select Store</option>
                                                            @foreach($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}"
                                                                    {{ old('store_id', $expense->store_id) == $vendor->id ? 'selected' : '' }}>
                                                                    {{ $vendor->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>


                                    </div>
                                </div>
                            </div>
                        @empty

                            <tr>
                                <td colspan="8" class="text-center">No expenses found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>


                    {{ $expenses->appends(request()->input())->links('pagination') }}


                </div>
            </div>
        </div>
    </div>

@endsection
