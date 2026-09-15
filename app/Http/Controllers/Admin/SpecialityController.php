<?php

namespace App\Http\Controllers\Admin;

use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpecialityController
{
    public function index()
    {
        return view('admin.specialities.index', ['title' => 'Specialities']);
    }

    public function datatable(Request $request)
    {
        $query = Speciality::query();
        $total = (clone $query)->count();
        if ($request->filled('search_term')) {
            $term = trim($request->string('search_term')->toString());
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%"));
        }
        $filtered = (clone $query)->count();
        $data = $query->latest()->skip(max(0, (int) $request->input('start', 0)))->take(min(100, max(1, (int) $request->input('length', 10))))->get();

        return response()->json(['draw' => (int) $request->input('draw'), 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $data]);
    }

    public function addEditForm(?int $id = null)
    {
        return view('admin.specialities.add_edit', ['title' => $id ? 'Edit Speciality' : 'Create Speciality', 'speciality' => $id ? Speciality::findOrFail($id) : new Speciality()]);
    }

    public function save(Request $request, ?int $id = null)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('specialities')->ignore($id)],
            'description' => ['nullable', 'string'],
        ]);
        Speciality::updateOrCreate(['id' => $id], $validated);
        return redirect()->route('admin.specialities')->with('success', 'Speciality saved successfully');
    }

    public function statusChange(int $id)
    {
        $item = Speciality::findOrFail($id);
        $item->update(['status' => $item->status === 'active' ? 'inactive' : 'active']);
        return response()->json(['success' => true]);
    }

    public function delete(int $id)
    {
        Speciality::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
