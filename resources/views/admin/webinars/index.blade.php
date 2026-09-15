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
                                <input id="webinar-search" type="text" class="form-control form-control-solid w-250px ps-12" placeholder="Search webinars">
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div id="webinar-toolbar"><a href="{{ route('admin.webinars.add_edit_form') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Webinar</a></div>
                            <div id="webinar-selected-toolbar" class="d-none align-items-center gap-3"><strong><span id="selected-count">0</span> selected</strong><button type="button" id="delete-selected" class="btn btn-danger">Delete Selected</button></div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="webinar-table">
                            <thead><tr class="text-start text-muted fw-bolder fs-7 text-uppercase">
                                <th class="w-10px"><input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#webinar-table .row-checkbox"></th>
                                <th>Title</th><th>Type</th><th>Speaker</th><th>Schedule</th><th>Status</th><th>Actions</th>
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
            const table = $('#webinar-table').DataTable({
                processing: true, serverSide: true, searchDelay: 400,
                ajax: {
                    url: '{{ route('admin.webinars.datatable') }}',
                    data: d => { d.search_term = document.getElementById('webinar-search').value.trim(); },
                    error: function () {
                        document.querySelectorAll('.dataTables_processing').forEach(element => element.style.display = 'none');
                        toastr.error('Unable to load webinars. Please try again.');
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {data:'id', orderable:false, searchable:false, render:id => `<input class="form-check-input row-checkbox" type="checkbox" value="${id}">`},
                    {data:'title', render:value => escapeHtml(value)},
                    {data:'type', render:value => escapeHtml(value)},
                    {data:'speaker_name', render:value => escapeHtml(value || '—')},
                    {data:'scheduled_at', render:(value, type, row) => row.publish_immediately ? '<span class="badge badge-light-success">Immediate</span>' : (value ? new Date(value).toLocaleString() : '—')},
                    {data:'status', render:(value, type, row) => `<label class="form-check form-switch form-check-custom form-check-solid"><input class="form-check-input status-change" data-id="${row.id}" type="checkbox" ${value === 'active' ? 'checked' : ''}></label>`},
                    {data:'id', orderable:false, render:id => `<a href="{{ route('admin.webinars.add_edit_form') }}/${id}" class="btn btn-sm" title="Edit"><i class="bi bi-pencil-fill"></i></a><button class="btn btn-sm delete-webinar" data-id="${id}" title="Delete"><i class="bi bi-trash"></i></button>`}
                ]
            });

            let searchTimer;
            let previousSearch = '';
            $('#webinar-search').on('input', function () {
                const searchValue = this.value.trim();
                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(function () {
                    if (searchValue === previousSearch) return;
                    previousSearch = searchValue;
                    table.ajax.reload(null, true);
                }, 500);
            });

            $('#webinar-table').on('xhr.dt error.dt', function () {
                document.querySelectorAll('.dataTables_processing').forEach(element => element.style.display = 'none');
            });

            const refreshToolbar = () => {
                const count = document.querySelectorAll('.row-checkbox:checked').length;
                document.getElementById('selected-count').textContent = count;
                document.getElementById('webinar-toolbar').classList.toggle('d-none', count > 0);
                document.getElementById('webinar-selected-toolbar').classList.toggle('d-none', count === 0);
                document.getElementById('webinar-selected-toolbar').classList.toggle('d-flex', count > 0);
            };
            document.addEventListener('change', event => {
                if (event.target.matches('.row-checkbox,[data-kt-check="true"]')) refreshToolbar();
                if (!event.target.matches('.status-change')) return;
                const checkbox = event.target;
                $.post('{{ route('admin.webinars.status_change', ':id') }}'.replace(':id', checkbox.dataset.id), {_token:'{{ csrf_token() }}'})
                    .done(() => toastr.success('Status updated successfully'))
                    .fail(() => { checkbox.checked = !checkbox.checked; Swal.fire('Error', 'Unable to update status.', 'error'); });
            });
            document.addEventListener('click', event => {
                const button = event.target.closest('.delete-webinar');
                if (!button) return;
                Swal.fire({text:'Delete this webinar?',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, delete'}).then(result => {
                    if (!result.isConfirmed) return;
                    $.post('{{ route('admin.webinars.delete', ':id') }}'.replace(':id', button.dataset.id), {_token:'{{ csrf_token() }}'}).done(() => table.draw(false));
                });
            });
            document.getElementById('delete-selected').addEventListener('click', function () {
                const ids = [...document.querySelectorAll('.row-checkbox:checked')].map(item => item.value);
                if (!ids.length) return;
                Swal.fire({text:`Delete ${ids.length} selected webinar(s)?`,icon:'warning',showCancelButton:true,confirmButtonText:'Yes, delete'}).then(result => {
                    if (!result.isConfirmed) return;
                    $.ajax({url:'{{ route('admin.webinars.delete_multiple') }}',method:'POST',contentType:'application/json',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},data:JSON.stringify({ids})}).done(() => { table.draw(false); refreshToolbar(); });
                });
            });
        });
    </script>
@endpush
