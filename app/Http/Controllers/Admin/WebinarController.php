<?php

namespace App\Http\Controllers\Admin;

use App\Models\Webinar;
use App\Models\Speciality;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WebinarController
{
    public function index()
    {
        return view('admin.webinars.index', [
            'title' => __('Webinars'),
            'breadcrumb' => breadcrumb([__('Webinars') => route('admin.webinars')]),
        ]);
    }

    public function datatable(Request $request)
    {
        $baseQuery = Webinar::query();
        $recordsTotal = (clone $baseQuery)->count();

        if ($request->filled('search_term')) {
            $search = trim($request->string('search_term')->toString());
            $baseQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('speaker_name', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();
        $allowedSorts = ['title', 'type', 'duration_minutes', 'scheduled_at', 'status'];
        $orderColumn = (int) $request->input('order.0.column', 0);
        $column = data_get($request->input('columns', []), $orderColumn . '.data');
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        if (in_array($column, $allowedSorts, true)) {
            $baseQuery->orderBy($column, $direction);
        } else {
            $baseQuery->latest('id');
        }

        $length = min(max((int) $request->input('length', 10), 1), 100);
        $data = $baseQuery->skip(max((int) $request->input('start', 0), 0))->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function addEditForm(?int $id = null)
    {
        $webinar = $id ? Webinar::with('people')->findOrFail($id) : new Webinar(['duration_minutes' => 60]);
        $facultyRows = old('faculty');

        if ($facultyRows === null) {
            $facultyRows = $webinar->people->map(fn ($person) => [
                'existing_id' => $person->id,
                'role' => $person->role,
                'name' => $person->name,
                'designation' => $person->designation,
                'qualifications' => $person->qualifications,
                'current_position' => $person->current_position,
                'institution' => $person->institution,
                'country' => $person->country,
                'bio' => $person->bio,
                'image_url' => $person->image_url,
            ])->all();

            if (!$facultyRows && $webinar->speaker_name) {
                $facultyRows[] = ['role' => 'speaker', 'name' => $webinar->speaker_name, 'bio' => $webinar->speaker_bio];
            }
        } else {
            $existingPeople = $webinar->people->keyBy('id');
            foreach ($facultyRows as &$row) {
                $existing = !empty($row['existing_id']) ? $existingPeople->get((int) $row['existing_id']) : null;
                if ($existing) $row['image_url'] = $existing->image_url;
            }
            unset($row);
        }

        $specialities = Speciality::where(function ($query) use ($webinar) {
            $query->where('status', 'active');
            if ($webinar->speciality_id) $query->orWhere('id', $webinar->speciality_id);
        })->orderBy('name')->get();

        $countries = Country::where(function ($query) use ($webinar) {
            $query->where('flag', 1);
            if ($webinar->country) $query->orWhere('name', $webinar->country);
        })->orderBy('name')->get();

        return view('admin.webinars.add_edit', [
            'webinar' => $webinar,
            'facultyRows' => $facultyRows,
            'specialities' => $specialities,
            'countries' => $countries,
            'title' => $webinar->exists ? __('Edit Webinar') : __('Create Webinar'),
            'breadcrumb' => breadcrumb([
                __('Webinars') => route('admin.webinars'),
                $webinar->exists ? __('Edit Webinar') : __('Create Webinar') => '',
            ]),
        ]);
    }

    public function save(Request $request, ?int $id = null)
    {
        $request->merge([
            'speciality_id' => $request->filled('speciality_id') ? (int) $request->input('speciality_id') : null,
        ]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'country' => [
                'required',
                Rule::exists('countries', 'name')->where(fn ($query) => $query->where('flag', 1)),
            ],
            'speciality_id' => ['required', 'integer', 'exists:specialities,id'],
            'type' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'description' => ['nullable', 'string'],
            'speaker_name' => ['nullable', 'string', 'max:255'],
            'speaker_bio' => ['nullable', 'string'],
            'meeting_url' => ['nullable', 'url', 'max:2048'],
            'recording_url' => ['nullable', 'url', 'max:2048'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'pre_read_url' => ['nullable', 'url', 'max:2048'],
            'post_read_url' => ['nullable', 'url', 'max:2048'],
            'certificate_template' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:15360'],
            'remove_certificate_template' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date', 'required_unless:publish_immediately,1'],
            'timezone_label' => ['nullable', 'string', 'max:100'],
            'publish_immediately' => ['nullable', 'boolean'],
            'faculty' => ['nullable', 'array'],
            'faculty.*.existing_id' => ['nullable', 'integer'],
            'faculty.*.role' => ['required', 'in:speaker,moderator'],
            'faculty.*.name' => ['required', 'string', 'max:255'],
            'faculty.*.designation' => ['nullable', 'string', 'max:255'],
            'faculty.*.qualifications' => ['nullable', 'string', 'max:255'],
            'faculty.*.current_position' => ['nullable', 'string', 'max:255'],
            'faculty.*.institution' => ['nullable', 'string', 'max:255'],
            'faculty.*.country' => ['nullable', 'string', 'max:100'],
            'faculty.*.bio' => ['nullable', 'string'],
            'faculty.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'faculty.*.remove_image' => ['nullable', 'boolean'],
        ], [
            'country.required' => 'Please select a country for this webinar.',
            'country.exists' => 'The selected country is not active in the Country master.',
            'speciality_id.required' => 'Please select a speciality for this webinar.',
            'speciality_id.exists' => 'The selected speciality is no longer available.',
        ]);

        $faculty = $validated['faculty'] ?? [];
        unset($validated['faculty'], $validated['cover_image'], $validated['remove_cover_image'], $validated['certificate_template'], $validated['remove_certificate_template']);
        $validated['publish_immediately'] = $request->boolean('publish_immediately');
        if ($validated['publish_immediately']) {
            $validated['scheduled_at'] = null;
        }

        DB::transaction(function () use ($validated, $faculty, $id, $request) {
            $webinar = Webinar::updateOrCreate(['id' => $id], $validated);

            if ($request->boolean('remove_cover_image')) {
                if ($webinar->cover_image_path) Storage::disk('public')->delete($webinar->cover_image_path);
                $webinar->cover_image_path = null;
                $webinar->cover_image_url = null;
            }

            if ($request->hasFile('cover_image')) {
                if ($webinar->cover_image_path) Storage::disk('public')->delete($webinar->cover_image_path);
                $webinar->cover_image_path = $request->file('cover_image')->store('uploads/webinar-covers', 'public');
                $webinar->cover_image_url = null;
            }
            if ($webinar->isDirty(['cover_image_path', 'cover_image_url'])) $webinar->save();

            if ($request->boolean('remove_certificate_template') && $webinar->certificate_template_path) {
                Storage::disk('public')->delete($webinar->certificate_template_path);
                $webinar->certificate_template_path = null;
            }

            if ($request->hasFile('certificate_template')) {
                if ($webinar->certificate_template_path) Storage::disk('public')->delete($webinar->certificate_template_path);
                $webinar->certificate_template_path = $request->file('certificate_template')->store('uploads/webinar-certificates', 'public');
            }

            if ($webinar->isDirty('certificate_template_path')) $webinar->save();
            $keptIds = [];

            foreach ($faculty as $index => $personData) {
                $person = !empty($personData['existing_id'])
                    ? $webinar->people()->findOrFail($personData['existing_id'])
                    : $webinar->people()->make();

                if (!empty($personData['remove_image']) && $person->image_path) {
                    Storage::disk('public')->delete($person->image_path);
                    $person->image_path = null;
                }

                if ($request->hasFile("faculty.{$index}.image")) {
                    if ($person->image_path) Storage::disk('public')->delete($person->image_path);
                    $person->image_path = $request->file("faculty.{$index}.image")->store('uploads/webinar-faculty', 'public');
                }

                $person->fill([
                    'role' => $personData['role'],
                    'name' => $personData['name'],
                    'designation' => $personData['designation'] ?? null,
                    'qualifications' => $personData['qualifications'] ?? null,
                    'current_position' => $personData['current_position'] ?? null,
                    'institution' => $personData['institution'] ?? null,
                    'country' => $personData['country'] ?? null,
                    'bio' => $personData['bio'] ?? null,
                    'sort_order' => $index,
                ])->save();
                $keptIds[] = $person->id;
            }

            $webinar->people()->whereNotIn('id', $keptIds)->get()->each(function ($person) {
                if ($person->image_path) Storage::disk('public')->delete($person->image_path);
                $person->delete();
            });

            $firstSpeaker = $webinar->people()->where('role', 'speaker')->orderBy('sort_order')->first();
            $webinar->update(['speaker_name' => $firstSpeaker?->name, 'speaker_bio' => $firstSpeaker?->bio]);
        });

        return redirect()->route('admin.webinars')->with('success', 'Webinar saved successfully');
    }

    public function statusChange(int $id)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->update(['status' => $webinar->status === 'active' ? 'inactive' : 'active']);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function delete(int $id)
    {
        $webinar = Webinar::with('people')->findOrFail($id);
        $this->deleteStoredFiles($webinar);
        $webinar->delete();

        return response()->json(['success' => true, 'message' => 'Webinar deleted successfully']);
    }

    public function deleteMultiple(Request $request)
    {
        $validated = $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer']]);
        Webinar::with('people')->whereIn('id', $validated['ids'])->get()->each(function ($webinar) {
            $this->deleteStoredFiles($webinar);
            $webinar->delete();
        });

        return response()->json(['success' => true, 'message' => 'Selected webinars deleted successfully']);
    }

    private function deleteStoredFiles(Webinar $webinar): void
    {
        if ($webinar->cover_image_path) Storage::disk('public')->delete($webinar->cover_image_path);
        if ($webinar->certificate_template_path) Storage::disk('public')->delete($webinar->certificate_template_path);
        $webinar->people->each(function ($person) {
            if ($person->image_path) Storage::disk('public')->delete($person->image_path);
        });
    }
}
