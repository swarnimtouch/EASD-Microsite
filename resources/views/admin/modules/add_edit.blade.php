@extends('layouts.admin')
@push('styles')
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css">
<style>.ck-editor__editable_inline{min-height:220px}.module-dropzone.dragging{border-color:#0891b2;background:#ecfeff}.flatpickr-calendar{z-index:9999!important;border:1px solid #e2e8f0!important;border-radius:16px!important;box-shadow:0 18px 45px rgba(15,23,42,.16)!important;overflow:hidden}.flatpickr-day{border-radius:9px}.flatpickr-day.selected,.flatpickr-day.selected:hover{background:#0891b2!important;border-color:#0891b2!important}.flatpickr-day.today{border-color:#0891b2;color:#0891b2}.flatpickr-time{border-top-color:#e2e8f0}.flatpickr-input{cursor:pointer}</style>
@endpush
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content"><div class="post d-flex flex-column-fluid" id="kt_post"><div id="kt_content_container" class="container-xxl">
  @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  @include('admin.partials.form_heading', ['eyebrow' => 'Learning Management / '.($module->exists?'Edit Module':'New Module'), 'heading' => $module->exists?'Edit Module':'Create Module', 'description' => 'Define the module now; chapters and quizzes can be connected to it in the next phase.', 'backUrl' => route('admin.modules'), 'backLabel' => 'Back to Modules'])
  <div class="card admin-form-shell"><div class="card-header">@include('admin.partials.form_card_header', ['icon' => 'bi bi-journal-richtext', 'title' => 'Module Details', 'subtitle' => 'Fields marked with an asterisk are required'])</div>
  <form id="module-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.modules.save', $module->exists?$module->id:null) }}">@csrf @if($module->exists)@method('PUT')@endif
    <div class="card-body space-y-6">
      <div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Module Title</label><input class="mh-field" name="title" value="{{ old('title',$module->title) }}" placeholder="e.g. Adult Asthma & Phenotypes"></div>
      <div><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600">Description</label><textarea id="module-description" name="description">{{ old('description',$module->description) }}</textarea></div>
      <div class="grid gap-5 md:grid-cols-3"><div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Sequence Order</label><input type="number" min="1" class="mh-field" name="sequence_order" value="{{ old('sequence_order',$module->sequence_order) }}"></div><div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Content Type</label><select class="mh-field" name="content_type"><option value="pdf" @selected(old('content_type',$module->content_type)==='pdf')>PDF Document</option><option value="external_url" @selected(old('content_type',$module->content_type)==='external_url')>External URL</option></select></div><div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600">Release Date & Time</label><div class="relative"><input id="release-at" type="text" class="mh-field pr-11" name="release_at" value="{{ old('release_at',$module->release_at?->format('Y-m-d H:i')) }}" placeholder="Select date and time" autocomplete="off"><i class="bi bi-calendar3 pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i></div></div></div>
      <div><label class="mb-3 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Module Content</label><div class="mb-4 inline-flex rounded-xl bg-slate-100 p-1"><button type="button" data-source="upload" class="source-tab rounded-lg px-4 py-2 text-xs font-bold"><i class="bi bi-upload me-2"></i>Upload File</button><button type="button" data-source="url" class="source-tab rounded-lg px-4 py-2 text-xs font-bold"><i class="bi bi-link-45deg me-2"></i>Provide URL</button></div><input id="content-source" type="hidden" name="content_source" value="{{ old('content_source',$module->content_source) }}">
        <div id="upload-panel" class="fv-row"><label for="module-file" class="module-dropzone flex min-h-48 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-8 text-center"><i class="bi bi-file-earmark-pdf text-4xl text-slate-400"></i><strong id="file-name" class="mt-4 text-sm text-slate-700">{{ $module->original_file_name ?: 'Click or drag PDF file to upload' }}</strong><span class="mt-2 text-xs text-slate-400">Accepted: PDF · Maximum 200MB</span></label><input id="module-file" type="file" name="module_file" accept="application/pdf" class="hidden"></div>
        <div id="url-panel" class="fv-row hidden"><input type="url" class="mh-field" name="content_url" value="{{ old('content_url',$module->content_url) }}" placeholder="https://example.com/module-resource"></div>
      </div>
      <div class="grid gap-5 md:grid-cols-2"><div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Minimum Viewing Time (minutes)</label><input type="number" min="0" max="1440" class="mh-field" name="minimum_viewing_minutes" value="{{ old('minimum_viewing_minutes',$module->minimum_viewing_minutes) }}"></div><div class="fv-row"><label class="mb-2 block text-[10px] font-bold uppercase tracking-wider text-slate-600 required">Status</label><select class="mh-field" name="status"><option value="draft" @selected(old('status',$module->status)==='draft')>Draft</option><option value="active" @selected(old('status',$module->status)==='active')>Active</option><option value="inactive" @selected(old('status',$module->status)==='inactive')>Inactive</option></select></div></div>
    </div><div class="card-footer"><a href="{{ route('admin.modules') }}" class="mh-btn-secondary">Cancel</a><button type="submit" class="mh-btn-primary"><i class="bi bi-floppy me-2"></i>{{ $module->exists?'Update Module':'Create Module' }}</button></div>
  </form></div>
</div></div></div>
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
<script>
KTUtil.onDOMContentLoaded(function () {
    const form = document.getElementById('module-form');
    const sourceInput = document.getElementById('content-source');
    const uploadPanel = document.getElementById('upload-panel');
    const urlPanel = document.getElementById('url-panel');
    const fileInput = document.getElementById('module-file');
    const dropzone = document.querySelector('.module-dropzone');
    const fileName = document.getElementById('file-name');
    const hasExistingFile = @json((bool) $module->file_path);

    ClassicEditor.create(document.querySelector('#module-description')).catch(console.error);

    flatpickr(document.getElementById('release-at'), {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        altInput: true,
        altFormat: 'd M Y, h:i K',
        minuteIncrement: 5,
        allowInput: false,
        disableMobile: true
    });

    const validator = FormValidation.formValidation(form, {
        fields: {
            title: { validators: {
                notEmpty: { message: 'Module title is required' },
                stringLength: { max: 255, message: 'Module title cannot exceed 255 characters' }
            }},
            sequence_order: { validators: {
                notEmpty: { message: 'Sequence order is required' },
                integer: { message: 'Enter a valid sequence number' },
                greaterThan: { min: 1, message: 'Sequence order must be at least 1' }
            }},
            content_type: { validators: {
                notEmpty: { message: 'Content type is required' }
            }},
            content_url: { validators: {
                callback: {
                    message: 'Enter a valid HTTP or HTTPS content URL',
                    callback: function (input) {
                        if (sourceInput.value !== 'url') return true;
                        try {
                            return ['http:', 'https:'].includes(new URL(input.value).protocol);
                        } catch (error) {
                            return false;
                        }
                    }
                }
            }},
            module_file: { validators: {
                callback: {
                    message: 'Please upload a PDF file up to 200MB',
                    callback: function () {
                        if (sourceInput.value !== 'upload') return true;
                        const file = fileInput.files[0];
                        if (!file) return hasExistingFile;
                        return file.type === 'application/pdf' && file.size <= 209715200;
                    }
                }
            }},
            minimum_viewing_minutes: { validators: {
                notEmpty: { message: 'Minimum viewing time is required' },
                integer: { message: 'Enter viewing time in whole minutes' },
                between: { min: 0, max: 1440, message: 'Viewing time must be between 0 and 1440 minutes' }
            }},
            status: { validators: {
                notEmpty: { message: 'Status is required' }
            }}
        },
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({ rowSelector: '.fv-row' })
        }
    });

    function selectSource(value) {
        sourceInput.value = value;
        uploadPanel.classList.toggle('hidden', value !== 'upload');
        urlPanel.classList.toggle('hidden', value !== 'url');
        document.querySelectorAll('.source-tab').forEach(function (tab) {
            const active = tab.dataset.source === value;
            tab.classList.toggle('bg-white', active);
            tab.classList.toggle('text-cyan-700', active);
            tab.classList.toggle('shadow-sm', active);
            tab.classList.toggle('text-slate-500', !active);
        });
        validator.revalidateField('content_url');
        validator.revalidateField('module_file');
    }

    document.querySelectorAll('.source-tab').forEach(function (tab) {
        tab.addEventListener('click', function () { selectSource(tab.dataset.source); });
    });
    selectSource(sourceInput.value || 'upload');

    fileInput.addEventListener('change', function () {
        if (fileInput.files[0]) fileName.textContent = fileInput.files[0].name;
        validator.revalidateField('module_file');
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add('dragging');
        });
    });
    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove('dragging');
        });
    });
    dropzone.addEventListener('drop', function (event) {
        if (!event.dataTransfer.files.length) return;
        fileInput.files = event.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        validator.validate().then(function (status) {
            if (status !== 'Valid') return;
            form.querySelector('button[type="submit"]').disabled = true;
            form.submit();
        });
    });
});
</script>
@endpush
