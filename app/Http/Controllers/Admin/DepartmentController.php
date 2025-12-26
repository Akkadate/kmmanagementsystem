<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only administrators can manage departments.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $departments = Department::withCount(['users', 'articles'])
            ->orderBy('name')
            ->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully!');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    public function destroy(Department $department)
    {
        // Check if department has users or articles
        if ($department->users()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with assigned users. Please reassign users first.');
        }

        if ($department->articles()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with associated articles. Please reassign articles first.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully!');
    }
}
