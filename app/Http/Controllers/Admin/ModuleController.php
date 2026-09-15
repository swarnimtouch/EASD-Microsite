<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ModuleController
{
    public function index()
    {
        return view('admin.modules.index', ['title' => 'Modules', 'breadcrumb' => breadcrumb(['Modules' => route('admin.modules')])]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = Module::query();
        $total = (clone $query)->count();
        if ($request->filled('search_term')) {
            $search = trim($request->string('search_term')->toString());
            $query->where(fn ($builder) => $builder->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        }
        $filtered = (clone $query)->count();
        $allowedSorts = ['title', 'sequence_order', 'content_type', 'release_at', 'status'];
        $columnIndex = (int) $request->input('order.0.column', 0);
        $column = data_get($request->input('columns', []), $columnIndex.'.data');
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        in_array($column, $allowedSorts, true) ? $query->orderBy($column, $direction) : $query->orderBy('sequence_order')->latest('id');
        $length = min(max((int) $request->input('length', 10), 1), 100);

        return response()->json(['draw' => (int) $request->input('draw'), 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $query->skip(max((int)$request->input('start', 0), 0))->take($length)->get()]);
    }

    public function addEditForm(?int $id = null)
    {
        $module = $id ? Module::findOrFail($id) : new Module(['sequence_order' => (Module::max('sequence_order') ?? 0) + 1, 'content_type' => 'pdf', 'content_source' => 'upload', 'minimum_viewing_minutes' => 0, 'status' => 'draft']);
        return view('admin.modules.add_edit', ['module' => $module, 'title' => $module->exists ? 'Edit Module' : 'Create Module', 'breadcrumb' => breadcrumb(['Modules' => route('admin.modules'), $module->exists ? 'Edit Module' : 'Create Module' => ''])]);
    }

    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $module = $id ? Module::findOrFail($id) : new Module();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'],
            'sequence_order' => ['required', 'integer', 'min:1'], 'content_type' => ['required', 'in:pdf,external_url'],
            'content_source' => ['required', 'in:upload,url'], 'module_file' => [(!$module->file_path && $request->input('content_source') === 'upload') ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:204800'],
            'content_url' => ['nullable', 'required_if:content_source,url', 'url', 'max:2048'], 'release_at' => ['nullable', 'date'],
            'minimum_viewing_minutes' => ['required', 'integer', 'min:0', 'max:1440'], 'status' => ['required', 'in:draft,active,inactive'],
        ]);

        $module->fill([
            'title' => $validated['title'], 'slug' => $this->uniqueSlug($validated['title'], $module->id), 'description' => $validated['description'] ?? null,
            'sequence_order' => $validated['sequence_order'], 'content_type' => $validated['content_type'], 'content_source' => $validated['content_source'],
            'content_url' => $validated['content_source'] === 'url' ? $validated['content_url'] : null, 'release_at' => $validated['release_at'] ?? null,
            'minimum_viewing_minutes' => $validated['minimum_viewing_minutes'], 'status' => $validated['status'],
        ]);
        if ($request->input('content_source') === 'url' && $module->file_path) { Storage::disk('public')->delete($module->file_path); $module->file_path = null; $module->original_file_name = null; }
        if ($request->hasFile('module_file')) { if ($module->file_path) Storage::disk('public')->delete($module->file_path); $module->file_path = $request->file('module_file')->store('uploads/modules', 'public'); $module->original_file_name = $request->file('module_file')->getClientOriginalName(); }
        $module->save();

        return redirect()->route('admin.modules')->with('success', 'Module saved successfully.');
    }

    public function statusChange(Module $module): JsonResponse
    {
        $module->update(['status' => $module->status === 'active' ? 'inactive' : 'active']);
        return response()->json(['success' => true, 'status' => $module->status]);
    }

    public function delete(Module $module): JsonResponse
    {
        if ($module->file_path) Storage::disk('public')->delete($module->file_path);
        $module->delete();
        return response()->json(['success' => true]);
    }

    public function deleteMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:modules,id'],
        ]);

        Module::whereKey($validated['ids'])->get()->each(function (Module $module): void {
            if ($module->file_path) {
                Storage::disk('public')->delete($module->file_path);
            }
            $module->delete();
        });

        return response()->json(['success' => true]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId): string
    {
        $base = Str::slug($title) ?: 'module'; $slug = $base; $number = 2;
        while (Module::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) $slug = $base.'-'.$number++;
        return $slug;
    }
}
