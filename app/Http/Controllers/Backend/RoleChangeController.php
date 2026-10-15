<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\RoleChange;

class RoleChangeController extends Controller
{
    /**
     * Display a listing of role changes (upgrades, downgrades and approvals).
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteUrl)
    {
        if (request()->ajax()) {
            return datatables()->of(RoleChange::with(['admin', 'changedBy'])->select('role_changes.*'))
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
                ->addColumn('user', function ($row) {
                    return ($row->admin->name ?? '-') . ' (' . ($row->admin->mobile_number ?? '-') . ')';
                })
                ->editColumn('action', function ($row) {
                    return ucwords(str_replace('_', ' ', $row->action));
                })
                ->addColumn('from', function ($row) {
                    return $this->typeLabel($row->from_type) . ' / ' . ($row->from_role ?? '-');
                })
                ->addColumn('to', function ($row) {
                    return $this->typeLabel($row->to_type) . ' / ' . $row->to_role;
                })
                ->addColumn('changed_by', function ($row) {
                    return $row->changedBy->name ?? 'System';
                })
                ->editColumn('upgrade_request_id', function ($row) {
                    return $row->upgrade_request_id ?? '-';
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('backend.role-change.index');
    }

    /**
     * Readable name for an Add2Farm user type.
     */
    private function typeLabel($type): string
    {
        $labels = [
            0 => 'Super Admin',
            1 => 'Farm Admin',
            2 => 'Farm Owner',
            3 => 'Supervisor',
            4 => 'Farmer',
        ];

        return $labels[(int) $type] ?? "Type {$type}";
    }
}
