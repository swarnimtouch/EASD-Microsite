@extends('layouts.admin')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="bi bi-search position-absolute ms-5"></i>
                            <input id="module-search" type="text" class="form-control form-control-solid w-250px ps-12" placeholder="Search modules">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div id="module-toolbar">
                            <a href="{{ route('admin.modules.add_edit_form') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Module</a>
                        </div>
                        <div id="module-selected-toolbar" class="d-none align-items-center gap-3">
                            <strong><span id="module-selected-count">0</span> selected</strong>
                            <button type="button" id="delete-selected-modules" class="btn btn-danger">Delete Selected</button>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="module-table">
                        <thead><tr class="text-start text-muted fw-bolder fs-7 text-uppercase">
                            <th class="w-10px"><input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#module-table .row-checkbox"></th>
                            <th>Order</th><th>Module</th><th>Content</th><th>Release</th><th>Viewing Time</th><th>Status</th><th>Actions</th>
                        </tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
KTUtil.onDOMContentLoaded(function () {
    const escapeHtml = value => $('<div>').text(value ?? '').html();
    const table = $('#module-table').DataTable({
        processing: true,
        serverSide: true,
        searchDelay: 400,
        ajax: {
            url: '{{ route('admin.modules.datatable') }}',
            data: data => { data.search_term = document.getElementById('module-search').value.trim(); },
            error: function () {
                document.querySelectorAll('.dataTables_processing').forEach(element => element.style.display = 'none');
                toastr.error('Unable to load modules. Please try again.');
            }
        },
        order: [[1, 'asc']],
        columns: [
            {data:'id', orderable:false, searchable:false, render:id => `<input class="form-check-input row-checkbox" type="checkbox" value="${id}">`},
            {data:'sequence_order'},
            {data:'title', render:(value, type, row) => `<div><strong>${escapeHtml(value)}</strong><div class="mt-1 text-muted fs-8">${escapeHtml(row.slug)}</div></div>`},
            {data:'content_type', render:(value, type, row) => `<span class="badge badge-light-primary">${escapeHtml(value === 'pdf' ? 'PDF Document' : 'External URL')}</span><div class="mt-1 text-muted fs-8">${escapeHtml(row.content_source)}</div>`},
            {data:'release_at', render:value => value ? new Date(value).toLocaleString() : '—'},
            {data:'minimum_viewing_minutes', render:value => `${value} mins`},
            {data:'status', render:(value, type, row) => `<label class="form-check form-switch form-check-custom form-check-solid"><input class="form-check-input module-status" data-id="${row.id}" type="checkbox" ${value === 'active' ? 'checked' : ''}></label>`},
            {data:'id', orderable:false, render:id => `<a href="{{ route('admin.modules.add_edit_form') }}/${id}" class="btn btn-sm" title="Edit"><i class="bi bi-pencil-fill"></i></a><button class="btn btn-sm delete-module" data-id="${id}" title="Delete"><i class="bi bi-trash"></i></button>`}
        ]
    });

    let searchTimer;
    let previousSearch = '';
    $('#module-search').on('input', function () {
        const searchValue = this.value.trim();
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(function () {
            if (searchValue === previousSearch) return;
            previousSearch = searchValue;
            table.ajax.reload(null, true);
        }, 500);
    });

    $('#module-table').on('xhr.dt error.dt', function () {
        document.querySelectorAll('.dataTables_processing').forEach(element => element.style.display = 'none');
    });

    const refreshToolbar = function () {
        const count = document.querySelectorAll('#module-table .row-checkbox:checked').length;
        document.getElementById('module-selected-count').textContent = count;
        document.getElementById('module-toolbar').classList.toggle('d-none', count > 0);
        document.getElementById('module-selected-toolbar').classList.toggle('d-none', count === 0);
        document.getElementById('module-selected-toolbar').classList.toggle('d-flex', count > 0);
    };

    document.addEventListener('change', function (event) {
        if (event.target.matches('#module-table .row-checkbox,#module-table [data-kt-check="true"]')) refreshToolbar();
        if (!event.target.matches('.module-status')) return;
        const checkbox = event.target;
        $.post('{{ route('admin.modules.status_change', ':id') }}'.replace(':id', checkbox.dataset.id), {_token:'{{ csrf_token() }}'})
            .done(() => toastr.success('Status updated successfully'))
            .fail(() => { checkbox.checked = !checkbox.checked; Swal.fire('Error', 'Unable to update status.', 'error'); });
    });

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.delete-module');
        if (!button) return;
        Swal.fire({text:'Delete this module and its uploaded content?', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete'}).then(result => {
            if (!result.isConfirmed) return;
            $.post('{{ route('admin.modules.delete', ':id') }}'.replace(':id', button.dataset.id), {_token:'{{ csrf_token() }}'}).done(() => table.draw(false));
        });
    });

    document.getElementById('delete-selected-modules').addEventListener('click', function () {
        const ids = [...document.querySelectorAll('#module-table .row-checkbox:checked')].map(item => item.value);
        if (!ids.length) return;
        Swal.fire({text:`Delete ${ids.length} selected module(s) and their uploaded content?`, icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete'}).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({url:'{{ route('admin.modules.delete_multiple') }}', method:'POST', contentType:'application/json', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, data:JSON.stringify({ids})})
                .done(() => { table.draw(false); refreshToolbar(); });
        });
    });
});
</script>
@endpush
