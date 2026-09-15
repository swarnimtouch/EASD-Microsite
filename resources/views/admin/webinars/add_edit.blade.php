@extends('layouts.admin')

@push('styles')
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css">
    <style>
        .flatpickr-calendar { border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 18px 45px rgba(15, 23, 42, .16); font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
        .flatpickr-day { border-radius: 9px; }
        .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #0891b2; border-color: #0891b2; }
        .flatpickr-day.today { border-color: #0891b2; color: #0891b2; }
        .flatpickr-time { border-top-color: #e2e8f0; }
        .webinar-date-wrap .flatpickr-input { padding-right: 3rem; }
    </style>
@endpush

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                @if ($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                @include('admin.partials.form_heading', ['eyebrow'=>'Event Management / '.($webinar->exists ? 'Edit Webinar' : 'New Webinar'),'heading'=>$webinar->exists ? 'Edit Webinar' : 'Create Webinar','description'=>$webinar->exists ? 'Update webinar scheduling, content, media and faculty.' : 'Configure a webinar, its faculty, resources and publishing schedule.','backUrl'=>route('admin.webinars'),'backLabel'=>'Back to Webinars'])

                <div class="card admin-form-shell">
                @include('admin.partials.form_card_header', ['icon'=>'bi bi-camera-video','title'=>'Webinar Information','subtitle'=>'Event details, publishing, media and faculty information'])
                <form method="POST" action="{{ route('admin.webinars.save', $webinar->id) }}" id="webinar-form" enctype="multipart/form-data">
                    @csrf
                    @if ($webinar->exists) @method('PUT') @endif

                    <div class="webinar-main-grid grid grid-cols-1 gap-6 p-6 md:grid-cols-2 lg:p-8">
                        <div class="fv-row md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-600 required">Webinar Title</label>
                            <input type="text" name="title" class="mh-field" value="{{ old('title', $webinar->title) }}" placeholder="e.g. Advances in COPD Management">
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600 required">Type</label>
                            @php
                                $selectedType = old('type', $webinar->type);
                            @endphp
                            <select name="type" class="mh-field">
                                <option value="">Select webinar type</option>
                                @foreach (['Quarterly National', 'Regional', 'International', 'Workshop', 'On Demand'] as $type)
                                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600 required">Duration (mins)</label>
                            <input type="number" name="duration_minutes" min="1" max="1440" class="mh-field" value="{{ old('duration_minutes', $webinar->duration_minutes ?? 60) }}">
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600 required">Country</label>
                            <select name="country" class="mh-field">
                                <option value="">Select country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->name }}" @selected(old('country', $webinar->country) === $country->name)>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Host tour country: Malaysia, Philippines, Indonesia, or Thailand.</p>
                            @if($countries->isEmpty())<p class="mt-2 text-xs font-semibold text-amber-600">No active countries are available. <a href="{{ route('admin.countries.add_edit_form') }}" class="underline">Create a country first</a>.</p>@endif
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600 required">Speciality</label>
                            <select name="speciality_id" class="mh-field"><option value="">Select speciality</option>@foreach($specialities as $speciality)<option value="{{ $speciality->id }}" @selected((string)old('speciality_id',$webinar->speciality_id)===(string)$speciality->id)>{{ $speciality->name }}</option>@endforeach</select>
                            @if($specialities->isEmpty())<p class="mt-2 text-xs font-semibold text-amber-600">No active specialities are available. <a href="{{ route('admin.specialities.add_edit_form') }}" class="underline">Create a speciality first</a>.</p>@endif
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Description</label>
                            <textarea name="description" rows="4" class="mh-field min-h-28 resize-y" placeholder="Webinar description...">{{ old('description', $webinar->description) }}</textarea>
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Meeting / Zoom Link</label>
                            <input type="url" name="meeting_url" class="mh-field" value="{{ old('meeting_url', $webinar->meeting_url) }}" placeholder="https://zoom.us/j/....">
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Recording URL (post-event)</label>
                            <input type="url" name="recording_url" class="mh-field" value="{{ old('recording_url', $webinar->recording_url) }}" placeholder="https://....">
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Scheduled Date &amp; Time</label>
                            <div class="webinar-date-wrap relative">
                                <input type="text" name="scheduled_at" id="scheduled_at" class="mh-field cursor-pointer" value="{{ old('scheduled_at', $webinar->scheduled_at?->format('Y-m-d H:i')) }}" placeholder="Select date and time" autocomplete="off">
                                <i class="bi bi-calendar3 pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-600">Location / Timezone Label</label>
                            <input type="text" name="timezone_label" class="mh-field" value="{{ old('timezone_label', $webinar->timezone_label) }}" placeholder="e.g. Philippines (PST)">
                        </div>
                        <div class="flex items-end pb-3">
                            <label class="form-check form-check-custom form-check-solid mb-1">
                                <input class="form-check-input" type="checkbox" name="publish_immediately" id="publish_immediately" value="1" @checked(old('publish_immediately', $webinar->publish_immediately))>
                                <span class="form-check-label">Publish immediately</span>
                            </label>
                        </div>
                        <div class="fv-row md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Webinar Thumbnail / Cover Image</label>
                            <div class="grid grid-cols-1 items-center gap-5 rounded-2xl border border-slate-200 bg-slate-50 p-5 md:grid-cols-[220px_1fr]">
                                <img id="webinar-cover-preview" src="{{ $webinar->cover_image }}" alt="Webinar cover preview" class="aspect-video w-full rounded-xl border border-slate-200 bg-slate-900 object-cover">
                                <div>
                                    <label for="webinar-cover-image" class="btn btn-light cursor-pointer"><i class="bi bi-cloud-arrow-up"></i>Choose Cover Image</label>
                                    <input type="file" name="cover_image" id="webinar-cover-image" class="hidden" accept="image/jpeg,image/png,image/webp">
                                    <p id="webinar-cover-filename" class="mt-3 text-xs text-slate-500">JPG, PNG or WebP up to 5 MB. Recommended 16:9 ratio.</p>
                                    @if($webinar->cover_image_path || $webinar->cover_image_url)
                                        <label class="mt-4 flex items-center gap-2 text-xs font-semibold text-rose-600"><input type="checkbox" name="remove_cover_image" id="remove-cover-image" value="1"> Remove current cover image</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Pre-read Material URL</label>
                            <input type="url" name="pre_read_url" class="mh-field" value="{{ old('pre_read_url', $webinar->pre_read_url) }}" placeholder="https://....">
                        </div>
                        <div class="fv-row">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Post-read Material URL</label>
                            <input type="url" name="post_read_url" class="mh-field" value="{{ old('post_read_url', $webinar->post_read_url) }}" placeholder="https://....">
                        </div>
                        <div class="fv-row md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-600">Certificate Template (PDF)</label>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <input type="file" name="certificate_template" class="mh-field bg-white" accept="application/pdf">
                                <p class="mt-2 text-xs text-slate-500">Upload the webinar certificate background. The logged-in doctor's name is added when the certificate is downloaded.</p>
                                @if($webinar->certificate_template_path)
                                    <label class="mt-3 flex items-center gap-2 text-xs font-semibold text-rose-600"><input type="checkbox" name="remove_certificate_template" value="1"> Remove current certificate template</label>
                                @endif
                            </div>
                        </div>
                        <section class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                                <div><h2 class="text-base font-bold text-slate-900">Speakers &amp; Moderators</h2><p class="mt-1 text-xs text-slate-500">Add multiple faculty members and upload an image for each person.</p></div>
                                <div class="flex gap-2"><button type="button" class="btn btn-light faculty-add" data-role="speaker"><i class="bi bi-plus-lg"></i>Add Speaker</button><button type="button" class="btn btn-light faculty-add" data-role="moderator"><i class="bi bi-plus-lg"></i>Add Moderator</button></div>
                            </div>
                            <div id="faculty-list" class="space-y-4">
                                @foreach($facultyRows as $index => $person)
                                    <div class="faculty-row rounded-2xl border border-slate-200 bg-white p-5" data-index="{{ $index }}">
                                        <input type="hidden" name="faculty[{{ $index }}][existing_id]" value="{{ $person['existing_id'] ?? '' }}">
                                        <div class="grid grid-cols-1 gap-5 md:grid-cols-[120px_1fr]">
                                            <div class="text-center"><img class="faculty-preview mx-auto h-24 w-24 rounded-full border-4 border-slate-100 object-cover" src="{{ $person['image_url'] ?? asset('assets/media/avatars/blank.png') }}"><label class="mt-3 block cursor-pointer text-xs font-bold text-cyan-700">Upload image<input type="file" name="faculty[{{ $index }}][image]" class="faculty-image hidden" accept="image/jpeg,image/png,image/webp"></label><label class="mt-2 flex items-center justify-center gap-2 text-[10px] text-slate-500"><input type="checkbox" name="faculty[{{ $index }}][remove_image]" value="1"> Remove image</label></div>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                <div class="fv-row"><label class="mb-2 block text-xs font-bold required">Role</label><select name="faculty[{{ $index }}][role]" class="mh-field faculty-role"><option value="speaker" @selected(($person['role']??'speaker')==='speaker')>Speaker</option><option value="moderator" @selected(($person['role']??'')==='moderator')>Moderator</option></select></div>
                                                <div class="fv-row"><label class="mb-2 block text-xs font-bold required">Name</label><input name="faculty[{{ $index }}][name]" class="mh-field faculty-name" value="{{ $person['name'] ?? '' }}" placeholder="Dr. Full Name"></div>
                                                <div><label class="mb-2 block text-xs font-bold">Designation</label><input name="faculty[{{ $index }}][designation]" class="mh-field" value="{{ $person['designation'] ?? '' }}" placeholder="Cardiologist"></div>
                                                <div><label class="mb-2 block text-xs font-bold">Qualifications</label><input name="faculty[{{ $index }}][qualifications]" class="mh-field" value="{{ $person['qualifications'] ?? '' }}" placeholder="MD, FESC, FACC, FHFA"></div>
                                                <div><label class="mb-2 block text-xs font-bold">Current Position</label><input name="faculty[{{ $index }}][current_position]" class="mh-field" value="{{ $person['current_position'] ?? '' }}" placeholder="Professor of Cardiology"></div>
                                                <div><label class="mb-2 block text-xs font-bold">Institution</label><input name="faculty[{{ $index }}][institution]" class="mh-field" value="{{ $person['institution'] ?? '' }}" placeholder="Institution name"></div>
                                                <div><label class="mb-2 block text-xs font-bold">Country</label><input name="faculty[{{ $index }}][country]" class="mh-field" value="{{ $person['country'] ?? '' }}" placeholder="Philippines"></div>
                                                <div class="md:col-span-2"><label class="mb-2 block text-xs font-bold">Profile</label><textarea name="faculty[{{ $index }}][bio]" class="mh-field min-h-24" placeholder="Professional profile">{{ $person['bio'] ?? '' }}</textarea></div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-right"><button type="button" class="faculty-remove text-xs font-bold text-rose-600"><i class="bi bi-trash me-1"></i>Remove person</button></div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="faculty-empty" class="{{ count($facultyRows) ? 'hidden' : '' }} rounded-xl border border-dashed border-slate-300 p-7 text-center text-xs text-slate-400">No faculty added yet. Add a speaker or moderator.</div>
                        </section>
                        <template id="faculty-template">
                            <div class="faculty-row rounded-2xl border border-slate-200 bg-white p-5" data-index="__INDEX__">
                                <div class="grid grid-cols-1 gap-5 md:grid-cols-[120px_1fr]">
                                    <div class="text-center"><img class="faculty-preview mx-auto h-24 w-24 rounded-full border-4 border-slate-100 object-cover" src="{{ asset('assets/media/avatars/blank.png') }}"><label class="mt-3 block cursor-pointer text-xs font-bold text-cyan-700">Upload image<input type="file" name="faculty[__INDEX__][image]" class="faculty-image hidden" accept="image/jpeg,image/png,image/webp"></label></div>
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div class="fv-row"><label class="mb-2 block text-xs font-bold required">Role</label><select name="faculty[__INDEX__][role]" class="mh-field faculty-role"><option value="speaker">Speaker</option><option value="moderator">Moderator</option></select></div>
                                        <div class="fv-row"><label class="mb-2 block text-xs font-bold required">Name</label><input name="faculty[__INDEX__][name]" class="mh-field faculty-name" placeholder="Dr. Full Name"></div>
                                        <div><label class="mb-2 block text-xs font-bold">Designation</label><input name="faculty[__INDEX__][designation]" class="mh-field" placeholder="Cardiologist"></div>
                                        <div><label class="mb-2 block text-xs font-bold">Qualifications</label><input name="faculty[__INDEX__][qualifications]" class="mh-field" placeholder="MD, FESC, FACC, FHFA"></div>
                                        <div><label class="mb-2 block text-xs font-bold">Current Position</label><input name="faculty[__INDEX__][current_position]" class="mh-field" placeholder="Professor of Cardiology"></div>
                                        <div><label class="mb-2 block text-xs font-bold">Institution</label><input name="faculty[__INDEX__][institution]" class="mh-field" placeholder="Institution name"></div>
                                        <div><label class="mb-2 block text-xs font-bold">Country</label><input name="faculty[__INDEX__][country]" class="mh-field" placeholder="Philippines"></div>
                                        <div class="md:col-span-2"><label class="mb-2 block text-xs font-bold">Profile</label><textarea name="faculty[__INDEX__][bio]" class="mh-field min-h-24" placeholder="Professional profile"></textarea></div>
                                    </div>
                                </div>
                                <div class="mt-3 text-right"><button type="button" class="faculty-remove text-xs font-bold text-rose-600"><i class="bi bi-trash me-1"></i>Remove person</button></div>
                            </div>
                        </template>
                    </div>
                    <div class="card-footer justify-end gap-3">
                        <a href="{{ route('admin.webinars') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="webinar-submit"><span class="indicator-label"><i class="bi bi-floppy"></i>{{ $webinar->exists ? 'Update Webinar' : 'Create Webinar' }}</span><span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm ms-2"></span></span></button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('webinar-form');
            const submit = document.getElementById('webinar-submit');
            const publish = document.getElementById('publish_immediately');
            const scheduled = document.getElementById('scheduled_at');

            const schedulePicker = flatpickr(scheduled, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'd M Y, h:i K',
                minuteIncrement: 5,
                disableMobile: true,
                allowInput: false,
                onChange: function () {
                    validator.revalidateField('scheduled_at');
                }
            });

            const validator = FormValidation.formValidation(form, {
                fields: {
                    title: {
                        validators: {
                            notEmpty: {message: 'Webinar title is required'},
                            stringLength: {max: 255, message: 'Webinar title must not exceed 255 characters'}
                        }
                    },
                    type: {
                        validators: {
                            notEmpty: {message: 'Webinar type is required'}
                        }
                    },
                    country: {
                        validators: {
                            notEmpty: {message: 'Please select a country for this webinar'}
                        }
                    },
                    speciality_id: {
                        validators: {
                            notEmpty: {message: 'Please select a speciality for this webinar'}
                        }
                    },
                    duration_minutes: {
                        validators: {
                            notEmpty: {message: 'Duration is required'},
                            integer: {message: 'Duration must be a whole number'},
                            between: {min: 1, max: 1440, message: 'Duration must be between 1 and 1440 minutes'}
                        }
                    },
                    meeting_url: {
                        validators: {
                            uri: {allowEmptyProtocol: false, message: 'Enter a valid meeting URL'}
                        }
                    },
                    recording_url: {
                        validators: {
                            uri: {allowEmptyProtocol: false, message: 'Enter a valid recording URL'}
                        }
                    },
                    cover_image: {
                        validators: {
                            file: {extension: 'jpg,jpeg,png,webp', type: 'image/jpeg,image/png,image/webp', maxSize: 5242880, message: 'Choose a JPG, PNG or WebP image up to 5 MB'}
                        }
                    },
                    pre_read_url: {
                        validators: {uri: {allowEmptyProtocol: false, message: 'Enter a valid pre-read URL'}}
                    },
                    post_read_url: {
                        validators: {uri: {allowEmptyProtocol: false, message: 'Enter a valid post-read URL'}}
                    },
                    scheduled_at: {
                        validators: {
                            callback: {
                                message: 'Scheduled date and time is required',
                                callback: function (input) {
                                    return publish.checked || input.value.trim() !== '';
                                }
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({rowSelector: '.fv-row'})
                }
            });

            const facultyList = document.getElementById('faculty-list');
            const facultyEmpty = document.getElementById('faculty-empty');
            const facultyTemplate = document.getElementById('faculty-template').innerHTML;
            let facultyIndex = Math.max(0, ...[...document.querySelectorAll('.faculty-row')].map(row => Number(row.dataset.index) + 1));

            const registerFacultyValidation = index => {
                validator.addField(`faculty[${index}][role]`, {validators: {notEmpty: {message: 'Faculty role is required'}}});
                validator.addField(`faculty[${index}][name]`, {validators: {notEmpty: {message: 'Faculty name is required'}}});
            };
            document.querySelectorAll('.faculty-row').forEach(row => registerFacultyValidation(row.dataset.index));

            document.querySelectorAll('.faculty-add').forEach(button => button.addEventListener('click', function () {
                const index = facultyIndex++;
                facultyList.insertAdjacentHTML('beforeend', facultyTemplate.replaceAll('__INDEX__', index));
                const row = facultyList.lastElementChild;
                row.querySelector('.faculty-role').value = this.dataset.role;
                registerFacultyValidation(index);
                facultyEmpty.classList.add('hidden');
                row.scrollIntoView({behavior: 'smooth', block: 'center'});
            }));

            facultyList.addEventListener('change', event => {
                if (!event.target.matches('.faculty-image')) return;
                const file = event.target.files?.[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = loadEvent => event.target.closest('.faculty-row').querySelector('.faculty-preview').src = loadEvent.target.result;
                reader.readAsDataURL(file);
            });

            facultyList.addEventListener('click', event => {
                const button = event.target.closest('.faculty-remove');
                if (!button) return;
                const row = button.closest('.faculty-row');
                const index = row.dataset.index;
                validator.removeField(`faculty[${index}][role]`);
                validator.removeField(`faculty[${index}][name]`);
                row.remove();
                facultyEmpty.classList.toggle('hidden', facultyList.children.length > 0);
            });

            const coverInput = document.getElementById('webinar-cover-image');
            const coverPreview = document.getElementById('webinar-cover-preview');
            const coverFilename = document.getElementById('webinar-cover-filename');
            coverInput.addEventListener('change', function () {
                const file = this.files?.[0];
                if (!file) return;
                coverFilename.textContent = file.name;
                const removeCover = document.getElementById('remove-cover-image');
                if (removeCover) removeCover.checked = false;
                const reader = new FileReader();
                reader.onload = event => coverPreview.src = event.target.result;
                reader.readAsDataURL(file);
                validator.revalidateField('cover_image');
            });

            const syncSchedule = () => {
                scheduled.disabled = publish.checked;
                schedulePicker._input.disabled = publish.checked;
                schedulePicker._input.classList.toggle('cursor-not-allowed', publish.checked);
                schedulePicker._input.classList.toggle('opacity-60', publish.checked);
                if (publish.checked) schedulePicker.clear();
                validator.revalidateField('scheduled_at');
            };

            publish.addEventListener('change', syncSchedule);
            syncSchedule();

            submit.addEventListener('click', function (event) {
                event.preventDefault();
                validator.validate().then(function (status) {
                    if (status !== 'Valid') return;
                    submit.setAttribute('data-kt-indicator', 'on');
                    submit.disabled = true;
                    form.submit();
                });
            });
        });
    </script>
@endpush
