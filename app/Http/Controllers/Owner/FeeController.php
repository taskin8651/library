<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FeeController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index(Request $request)
    {
        $library = $this->library();
        $payments = FeePayment::with('member.user')
            ->where('library_id', $library->id)
            ->when($request->search, fn($q) => $q->whereHas('member.user', fn($u) =>
                $u->where('name','like',"%{$request->search}%")))
            ->when($request->mode, fn($q) => $q->where('payment_mode',$request->mode))
            ->when($request->date_from, fn($q) => $q->whereDate('payment_date','>=',$request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('payment_date','<=',$request->date_to))
            ->latest()->paginate(20);

        $summary = [
            'today'   => FeePayment::where('library_id',$library->id)->whereDate('payment_date',today())->sum('amount'),
            'month'   => FeePayment::where('library_id',$library->id)->whereMonth('payment_date',now()->month)->sum('amount'),
            'cash'    => FeePayment::where('library_id',$library->id)->where('payment_mode','cash')->whereMonth('payment_date',now()->month)->sum('amount'),
            'upi'     => FeePayment::where('library_id',$library->id)->where('payment_mode','upi')->whereMonth('payment_date',now()->month)->sum('amount'),
        ];

        return view('owner.fees.index', compact('payments','summary','library'));
    }

    public function create()
    {
        $library = $this->library();
        $members = Member::with('user')->where('library_id',$library->id)->where('status','active')->get();
        return view('owner.fees.create', compact('members','library'));
    }

    public function store(Request $request)
    {
        $library = $this->library();
        $request->validate([
            'member_id'    => 'required|exists:members,id',
            'amount'       => 'required|numeric|min:1',
            'payment_mode' => 'required|in:cash,upi,bank,other',
            'payment_date' => 'required|date',
            'valid_from'   => 'required|date',
            'valid_till'   => 'required|date|after:valid_from',
        ]);

        $payment = FeePayment::create([
            'library_id'   => $library->id,
            'member_id'    => $request->member_id,
            'amount'       => $request->amount,
            'payment_mode' => $request->payment_mode,
            'upi_ref'      => $request->upi_ref,
            'payment_date' => $request->payment_date,
            'valid_from'   => $request->valid_from,
            'valid_till'   => $request->valid_till,
            'collected_by' => Auth::user()->name,
            'notes'        => $request->notes,
        ]);

        // Update member plan dates
        $member = Member::find($request->member_id);
        $member->update([
            'plan_start_date' => $request->valid_from,
            'plan_end_date'   => $request->valid_till,
            'status'          => 'active',
        ]);

        return redirect('/owner/fees/' . $payment->id . '/receipt')
            ->with('success', 'Payment recorded. Receipt #' . $payment->receipt_number);
    }

    public function receipt(FeePayment $payment)
    {
        $library = $this->library();
        if ($payment->library_id !== $library->id) abort(403);
        $payment->load('member.user','member.seat','member.shift');
        return view('owner.fees.receipt', compact('payment','library'));
    }

    public function downloadReceipt(FeePayment $payment)
    {
        $library = $this->library();
        if ($payment->library_id !== $library->id) abort(403);
        $payment->load('member.user','member.seat','member.shift');
        $pdf = Pdf::loadView('owner.fees.receipt_pdf', compact('payment','library'));
        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }
}
