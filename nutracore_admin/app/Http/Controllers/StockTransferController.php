<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\StockTransfer;
use Attribute;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;

use App\Models\Brand;
use App\Models\Attributes;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class StockTransferController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $q = StockTransfer::with([
            'stock.product',
            'stock.variant'
        ])->latest();

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $transfers = $q->paginate(20)->withQueryString();
        return view('stock_transfers.index', compact('transfers'));
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $transfer = null;
        if (is_numeric($id) && $id > 0) {
            $transfer = StockTransfer::find($id);
            if (empty($transfer)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/stock_transfers');
            }
        }

        if ($request->isMethod('post')) {
            $back_url = $this->ADMIN_ROUTE_NAME . '/stock_transfers';

            $rules = [
                'items' => 'required|array|min:1',
                'items.*.stock_id' => 'required|exists:stocks,id',
                'items.*.from_location' => 'required|string|max:120',
                'items.*.to_location' => 'required|string|max:120|different:items.*.from_location',
                'items.*.quantity' => 'required|integer|min:1',
            ];

            $request->validate($rules);

            $saved = $this->save($request, $id);

            if ($saved) {
                $alert_msg = 'Stock transfer(s) added successfully.';
                if ($id > 0) {
                    $alert_msg = 'Stock transfer(s) updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'Something went wrong, please try again or contact the administrator.');
            }
        }

        $page_heading = 'Transfer Stock';
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['transfer'] = $transfer;
        $data['stocks'] = Stock::latest()->get();

        return view('stock_transfers.form', $data);
    }

    public function save(Request $request, $id = 0)
    {
        // No bulk update logic for now — only insert new transfers
        foreach ($request->items as $row) {
            $transfer = new StockTransfer();
            $transfer->stock_id = $row['stock_id'];
            $transfer->from_location = $row['from_location'];
            $transfer->to_location = $row['to_location'];
            $transfer->quantity = $row['quantity'];
            $transfer->status = 'pending';
            $transfer->save();
        }

        return true;
    }


    public function approve($id)
    {
        $t = StockTransfer::findOrFail($id);

        if ($t->status !== 'pending') {
            return back()->withErrors('Only pending transfers can be approved.');
        }

        // Get the stock record for this transfer
        $stock = Stock::with('product', 'variant')->find($t->stock_id);

        if (!$stock) {
            return back()->withErrors('Stock record not found.');
        }

        // Decrement from source location
        StockBatch::where('product_id', $stock->product_id)
            ->where('variant_id', $stock->variant_id)
            ->where('batch_number', $stock->batch_number)
            ->where('store_id', $t->from_location)
            ->decrement('quantity', $t->quantity);

        // Increment in target location
        StockBatch::updateOrCreate(
            [
                'product_id' => $stock->product_id,
                'variant_id' => $stock->variant_id,
                'batch_number' => $stock->batch_number,
                'store_id' => $t->to_location,
            ],
            [
                'mfg_date' => $stock->mfg_date,
                'expiry_date' => $stock->expiry_date,
                'purchase_price' => $stock->purchase_price,
                'quantity' => DB::raw('quantity + ' . $t->quantity),
            ]
        );

        // From Location (Out)
        CustomHelper::logStock(
            $stock->product_id,
            $stock->variant_id,
            $t->from_location,
            'transfer_out',
            $t->quantity,
            $t->id,
            'StockTransfer'
        );

// To Location (In)
        CustomHelper::logStock(
            $stock->product_id,
            $stock->variant_id,
            $t->to_location,
            'transfer_in',
            $t->quantity,
            $t->id,
            'StockTransfer'
        );


        // Mark as approved
        $t->status = 'approved';
        $t->save();

        return back()->with('success', 'Transfer approved and stock updated.');
    }


    public function reject($id)
    {
        $t = StockTransfer::findOrFail($id);
        if ($t->status !== 'pending') return back()->withErrors('Only pending transfers can be rejected.');
        $t->status = 'rejected';
        $t->save();
        return back()->with('success', 'Transfer rejected.');
    }


}
