@extends('layouts.admin')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @include('admin.partials.form_heading', ['eyebrow'=>'User Directory / '.($doctor->exists ? 'Edit HCP' : 'Add New HCP'),'heading'=>$doctor->exists ? 'Edit HCP Profile' : 'Create HCP Profile','description'=>$doctor->exists ? 'Update healthcare professional details and access.' : 'Set up a new healthcare professional account.','backUrl'=>route('admin.doctors'),'backLabel'=>'Back to Doctors'])
                <div class="card admin-form-shell">
                    @include('admin.partials.form_card_header', ['icon'=>'bi bi-person-vcard','title'=>'HCP Information','subtitle'=>'Profile, professional details and account information'])
                            <form method="POST"
                                  action="{{ route('admin.doctors.save',$doctor->id??null) }}"
                                  id="kt_doctor_form"
                                  enctype="multipart/form-data">
                                @csrf
                                @if($doctor->exists)
                                    @method('PUT')
                                @endif
                                <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-3 lg:p-8">
                                    <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                                        <div class="image-input relative mx-auto mb-4 h-32 w-32" data-kt-image-input="true">
                                            <div class="image-input-wrapper h-32 w-32 rounded-full border-4 border-white bg-cover bg-center shadow" style="background-image:url('{{ $doctor->profile_image }}')"></div>
                                            <label class="absolute bottom-0 right-0 flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border-4 border-white bg-cyan-600 text-white shadow transition hover:bg-cyan-700" data-kt-image-input-action="change" title="Change profile image">
                                                <i class="bi bi-camera"></i><input id="profile_image" type="file" name="profile_image" accept="image/png,image/jpeg,image/webp" class="hidden">
                                            </label>
                                            <button type="button" class="absolute right-0 top-0 flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow hover:bg-rose-50 hover:text-rose-600" data-kt-image-input-action="remove" title="Remove profile image"><i class="bi bi-x"></i></button>
                                            <input type="hidden" name="profile_image_remove" value="0">
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-900">Profile Photo</h3><p class="mt-1 text-[10px] leading-5 text-slate-400">PNG, JPG or WebP up to 5MB.<br>Recommended size 400×400px.</p>
                                    </aside>
                                    <div class="lg:col-span-2">
                                        <h3 class="mb-5 border-b border-slate-200 pb-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Basic Information</h3>
                                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Name</label><input type="text" name="name" value="{{ old('name',$doctor->name) }}" class="mh-field" placeholder="Full name"></div>
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Email</label><input type="email" name="email" value="{{ old('email',$doctor->email) }}" class="mh-field" placeholder="Email address"></div>
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Mobile</label><input type="text" name="mobile" value="{{ old('mobile',$doctor->mobile) }}" class="mh-field" placeholder="Mobile number"></div>
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Hospital</label><input type="text" name="hospital" value="{{ old('hospital',$doctor->hospital) }}" class="mh-field" placeholder="Hospital or affiliation"></div>
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Speciality</label><select name="speciality" class="mh-field"><option value="">Select speciality</option>@foreach($specialities as $speciality)<option value="{{ $speciality->name }}" @selected(old('speciality',$doctor->speciality)===$speciality->name)>{{ $speciality->name }}</option>@endforeach</select></div>
                                            <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Medical Registration Number</label><input type="text" name="medical_registration_number" value="{{ old('medical_registration_number',$doctor->medical_registration_number) }}" class="mh-field" placeholder="License or registration number"></div>
                                            <div class="fv-row md:col-span-2"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Country</label><select id="country" name="country" class="mh-field"><option value="">Select Country</option>@foreach($country as $value)<option value="{{ $value->name }}" @selected(old('country',$doctor->country)===$value->name)>{{ $value->name }}</option>@endforeach</select></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer justify-end gap-3">
                                    <a href="{{ route('admin.doctors') }}"
                                       class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="kt_submit">
                                        <span class="indicator-label"><i class="bi bi-floppy"></i>{{ $doctor->exists ? 'Update HCP' : 'Create HCP' }}</span>
                                        <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    </button>
                                </div>
                            </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function () {

            const form = document.querySelector('#kt_doctor_form');
            const submitBtn = document.querySelector('#kt_submit');


            const validator = FormValidation.formValidation(form, {
                fields: {
                    name: {
                        validators: {
                            notEmpty: {message: "Name is required"}
                        }
                    },
                    email: {
                        validators: {
                            notEmpty: {message: "Email is required"},
                            emailAddress: {message: "Enter valid email"},
                            remote: {
                                url: "{{ route('admin.check-email-exists') }}",
                                method: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                data: () => ({
                                    email: form.querySelector('[name="email"]').value,
                                    id: {{ $doctor->id ?? 0 }}
                                }),
                                message: "Email already exists"
                            }
                        }
                    },
                    mobile: {
                        validators: {
                            notEmpty: {message: "Mobile is required"},
                            remote: {
                                url: "{{ route('admin.check-mobile-exists') }}",
                                method: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                data: () => ({
                                    mobile: form.querySelector('[name="mobile"]').value,
                                    id: {{ $doctor->id ?? 0 }}
                                }),
                                message: "Mobile already exists"
                            }
                        }
                    },
                    hospital: {
                        validators: {
                            notEmpty: {message: "Hospital is required"}
                        }
                    },
                    speciality: {
                        validators: {notEmpty: {message: "Speciality is required"}}
                    },
                    medical_registration_number: {
                        validators: {notEmpty: {message: "Medical registration number is required"}}
                    },
                    country: {
                        validators: {
                            notEmpty: {message: "Country is required"},
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
                }
            });

            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();

                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });


        });

        const DEFAULT_AVATAR = "{{ asset('assets/media/avatars/blank.png') }}";

        document.addEventListener('DOMContentLoaded', function () {

            const imageInput = document.querySelector('[data-kt-image-input="true"]');
            if (!imageInput) return;

            const wrapper = imageInput.querySelector('.image-input-wrapper');
            const fileInput = imageInput.querySelector('input[type="file"]');
            const removeInput = imageInput.querySelector('input[name="profile_image_remove"]');

            fileInput?.addEventListener('change', function () {
                const file = this.files?.[0];
                if (!file) return;
                removeInput.value = '0';
                const reader = new FileReader();
                reader.addEventListener('load', event => wrapper.style.backgroundImage = `url('${event.target.result}')`);
                reader.readAsDataURL(file);
            });

            imageInput.querySelector('[data-kt-image-input-action="remove"]')?.addEventListener('click', () => {
                fileInput.value = '';
                removeInput.value = '1';
                wrapper.style.backgroundImage = `url('${DEFAULT_AVATAR}')`;
            });

        });
    </script>
@endpush
