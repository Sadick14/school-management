<?php

namespace App\Http\Controllers\Backend;

use App\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, ['hiddenId' => 'required|integer']);
            ExpenseCategory::findOrFail($request->get('hiddenId'))->delete();

            return redirect()->route('finance.expense_category.index')->with('success', 'Category deleted.');
        }

        $categories = ExpenseCategory::orderBy('name', 'asc')->get();

        return view('backend.finance.expense_category.list', compact('categories'));
    }

    public function create(Request $request, $id = 0)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required|max:255|unique:expense_categories,name' . ($id ? ",{$id}" : ''),
            ]);

            ExpenseCategory::updateOrCreate(
                ['id' => $id],
                ['name' => $request->get('name'), 'status' => AppHelper::ACTIVE]
            );

            return redirect()->route('finance.expense_category.index')
                ->with('success', $id ? 'Category updated.' : 'Category added.');
        }

        $category = ExpenseCategory::find($id);

        return view('backend.finance.expense_category.add', compact('category'));
    }

    public function status(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->status = (string) $request->get('status');
        $category->save();

        return ['success' => true, 'message' => 'Status updated.'];
    }
}
