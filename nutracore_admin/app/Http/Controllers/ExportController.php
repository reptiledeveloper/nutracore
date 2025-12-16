<?php

namespace App\Http\Controllers;

use App\Exports\SampleExport;
use App\Helpers\CustomHelper;
use App\Models\Admin;
use App\Models\Category;

use App\Models\POSDailyCashTransaction;
use App\Models\StockDataImport;
use App\Models\Order;

use App\Models\DeliveryAgents;
use App\Models\Sellers;
use App\Models\Subscriptions;
use App\Models\User;
use App\Models\Products;
use App\Models\Transaction;
use App\Exports\StockDataExport;
use App\Models\Vendors;
use Auth;
use DB;
use Google\Service\ShoppingContent\ProductsCustomBatchResponse;


use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class ExportController extends Controller
{


    private string $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }

    public function index(Request $request)
    {
        $data = [];


        return view('reports.index', $data);
    }

    public function sales(Request $request)
    {
        $start_date = $request->start_date ?? '';
        $end_date = $request->end_date ?? '';
        $exportArr = [];

        // You can apply filters if needed (date range, payment mode, etc.)
        $orders = \App\Models\Order::where('is_delete', 0)->where('status', 'DELIVERED')->where('is_delete', 0);
        // ✅ Apply date range filter (if both provided)
        if (!empty($start_date) && !empty($end_date)) {
            $orders->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date]);
        } elseif (!empty($start_date)) {
            $orders->whereDate('created_at', '>=', $start_date);
        } elseif (!empty($end_date)) {
            $orders->whereDate('created_at', '<=', $end_date);
        }

        $orders->orderBy('id', 'DESC');

        // Chunk to handle large data efficiently
        $orders->chunk(50, function ($orders) use (&$exportArr) {
            foreach ($orders as $order) {
                $excelArr = [];

                // Decode payment method values if JSON stored
                $paymentValues = json_decode($order->payment_method_values, true) ?? [];
                if ($order->payment_method == "Multipay") {
                    // If multiple payments are used, values will come from JSON
                    $cash = $paymentValues['cash'] ?? 0;
                    $card = $paymentValues['card'] ?? 0;
                    $upi = $paymentValues['upi'] ?? 0;
                    $wallet = $paymentValues['wallet'] ?? 0;
                    $bank = $paymentValues['bank'] ?? 0;
                    $sodexo = $paymentValues['sodexo'] ?? 0;
                    $cheque = $paymentValues['cheque'] ?? 0;
                    $paylater = $paymentValues['paylater'] ?? 0;
                    $credit_apply = $paymentValues['credit_apply'] ?? 0;

                    // Set overall payment mode display
                    $paymentModeDisplay = 'Multiple';
                } else {
                    // Single payment mode (like COD, Online, Wallet, etc.)
                    $cash = $card = $upi = $wallet = $bank = $sodexo = $cheque = $paylater = $credit_apply = 0;
                    $order->total_amount = (int)$order->total_amount + (int)$order->delivery_charges - (int)$order->applied_cashback - (int)$order->flatDiscountValue;
                    // Assign based on mode
                    switch (strtolower($order->payment_method)) {
                        case 'cash':
                        case 'cod':
                            $cash = $order->total_amount ?? 0;
                            break;
                        case 'card':
                            $card = $order->total_amount ?? 0;
                            break;
                        case 'upi':
                            $upi = $order->total_amount ?? 0;
                            break;
                        case 'wallet':
                            $wallet = $order->total_amount ?? 0;
                            break;
                        case 'bank':
                            $bank = $order->total_amount ?? 0;
                            break;
                        case 'sodexo':
                            $sodexo = $order->total_amount ?? 0;
                            break;
                        case 'cheque':
                            $cheque = $order->total_amount ?? 0;
                            break;
                        case 'paylater':
                            $paylater = $order->total_amount ?? 0;
                            break;
                        case 'credit_apply':
                            $credit_apply = $order->total_amount ?? 0;
                            break;
                        default:
                            // Fallback: just show the mode name
                            $paymentModeDisplay = ucfirst($order->payment_method ?? '');
                            break;
                    }

                    $paymentModeDisplay = ucfirst($order->payment_method ?? '');
                }
                $admin_data = Admin::where('id', $order->created_by)->first();

                $excelArr['Sr No.'] = $order->id;
                $excelArr['Invoice No'] = $order->invoice_no ?? '';
                $excelArr['Date'] = $order->created_at ? $order->created_at->format('Y-m-d') : '';
                $excelArr['Customer Name'] = $order->customer_name ?? '';
                $excelArr['Mobile No'] = $order->contact_no ?? '';
                $excelArr['Total Amount'] = $order->total_amount ?? 0;
                $excelArr['TCS Amount'] = $paymentValues['tcs'] ?? 0;
                $excelArr['Payment Mode'] = ucfirst($order->payment_method ?? '');
                $excelArr['Cash'] = $cash;
                $excelArr['Card'] = $card;
                $excelArr['UPI'] = $upi;
                $excelArr['Wallet'] = $wallet;
                $excelArr['Bank'] = $bank;
                $excelArr['SODEXO'] = $sodexo;
                $excelArr['Cheque'] = $cheque;
                $excelArr['Paylater'] = $paylater;
                $excelArr['Credit Apply'] = $credit_apply;
                $excelArr['Created By'] = $admin_data->name ?? 'User'; // assuming relation exists
                $excelArr['Order From'] = strtoupper($order->order_from ?? '');
                $excelArr['Location'] = $order->vendor->name ?? '';

                $exportArr[] = $excelArr;
            }
        });

        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Sales-Summary-' . date('Y-m-d-H-i-s') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back()->with('error', 'No sales data found!');
        }

    }

    public function sales_register_tax_report(Request $request)
    {
        $unitState = 'TG'; // Your business unit state code (e.g., Telangana)
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth();
        $end_date = $request->end_date ?? Carbon::now()->endOfMonth();


        $orders = \App\Models\Order::where('is_delete', 0)
            ->where('status', 'DELIVERED')
            ->whereBetween('created_at', [
                $start_date . ' 00:00:00',
                $end_date . ' 23:59:59'
            ])
            ->get();

        $exportArr = [];
        $sr = 1;

        foreach ($orders as $order) {
            // 🏠 Fetch customer address (joined via relation)

            $address = $order->userAddress ?? null;
            $buyerState = $address->state ?? null; // State code from user_address (e.g., TG, MH)
            if ($order->order_from == 'POS') {
                $buyerState = $unitState;
            }
            foreach ($order->items as $item) {
                $product = $item->product;
                $variant = $item->variant ?? null;
                // 💡 Skip if product not found
                if (!$product) continue;
                $place_of_supply = $address->state ?? '';
                if ($order->order_from == "POS") {
                    $place_of_supply = "TG";

                }
                $excelArr = [];
                $excelArr['S.No.'] = $sr++;
                $excelArr['Order Date'] = $order->created_at->format('d-M-Y');
                $excelArr['Store Name'] = CustomHelper::getVendorName($order->vendor_id) ?? '';
                $excelArr['Invoice No.'] = $order->invoice_no ?? '';
                $excelArr['Order No.'] = $order->unique_id ?? '';
                $excelArr['Channel (Store/App/Web)'] = ucfirst($order->order_from ?? '');
                $excelArr['Customer Name'] = $order->customer_name ?? '';
                $excelArr['Customer Type (B2C/B2B)'] = 'B2C';
                $excelArr['Customer GSTIN'] = $order->gstin ?? '';
                $excelArr['Place of Supply (State, Code)'] = $place_of_supply;
                $excelArr['HSN Code'] = $product->hsn ?? '';
                $excelArr['SKU / Product Name'] = $product->name ?? '';
                $excelArr['Qty'] = $item->qty ?? 0;
                $excelArr['UOM'] = $variant->unit ?? '';
                $excelArr['MRP (₹)'] = $item->price ?? 0;
                $excelArr['Selling Price / Unit (₹)'] = round(($item->net_price / max($item->qty, 1)), 2);
//                $grossAmount = (int)$order->total_amount + (int)$order->delivery_charges - (int)$order->applied_cashback - (int)$order->flatDiscountValue;
                $grossAmount = $item->net_price ?? 0;
                $discount = (float)($item->discount ?? 0);
                $shipping = (float)($order->shipping_amount ?? 0);
                $finalAmount = $grossAmount;
                $igst_rate = (float)($product->tax ?? 0);
                $cgst_rate = $igst_rate / 2;
                $sgst_rate = $igst_rate / 2;

                $taxableValue = round($finalAmount / (1 + ($igst_rate / 100)), 2);
                $taxAmount = round($finalAmount - $taxableValue, 2);
//                $grossAmount = max(($item->mrp - ($item->discount ?? 0)), 0);
                if ($buyerState && strcasecmp($buyerState, $unitState) === 0) {
                    // 🌐 Inter-State: IGST only
                    $cgst = round($taxAmount / 2, 2);
                    $sgst = round($taxAmount / 2, 2);
                    $igst = 0;
                    $igst_rate = '';

                } else {
                    $igst = $taxAmount;
                    $cgst = 0;
                    $sgst = 0;
                    $cgst_rate = '';
                    $sgst_rate = '';
                }
                $invoiceValue = $finalAmount;
                $excelArr['Gross Amount (₹)'] = $invoiceValue;
                $excelArr['Discount (₹)'] = $discount;
                $excelArr['Shipping (₹)'] = $shipping;
                $excelArr['Taxable Value (₹)'] = $taxableValue;
                $excelArr['CGST %'] = $cgst_rate;
                $excelArr['CGST (₹)'] = $cgst;
                $excelArr['SGST %'] = $sgst_rate;
                $excelArr['SGST (₹)'] = $sgst;
                $excelArr['IGST %'] = $igst_rate;
                $excelArr['IGST (₹)'] = $igst;
                $excelArr['Total Tax (₹)'] = $taxAmount;
                $excelArr['Invoice Value (₹)'] = $invoiceValue;

                $excelArr['Payment Mode'] = ucfirst($order->payment_method ?? '');
                $excelArr['Transaction ID / UTR'] = $order->transaction_id ?? '';
                $excelArr['Returns / Exchange Ref'] = $item->return_reasons ?? '';
                $excelArr['Remarks'] = $item->admin_remarks ?? '';
                $excelArr['Order Status'] = $order->status ?? '';

                $exportArr[] = $excelArr;
            }
        }

        if (empty($exportArr)) {
            return back()->with('error', 'No orders found for the selected date.');
        }

        $headers = array_keys($exportArr[0]);
        $fileName = 'Sales_Register_Tax_Report_' . date('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(new SampleExport($exportArr, $headers), $fileName);
    }

    public function delivery_agent(Request $request)
    {

        $exportArr = [];
        $agents = DeliveryAgents::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Email'] = $agent->email ?? '';
                $excelArr['Phone'] = $agent->phone ?? '';
                $excelArr['Address'] = $agent->address ?? '';
                $excelArr['Vehicle Type'] = $agent->vehicle_type ?? '';
                $excelArr['Vehicle No'] = $agent->vehicle_no ?? '';
                $excelArr['Vehicle Name'] = $agent->vehicle_name ?? '';
                $excelArr['Bank Name'] = $agent->bank_name ?? '';
                $excelArr['Account No'] = $agent->account_no ?? '';
                $excelArr['IFSC Code'] = $agent->ifsc_code ?? '';
                $excelArr['Wallet'] = $agent->wallet ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Delivery Agents-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function supplier_bill(Request $request)
    {
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth();
        $end_date = $request->end_date ?? Carbon::now()->endOfMonth();

        // Fetch invoices with suppliers
        $invoices = \App\Models\Invoice::with('supplier')
            ->whereBetween('invoice_date', [$start_date, $end_date])
            ->where('is_delete', 0)
            ->get();

        $exportArr = [];
        $sr = 1;

        foreach ($invoices as $invoice) {
            $supplier = $invoice->supplier;

            $excelArr = [];
            $excelArr['S.No.'] = $sr++;
            $excelArr['Status'] = $invoice->status == 1 ? 'Active' : 'Inactive';
            $excelArr['Bill No'] = $invoice->invoice_number;
            $excelArr['Bill Date'] = Carbon::parse($invoice->invoice_date)->format('d-M-Y');
            $excelArr['Vendor'] = $supplier->name ?? 'N/A';
            $excelArr['Amount'] = number_format($invoice->subtotal, 2);

            // You can later fetch payment details dynamically
            $paidAmount = $invoice->paid_amount ?? 0; // if you have column or payments table
            $excelArr['Paid Amount'] = number_format($paidAmount, 2);

            $dueAmount = $invoice->subtotal - $paidAmount;
            $excelArr['Due Amount'] = number_format($dueAmount, 2);

            // Tax Calculation (assuming subtotal includes tax)
            $taxAmount = ($invoice->tax_amount ?? 0);
            $excelArr['Tax Amount'] = number_format($taxAmount, 2);

            $excelArr['Due Date'] = Carbon::parse($invoice->invoice_date)->addDays(30)->format('d-M-Y'); // Example due date logic
            $excelArr['Created By'] = $invoice->created_by ?? 'Admin';

            $exportArr[] = $excelArr;
        }

        if (empty($exportArr)) {
            return back()->with('error', 'No invoices found for the selected date.');
        }

        $headers = array_keys($exportArr[0]);
        $fileName = 'Purchase_Register_' . date('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(new SampleExport($exportArr, $headers), $fileName);
    }


    public function sellers(Request $request)
    {

        $exportArr = [];
        $agents = Sellers::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['Business Name'] = $agent->name ?? '';
                $excelArr['Name'] = $agent->user_name ?? '';
                $excelArr['Email'] = $agent->user_email ?? '';
                $excelArr['Phone'] = $agent->user_phone ?? '';
                $excelArr['GST No'] = $agent->tax_number ?? '';
                $excelArr['Address'] = $agent->address ?? '';
                $excelArr['Bank Name'] = $agent->bank_name ?? '';
                $excelArr['Account No'] = $agent->account_no ?? '';
                $excelArr['IFSC Code'] = $agent->ifsc_code ?? '';
                $excelArr['Delivery Time'] = $agent->delivery_time ?? '';
                $excelArr['Open Time'] = $agent->open_time ?? '';
                $excelArr['Close Time'] = $agent->close_time ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Sellers-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function categories(Request $request)
    {

        $exportArr = [];
        $agents = Category::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['ID'] = $agent->id ?? '';
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Slug'] = $agent->slug ?? '';
                $excelArr['Is Subscribe'] = $agent->is_subscribe ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Categories-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function subcategories(Request $request)
    {

        $exportArr = [];

        $agents = Category::where('status', 1)->where('parent_id', '!=', 0)->where('is_delete', 0);

        $agents = Category::where('status', 1)->where('parent_id', '!=', 0)->where('is_delete', 0);

        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['ID'] = $agent->id ?? '';
                $excelArr['Parent Category'] = CustomHelper::getCategoryName($agent->parent_id) ?? '';
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Slug'] = $agent->slug ?? '';
                $excelArr['Is Subscribe'] = $agent->is_subscribe ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'SubCategories-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function users(Request $request)
    {
        $exportArr = [];

        $agents = User::where('status', 1)->where('is_delete', 0);

        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $customer_subs_data = \App\Helpers\CustomHelper::getUserSubsData($agent);
                $excelArr = [];

                // Image URL

                // User fields
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Email'] = $agent->email ?? '';
                $excelArr['Phone'] = $agent->phone ?? '';
                $excelArr['NC Cash'] = $agent->cashback_wallet ?? 0;

                // Status using helper
                $excelArr['Status'] = \App\Helpers\CustomHelper::getStatusStr($agent->status);

                // Referral user
                if ($agent->referral_userID) {
                    $refUser = \App\Helpers\CustomHelper::getUserDetails($agent->referral_userID);
                    $excelArr['Join By'] = $refUser->name ?? '';
                } else {
                    $excelArr['Join By'] = '';
                }

                // Type
                $excelArr['Join Through'] = $agent->type ?? '';
                $excelArr['IS Ban'] = $agent->is_ban == 1 ? "Yes" : 'No';

                // Subscription data
                $excelArr['Loyalty Tier'] = $customer_subs_data['loyality'] ?? '';
                $excelArr['Membership Status'] = $customer_subs_data['membership_status'] ?? '';
                $excelArr['Total Spent'] = $customer_subs_data['total_spent'] ?? 0;
                $excelArr['Total Orders'] = $customer_subs_data['total_order'] ?? 0;

                // Join date
                $excelArr['Join On'] = $agent->created_at ? date('d M Y', strtotime($agent->created_at)) : '';

                $exportArr[] = $excelArr;
            }
        });

        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]); // Get headers from the first row
            $fileName = 'Users-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back()->with('error', 'No users found to export.');
        }
    }

    public function consultation(Request $request)
    {
        $exportArr = [];

        $agents = User::where('status', 1)->where('is_delete', 0);
        $agents->whereNotNull('gender')->where('gender', '!=', '')
            ->whereNotNull('height')->where('height', '!=', '')
            ->whereNotNull('weight')->where('weight', '!=', '')
            ->whereNotNull('health_profile')->where('health_profile', '!=', '')
            ->whereNotNull('activity')->where('activity', '!=', '')
            ->whereNotNull('food_choice')->where('food_choice', '!=', '');
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $user) {
                $row = [];
                $row['Name'] = $user->name ?? '';
                $row['Email'] = $user->email ?? '';
                $row['Phone'] = $user->phone ?? '';
                $row['DOB'] = !empty($user->dob) ? date('Y-m-d', strtotime($user->dob)) : '';
                $row['Gender'] = $user->gender ?? '';
                $row['Height'] = $user->height ?? '';
                $row['Weight'] = $user->weight ?? '';
                $row['Health Profile'] = $user->health_profile ?? '';
                $row['Daily Activity'] = $user->activity ?? '';
                $row['Food Choice'] = $user->food_choice ?? '';
                $row['Lead Status'] = $user->lead_status ?? '';

                $exportArr[] = $row;
            }
        });

        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]); // Get headers from the first row
            $fileName = 'Users-Consultation' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back()->with('error', 'No users found to export.');
        }
    }


    public function stock_data(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $data = [];
        $category_id = $request->category_id ?? '';
        $sub_category_id = $request->sub_category_id ?? '';
        $vendor_id = $request->vendor_id ?? 0;
        $products = Products::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($category_id)) {
            $products->where('category_id', $category_id);
        }
        if (!empty($sub_category_id)) {
            $products->where('subcategory_id', $sub_category_id);
        }
        $products = $products->paginate(50);
        $data['products'] = $products;
        $exportArr = [];
        if (!empty($products)) {
            foreach ($products as $product) {
                $varients = CustomHelper::getProductVarients($product->id ?? '');
                if (!empty($varients)) {
                    foreach ($varients as $varient) {
                        $stock_avail = CustomHelper::getNoOfStock($product->id, $varient->id, $vendor_id);
                        $excelArr = [];
                        $excelArr['ID'] = $product->id ?? '';
                        $excelArr['VarientID'] = $varient->id ?? '';
                        $excelArr['VendorID'] = (string)$vendor_id;
                        $excelArr['Category'] = CustomHelper::getCategoryName($product->category_id ?? '');
                        $excelArr['SubCategory'] = CustomHelper::getCategoryName($product->subcategory_id ?? '');
                        $excelArr['ProductName'] = $product->name ?? '';
                        $excelArr['Varient'] = $varient->unit ?? '';
                        $excelArr['StockAvailable'] = (string)$stock_avail ?? 0;
                        $exportArr[] = $excelArr;
                    }
                }
            }
        }

        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Product Stock Data-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new StockDataExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }
    }


    public function transaction(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $start_date = $request->start_date ?? '';
        $end_date = $request->end_date ?? '';
        $exportArr = [];

        $transactionsArr = Transaction::latest('id');
        if (!empty($start_date)) {
            $transactionsArr->whereDate('created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $transactionsArr->whereDate('created_at', '<=', $end_date);
        }
        $transactionsArr->chunk(50, function ($transactions) use (&$exportArr) {
            foreach ($transactions as $transaction) {
                $user = CustomHelper::getUserDetails($transaction->userID);
                $excelArr = [];
                $excelArr['UserName'] = $user->name ?? '';
                $excelArr['UserPhone'] = $user->phone ?? '';
                $excelArr['Txn No'] = $transaction->txn_no ?? '';
                $excelArr['Amount'] = $transaction->amount ?? '';
                $excelArr['Type'] = $transaction->type ?? '';
                $excelArr['Note'] = $transaction->note ?? '';
                $excelArr['Order ID'] = $transaction->orderID ?? '';
                $excelArr['TimeStamp'] = $transaction->created_at ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Transaction Data-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new StockDataExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }
    }


    public function stock_data_import(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $rules = [];
            $rules['file'] = 'required';
            $request->validate($rules);

            Excel::import(new StockDataImport, request()->file('file'));
            return back();
        }

    }

//    public function cash_management(Request $request)
//    {
//        $start_date = $request->start_date ?? '';
//        $end_date = $request->end_date ?? '';
//        $exportArr = [];
//
//        $records = POSDailyCashTransaction::select(
//            'date',
//            'vendor_id',
//            DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_sales"),
//            DB::raw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_expense"),
//            DB::raw('COUNT(id) as total_transactions')
//        )
//            ->where('is_delete', 0);
//
//        if (!empty($start_date)) {
//            $records->whereDate('date', '>=', $start_date);
//        }
//
//        if (!empty($end_date)) {
//            $records->whereDate('date', '<=', $end_date);
//        }
//
//        $records->groupBy('date', 'vendor_id')
//            ->orderByDesc('date')
//            ->chunk(100, function ($rows) use (&$exportArr) {
//                foreach ($rows as $row) {
//                    $vendor = \App\Helpers\CustomHelper::getVendorDetails($row->vendor_id);
//                    $excelArr = [];
//
//                    $excelArr['Date'] = $row->date ? date('d M Y', strtotime($row->date)) : '';
//                    $excelArr['Vendor Name'] = $vendor->name ?? '';
//                    $excelArr['Total Sales (Credit)'] = number_format($row->total_sales ?? 0, 2);
//                    $excelArr['Total Expense (Debit)'] = number_format($row->total_expense ?? 0, 2);
//                    $excelArr['Total Transactions'] = $row->total_transactions ?? 0;
//
//                    $exportArr[] = $excelArr;
//                }
//            });
//
//        if (!empty($exportArr)) {
//            $headings = array_keys($exportArr[0]);
//            $fileName = 'Cash-Transactions-' . date('Y-m-d-H-i-s') . '.xlsx';
//            return Excel::download(new \App\Exports\StockDataExport($exportArr, $headings), $fileName);
//        } else {
//            return back()->with('error', 'No transaction data found for export.');
//        }
//    }


    public function cash_management(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        $exportArr = [];

        /** Vendors */
        $vendors = Vendors::where('is_delete', 0)
            ->pluck('name', 'id')
            ->toArray();

        /** Orders mapped by unique_id */
        $orders = Order::where('is_delete', 0)
            ->select('unique_id','id', 'invoice_no', 'payment_method')
            ->get()
            ->keyBy('id');

        $query = POSDailyCashTransaction::where('is_delete', 0)
            ->orderBy('vendor_id')
            ->orderBy('date')
            ->orderBy('id');

        if ($start_date) {
            $query->whereDate('date', '>=', $start_date);
        }

        if ($end_date) {
            $query->whereDate('date', '<=', $end_date);
        }

        $transactions = $query->get();

        /** Balance key: vendor_id + date (DAILY RESET) */
        $vendorDayBalance = [];

        foreach ($transactions as $txn) {

            $vendorId = $txn->vendor_id;
            $dateKey  = date('Y-m-d', strtotime($txn->date));
            $balanceKey = $vendorId . '_' . $dateKey;

            /** Reset balance per vendor per day */
            if (!isset($vendorDayBalance[$balanceKey])) {
                $vendorDayBalance[$balanceKey] = 0;
            }

            $cashIn  = 0;
            $cashOut = 0;

            if ($txn->type === 'credit') {
                $cashIn = (float) $txn->amount;
                $vendorDayBalance[$balanceKey] += $cashIn;
            }

            if ($txn->type === 'debit') {
                $cashOut = (float) $txn->amount;
                $vendorDayBalance[$balanceKey] -= $cashOut;
            }

            $order = $orders[$txn->order_id] ?? null;



            $exportArr[] = [
                'Date' => date('d M Y', strtotime($txn->date)),

                'Vendor Name' => $vendors[$vendorId] ?? 'N/A',

                'Order No.' =>  $order->unique_id ?? '-',

                'Invoice No.' => $order->invoice_no ?? '-',

                'Description (Sale / Expense)' =>
                    $txn->type === 'credit' ? 'Sale' : 'Expense',

                'Mode of Payment' =>
                    $order->payment_method ?? 'Cash',

                'Cash In (₹)' =>
                    $cashIn > 0 ? number_format($cashIn, 2) : '',

                'Cash Out (₹)' =>
                    $cashOut > 0 ? number_format($cashOut, 2) : '',

                'Running Closing Balance (₹)' =>
                    number_format($vendorDayBalance[$balanceKey], 2),

                'Closing Entry' =>
                    $txn->type === 'credit'
                        ? 'Sale Entry'
                        : 'Expense Entry',

                'Closing Notes' => $txn->remarks ?? '',
            ];
        }



        if (empty($exportArr)) {
            return back()->with('error', 'No cash transactions found.');
        }

        $fileName = 'POS-Cash-Ledger-Daily-' . date('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new \App\Exports\StockDataExport(
                $exportArr,
                array_keys($exportArr[0])
            ),
            $fileName
        );
    }


    public function nc_cash_report(Request $request)
    {
        $exportArr = [];

        Order::where('is_delete', 0)
            ->where(function ($q) {
                $q->where('nc_cash_earned', ' > ', 0)
                    ->orWhere('applied_cashback', ' > ', 0);
            })
            ->orderBy('created_at', 'desc')
            ->chunk(100, function ($orders) use (&$exportArr) {

                foreach ($orders as $order) {
                    $user = User::find($order->userID);
                    $amount = $order->total_amount ?? 0;
                    $earned = (float)($order->nc_cash_earned ?? 0);
                    $redeemed = (float)($order->applied_cashback ?? 0);

                    $exportArr[] = [
                        'Date' => optional($order->created_at)->format('d - m - Y'),

                        'Customer Name' => $order->customer_name ?? '',

                        'Customer Mobile / Email' => $order->contact_no ?? '',

                        'Order ID' => $order->invoice_no ?? $order->id,

                        'Transaction Type(Earn / Bonus / Adjustment)' =>
                            $earned > 0 && $redeemed > 0
                                ? 'Earn & Redeem'
                                : ($earned > 0 ? 'Earn' : 'Redeem'),

                        'Transaction Description' =>
                            $earned > 0
                                ? 'NC Cash earned on order'
                                : 'NC Cash redeemed on order',

                        'Order Value(₹)' => $order->total_amount ?? 0,

                        'NC Cash Earned' => $earned,

                        'NC Cash Redeemed' => $redeemed,
                        'Available Balance(After Txn)' => $user->cashback_wallet ?? '', // optional

                        'Expiry Date' => '',

                        'Channel(App / Store / Web)' =>
                            ucfirst(strtolower($order->order_from ?? 'app')),

                        'Loyalty Tier(Silver / Gold / Platinum)' => self::getActiveSubscription($user),

                        'NutraPass Membership' =>
                            $order->is_subscribe ? 'Yes' : 'No',

                        'NutraPass Expiry Date' => $user->subscription_end,
                    ];
                }
            });

        if (empty($exportArr)) {
            return back()->with('error', 'No NC Cash data found');
        }

        $fileName = 'NC - Cash - Order - Report - ' . date('Y - m - d - H - i - s') . ' . xlsx';

        return Excel::download(
            new SampleExport($exportArr, array_keys($exportArr[0])),
            $fileName
        );
    }


}
