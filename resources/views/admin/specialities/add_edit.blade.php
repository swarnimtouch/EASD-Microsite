@extends('layouts.admin')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content"><div class="post d-flex flex-column-fluid" id="kt_post"><div id="kt_content_container" class="container-xxl">
    @if($errors->any())<div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    @include('admin.partials.form_heading', ['eyebrow'=>'Content Management / '.($speciality->exists ? 'Edit Speciality' : 'New Speciality'),'heading'=>$speciality->exists ? 'Edit Speciality' : 'Create Speciality','description'=>$speciality->exists ? 'Update this speciality and its availability across the platform.' : 'Add a speciality for doctor registration and webinar classification.','backUrl'=>route('admin.specialities'),'backLabel'=>'Back to Specialities'])

    <div class="card admin-form-shell">
        @include('admin.partials.form_card_header', ['icon'=>'bi bi-heart-pulse','title'=>'Speciality Information','subtitle'=>'Fields marked with an asterisk are required'])
        <form method="POST" action="{{ route('admin.specialities.save', $speciality->id) }}" id="speciality-form">@csrf @if($speciality->exists) @method('PUT') @endif
            <div class="card-body space-y-6 p-7 lg:p-8">
                <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Speciality Name</label><input class="mh-field" name="name" value="{{ old('name',$speciality->name) }}" placeholder="e.g. Cardiology"><p class="mt-2 text-[10px] text-slate-400">This name appears in doctor registration and webinar forms.</p></div>
                <div><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600">Description</label><textarea class="mh-field min-h-32 resize-y" name="description" placeholder="Describe this speciality and its clinical focus">{{ old('description',$speciality->description) }}</textarea><p class="mt-2 text-[10px] text-slate-400">Optional internal description for administrators.</p></div>
            </div>
            <div class="card-footer justify-end gap-3"><a class="btn btn-light" href="{{ route('admin.specialities') }}">Cancel</a><button type="submit" class="btn btn-primary" id="speciality-submit"><span class="indicator-label"><i class="bi bi-floppy"></i>{{ $speciality->exists ? 'Update Speciality' : 'Create Speciality' }}</span><span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm ms-2"></span></span></button></div>
        </form>
    </div>
</div></div></div>
@endsection
@push('scripts')
<script>KTUtil.onDOMContentLoaded(function(){const form=document.getElementById('speciality-form'),button=document.getElementById('speciality-submit');const validator=FormValidation.formValidation(form,{fields:{name:{validators:{notEmpty:{message:'Speciality name is required'},stringLength:{max:255,message:'Maximum 255 characters allowed'}}}},plugins:{trigger:new FormValidation.plugins.Trigger(),bootstrap:new FormValidation.plugins.Bootstrap5({rowSelector:'.fv-row'})}});button.addEventListener('click',function(event){event.preventDefault();validator.validate().then(function(status){if(status!=='Valid')return;button.setAttribute('data-kt-indicator','on');button.disabled=true;form.submit();});});});</script>
@endpush
