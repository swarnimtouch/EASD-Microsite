@extends('layouts.admin')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content"><div class="post d-flex flex-column-fluid" id="kt_post"><div id="kt_content_container" class="container-xxl">
    @if($errors->any())<div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    @include('admin.partials.form_heading', ['eyebrow'=>'Content Management / '.($country->exists ? 'Edit Country' : 'New Country'),'heading'=>$country->exists ? 'Edit Country' : 'Create Country','description'=>'Active countries appear automatically in user registration and webinar forms.','backUrl'=>route('admin.countries'),'backLabel'=>'Back to Countries'])

    <div class="card admin-form-shell">
        @include('admin.partials.form_card_header', ['icon'=>'bi bi-globe-asia-australia','title'=>'Country Information','subtitle'=>'Fields marked with an asterisk are required'])
        <form method="POST" action="{{ route('admin.countries.save', $country->id) }}" id="country-form">@csrf @if($country->exists) @method('PUT') @endif
            <div class="card-body grid gap-6 p-7 md:grid-cols-2 lg:p-8">
                <div class="fv-row md:col-span-2"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Country Name</label><input class="mh-field" name="name" value="{{ old('name',$country->name) }}" placeholder="e.g. Malaysia"><p class="mt-2 text-[10px] text-slate-400">This name appears in registration and webinar country dropdowns.</p></div>
                <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">ISO 2 Code</label><input class="mh-field uppercase" name="iso2" maxlength="2" value="{{ old('iso2',$country->iso2) }}" placeholder="MY"></div>
                <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">ISO 3 Code</label><input class="mh-field uppercase" name="iso3" maxlength="3" value="{{ old('iso3',$country->iso3) }}" placeholder="MYS"></div>
                <div class="fv-row md:col-span-2"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600">Phone Code</label><input class="mh-field" name="phonecode" value="{{ old('phonecode',$country->phonecode) }}" placeholder="60"></div>
            </div>
            <div class="card-footer justify-end gap-3"><a class="btn btn-light" href="{{ route('admin.countries') }}">Cancel</a><button type="submit" class="btn btn-primary" id="country-submit"><span class="indicator-label"><i class="bi bi-floppy"></i>{{ $country->exists ? 'Update Country' : 'Create Country' }}</span><span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm ms-2"></span></span></button></div>
        </form>
    </div>
</div></div></div>
@endsection
@push('scripts')
<script>KTUtil.onDOMContentLoaded(function(){const form=document.getElementById('country-form'),button=document.getElementById('country-submit');const validator=FormValidation.formValidation(form,{fields:{name:{validators:{notEmpty:{message:'Country name is required'},stringLength:{max:255,message:'Maximum 255 characters allowed'}}},iso2:{validators:{notEmpty:{message:'ISO 2 code is required'},stringLength:{min:2,max:2,message:'Enter exactly 2 characters'}}},iso3:{validators:{notEmpty:{message:'ISO 3 code is required'},stringLength:{min:3,max:3,message:'Enter exactly 3 characters'}}}},plugins:{trigger:new FormValidation.plugins.Trigger(),bootstrap:new FormValidation.plugins.Bootstrap5({rowSelector:'.fv-row'})}});button.addEventListener('click',function(event){event.preventDefault();validator.validate().then(function(status){if(status!=='Valid')return;button.setAttribute('data-kt-indicator','on');button.disabled=true;form.submit();});});});</script>
@endpush
