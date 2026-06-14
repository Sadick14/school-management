<?php

namespace App\Http\Controllers\Backend;

use App\AcademicTerm;
use App\AcademicYear;
use App\FeeStructure;
use App\FeeType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\IClass;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['hiddenId' => 'required|integer']);
            FeeStructure::findOrFail($request->get('hiddenId'))->delete();

            return redirect()->back()->with('success', 'Fee structure deleted.');
        }

        $academicYearId = $request->query->get('academic_year', 0);
        $classId = $request->query->get('class', 0);
        $termId = $request->query->get('term', 0);

        $structures = FeeStructure::with(['feeType', 'class', 'term', 'academicYear'])
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->when($classId, function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->when($termId, function ($q) use ($termId) {
                $q->where('term_id', $termId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order', 'asc')
            ->pluck('name', 'id');
        $terms = AcademicTerm::when($academicYearId, function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        })->orderBy('start_date', 'asc')->pluck('name', 'id');

        return view('backend.finance.fee_structure.list', compact(
            'structures', 'academicYears', 'classes', 'terms',
            'academicYearId', 'classId', 'termId'
        ));
    }

    public function create(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'fee_type_id' => 'required|integer|exists:fee_types,id',
                'class_id' => 'nullable|integer|exists:i_classes,id',
                'term_id' => 'nullable|integer|exists:academic_terms,id',
                'amount' => 'required|numeric|min:0',
            ]);

            $data = $request->only(['academic_year_id', 'fee_type_id', 'class_id', 'term_id', 'amount']);
            $data['class_id'] = $data['class_id'] ?: null;
            $data['term_id'] = $data['term_id'] ?: null;
            $data['status'] = AppHelper::ACTIVE;

            FeeStructure::updateOrCreate(['id' => $id], $data);

            return redirect()->route('finance.fee_structure.index', [
                'academic_year' => $data['academic_year_id'],
            ])->with('success', $id ? 'Fee structure updated.' : 'Fee structure added.');
        }

        $structure = FeeStructure::find($id);
        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');
        $feeTypes = FeeType::where('status', AppHelper::ACTIVE)
            ->orderBy('name', 'asc')
            ->pluck('name', 'id');
        $classes = IClass::where('status', AppHelper::ACTIVE)
            ->orderBy('order', 'asc')
            ->pluck('name', 'id');

        $selectedYear = $structure ? $structure->academic_year_id : $request->query->get('academic_year', 0);
        $terms = AcademicTerm::when($selectedYear, function ($q) use ($selectedYear) {
            $q->where('academic_year_id', $selectedYear);
        })->orderBy('start_date', 'asc')->pluck('name', 'id');

        return view('backend.finance.fee_structure.add', compact(
            'structure', 'academicYears', 'feeTypes', 'classes', 'terms', 'selectedYear'
        ));
    }

    public function status(Request $request, $id)
    {
        $structure = FeeStructure::findOrFail($id);
        $structure->status = (string) $request->get('status');
        $structure->save();

        return ['success' => true, 'message' => 'Status updated.'];
    }
}
