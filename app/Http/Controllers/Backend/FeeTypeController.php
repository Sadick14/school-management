<?php

namespace App\Http\Controllers\Backend;

use App\FeeType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['hiddenId' => 'required|integer']);
            FeeType::findOrFail($request->get('hiddenId'))->delete();

            return redirect()->route('finance.fee_type.index')->with('success', 'Fee type deleted.');
        }

        $feeTypes = FeeType::orderBy('name', 'asc')->get();

        return view('backend.finance.fee_type.list', compact('feeTypes'));
    }

    public function create(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'code' => 'required|max:30|unique:fee_types,code' . ($id ? ",{$id}" : ''),
                'name' => 'required|max:255',
                'billing_cycle' => 'required|in:term,daily,once_per_year,once_per_student,ad_hoc',
                'applies_to' => 'required|in:all,new_students_only,continuing_only',
            ]);

            $data = $request->only(['code', 'name', 'billing_cycle', 'applies_to']);
            $data['code'] = strtoupper($data['code']);
            $data['is_optional'] = $request->has('is_optional') ? 1 : 0;
            $data['status'] = AppHelper::ACTIVE;

            FeeType::updateOrCreate(['id' => $id], $data);

            return redirect()->route('finance.fee_type.index')
                ->with('success', $id ? 'Fee type updated.' : 'Fee type added.');
        }

        $feeType = FeeType::find($id);

        return view('backend.finance.fee_type.add', compact('feeType'));
    }

    public function status(Request $request, $id)
    {
        $feeType = FeeType::findOrFail($id);
        $feeType->status = (string) $request->get('status');
        $feeType->save();

        return ['success' => true, 'message' => 'Status updated.'];
    }
}
