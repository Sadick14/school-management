<?php

namespace App\Http\Controllers\Backend;

use App\AcademicTerm;
use App\AcademicYear;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['hiddenId' => 'required|integer']);
            $term = AcademicTerm::findOrFail($request->get('hiddenId'));
            $term->delete();

            return redirect()->route('finance.term.index')->with('success', 'Term deleted.');
        }

        $academicYearId = $request->query->get('academic_year', 0);
        $terms = AcademicTerm::with('academicYear')
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->orderBy('start_date', 'asc')
            ->get();

        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');

        $currentTermIds = $terms->pluck('academic_year_id')->unique()
            ->mapWithKeys(function ($yearId) {
                $term = AppHelper::getActiveTerm($yearId);
                return [$yearId => $term ? $term->id : null];
            });

        return view('backend.finance.term.list', compact('terms', 'academicYears', 'academicYearId', 'currentTermIds'));
    }

    public function create(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'name' => 'required|max:255',
                'start_date' => 'required|date_format:d/m/Y',
                'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
            ]);

            $data = $request->only(['academic_year_id', 'name']);
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', $request->get('start_date'));
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $request->get('end_date'));
            $data['status'] = AppHelper::ACTIVE;

            AcademicTerm::updateOrCreate(['id' => $id], $data);

            $msg = $id ? 'Term updated.' : 'Term added.';

            return redirect()->route('finance.term.index', ['academic_year' => $data['academic_year_id']])
                ->with('success', $msg);
        }

        $term = AcademicTerm::find($id);
        $academicYears = AcademicYear::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->pluck('title', 'id');

        return view('backend.finance.term.add', compact('term', 'academicYears'));
    }

    public function status(Request $request, $id)
    {
        $term = AcademicTerm::findOrFail($id);
        $term->status = (string) $request->get('status');
        $term->save();

        return ['success' => true, 'message' => 'Status updated.'];
    }
}
