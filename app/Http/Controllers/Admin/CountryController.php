<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController
{
    public function index()
    {
        return view('admin.countries.index', ['title' => 'Countries']);
    }

    public function datatable(Request $request)
    {
        $query = Country::query();
        $total = (clone $query)->count();

        if ($request->filled('search_term')) {
            $term = trim($request->string('search_term')->toString());
            $query->where('name', 'like', "%{$term}%");
        }

        $filtered = (clone $query)->count();
        $data = $query->orderBy('name')
            ->skip(max(0, (int) $request->input('start', 0)))
            ->take(min(100, max(1, (int) $request->input('length', 10))))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function addEditForm(?int $id = null)
    {
        return view('admin.countries.add_edit', [
            'title' => $id ? 'Edit Country' : 'Create Country',
            'country' => $id ? Country::findOrFail($id) : new Country(),
        ]);
    }

    public function save(Request $request, ?int $id = null)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('countries')->ignore($id)],
            'iso2' => ['required', 'string', 'size:2', Rule::unique('countries')->ignore($id)],
            'iso3' => ['required', 'string', 'size:3', Rule::unique('countries')->ignore($id)],
            'phonecode' => ['nullable', 'string', 'max:30'],
        ]);

        $validated['iso2'] = strtoupper($validated['iso2']);
        $validated['iso3'] = strtoupper($validated['iso3']);
        $validated['phonecode'] = $validated['phonecode'] ?? '';

        if (!$id) {
            $validated = array_merge([
                'capital' => '',
                'currency' => '',
                'currency_symbol' => '',
                'tld' => '',
                'native' => null,
                'region' => '',
                'subregion' => '',
                'timezones' => '[]',
                'translations' => null,
                'latitude' => '',
                'longitude' => '',
                'emoji' => '',
                'emojiU' => '',
                'flag' => 1,
                'wikiDataId' => null,
            ], $validated);
        }

        Country::updateOrCreate(['id' => $id], $validated);

        return redirect()->route('admin.countries')->with('success', 'Country saved successfully');
    }

    public function statusChange(int $id)
    {
        $country = Country::findOrFail($id);
        $country->update(['flag' => $country->flag ? 0 : 1]);

        return response()->json(['success' => true]);
    }

    public function delete(int $id)
    {
        Country::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
