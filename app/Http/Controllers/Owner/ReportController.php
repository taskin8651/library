<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function library() { return Auth::user()->library; }

    public function index()
    {
        $library = $this->library();

        $summary = [
            'total_fees'       => FeePayment::where('library_id', $library->id)->sum('amount'),
            'total_payments'   => FeePayment::where('library_id', $library->id)->count(),
            'total_members'    => Member::where('library_id', $library->id)->count(),
            'total_attendance' => Attendance::where('library_id', $library->id)->count(),
        ];

        return view('owner.reports.index', compact('library', 'summary'));
    }

    public function exportFees(Request $request)
    {
        $library = $this->library();
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $payments = FeePayment::with('member.user')
            ->where('library_id', $library->id)
            ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('payment_date', '<=', $dateTo))
            ->orderBy('payment_date')
            ->get();

        $filename = 'fee-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Receipt No.', 'Member Name', 'UID', 'Amount', 'Mode', 'Payment Date', 'Valid From', 'Valid Till', 'Collected By']);
            foreach ($payments as $p) {
                fputcsv($out, [
                    $p->receipt_number,
                    $p->member->user->name ?? '-',
                    $p->member->uid ?? '-',
                    $p->amount,
                    strtoupper($p->payment_mode),
                    $p->payment_date->format('d-m-Y'),
                    $p->valid_from->format('d-m-Y'),
                    $p->valid_till->format('d-m-Y'),
                    $p->collected_by,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportAttendance(Request $request)
    {
        $library = $this->library();
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $records = Attendance::with('member.user', 'seat')
            ->where('library_id', $library->id)
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->orderBy('date')
            ->get();

        $filename = 'attendance-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Member Name', 'UID', 'Seat', 'Check In', 'Check Out', 'Duration']);
            foreach ($records as $a) {
                fputcsv($out, [
                    $a->date->format('d-m-Y'),
                    $a->member->user->name ?? '-',
                    $a->member->uid ?? '-',
                    $a->seat->seat_number ?? '-',
                    $a->check_in?->format('h:i A') ?? '-',
                    $a->check_out?->format('h:i A') ?? 'In Library',
                    $a->duration(),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportMembers()
    {
        $library = $this->library();

        $members = Member::with('user', 'seat', 'shift')
            ->where('library_id', $library->id)
            ->orderBy('created_at')
            ->get();

        $filename = 'members-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($members) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['UID', 'Name', 'Email', 'Phone', 'Seat', 'Shift', 'Status', 'Plan Start', 'Plan End']);
            foreach ($members as $m) {
                fputcsv($out, [
                    $m->uid,
                    $m->user->name ?? '-',
                    $m->user->email ?? '-',
                    $m->user->phone ?? '-',
                    $m->seat->seat_number ?? '-',
                    $m->shift->name ?? '-',
                    ucfirst($m->status),
                    $m->plan_start_date?->format('d-m-Y') ?? '-',
                    $m->plan_end_date?->format('d-m-Y') ?? '-',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
