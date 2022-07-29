@extends('layout.app', ['title' => 'User Activities'])

@push('specific-css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layout.header')
        <!-- ========== App Menu ========== -->
        @include('layout.sidebar', ['title' => 'User Activities'])
            
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">{{ $user->name}}'s Activities</h5>
                        <div>
                            <button onclick="showAddModal()" class="btn btn-primary">Add New Activity</button>
                        </div>
                    </div>
                    <div class="card-body">

                        <table id="fixed-header" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Activity ID</th>
                                    <th>Activity Title</th>
                                    <th>Activity Image</th>
                                    <th>Start Date</th>
                                    <th>Activity Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user->activities as $key => $activity)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{ucfirst($activity->event_title)}}</td>
                                        <td><img src="{{$activity->event_image}}" alt="" class="rounded avatar-xl"></td>
                                        <td>{{date('D, F jS Y', strtotime($activity->start_date))}}</td>
                                        <td>{{$activity->description}}</td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item edit-item-btn" onclick="showEditModal(this)" 
                                                                data-id="{{$activity->id}}" data-userId="{{$user->id}}" data-eventTitle="{{$activity->event_title}}"
                                                                data-eventDescription="{{$activity->description}}" data-eventStartDate="{{$activity->start_date}}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item remove-item-btn" onclick="deleteActivity(this)" data-id="{{$activity->id}}" data-userId="{{$user->id}}">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Add New Event MODAL -->
                <div class="modal fade" id="event-modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0">
                            <div class="modal-header p-3 bg-soft-info">
                                <h5 class="modal-title" id="modal-title">Activity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form method="POST" action="{{route('activity')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row event-form">
                                        <!--end col-->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Activity Title</label>
                                                <input class="form-control" placeholder="Enter event title" type="text" name="event_title" id="event-title" required  />
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label>Activity Image</label>
                                                <div class="input-group">
                                                    <input type="file" name="event_image" class="form-control" placeholder="Select file" required>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        
                                        <input type="text"  id="user" name="user" value="{{$user->id}}" hidden/>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description" placeholder="Enter a description" rows="3" spellcheck="false"></textarea>
                                            </div>
                                        </div>
    
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Start Date</label>
                                                <input class="form-control flatpickr-input" data-provider="flatpickr" data-date-format="Y-m-d" data-min-date="today" placeholder="Select date" type="date" name="start_date" id="start_date" required  />
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-success" id="btn-save-event">Add Activity</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end modal-content-->
                    </div> <!-- end modal dialog-->
                </div> <!-- end modal-->
    
                <!-- Add New Event MODAL -->
                <div class="modal fade" id="event-edit-modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0">
                            <div class="modal-header p-3 bg-soft-info">
                                <h5 class="modal-title" id="modal-title">Activity</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form method="POST" action="{{route('editUserActivity')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row event-form">
                                        <!--end col-->
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Activity Title</label>
                                                <input class="form-control" placeholder="Enter event title" type="text" name="event_title" id="edit-event-title" required  />
                                            </div>
                                        </div>
                                        <!--end col-->
                                        
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label>Event Image</label>
                                                <div class="input-group">
                                                    <input type="file" name="event_image" class="form-control" placeholder="Select file">
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label>Start Date</label>
                                                <div class="input-group">
                                                    <input class="form-control flatpickr-input" data-provider="flatpickr" data-date-format="Y-m-d" data-min-date="today" placeholder="Select date" type="date" name="start_date" id="edit-start-date" required  />
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description" placeholder="Enter a description" rows="3" spellcheck="false" id="edit-description"></textarea>
                                            </div>
                                        </div>
                                        <input type="text" name="id" id="event-id-tag" hidden>
                                        <input type="text" name="user" id="event-userid-tag" hidden>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-success" id="btn-save-event">Update Activity</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end modal-content-->
                    </div> <!-- end modal dialog-->
                </div> <!-- end modal-->
            </div><!--end col-->

        </div><!--end row-->


        @include('layout.footer')
    </div>
    <!-- END layout-wrapper -->
@endsection

    
@push('specific-js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

        <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- init js -->
    <script src="{{ asset('assets/js/pages/form-pickers.init.js') }}"></script>
    <script>
        function deleteActivity(e) {
            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            let activityId = $(e).attr("data-id");
            let userId = $(e).attr("data-userId");
            Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: !0,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
                    cancelButtonClass: "btn btn-danger w-xs mt-2",
                    buttonsStyling: !1,
                    showCloseButton: !0,
                }).then(function (t) {
                    if (t.value) {
                        $.ajax({
                            url: SITEURL + '/delete/user/activity',
                            data: {
                                id: activityId,
                                user_id: userId
                            },
                            type: "DELETE",
                            success: function (response) {
                                displayMessage("Deleted Successfully");
                                location.reload();
                            }
                        })   

                    } else {
                        Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        confirmButtonClass: "btn btn-primary mt-2",
                        buttonsStyling: !1,
                        });
                    }
                });
        }

        function showAddModal() {
            var g = new bootstrap.Modal(document.getElementById("event-modal"), {
                        keyboard: !1,
                    });
                    g.show();
        }
        
        function showEditModal(e) {
            document.getElementById("event-id-tag").value = $(e).attr("data-id");
            document.getElementById("edit-event-title").value = $(e).attr("data-eventTitle");
            document.getElementById("edit-description").value = $(e).attr("data-eventDescription");
            document.getElementById("edit-start-date").value = $(e).attr("data-eventStartDate");
            document.getElementById("event-userid-tag").value = $(e).attr("data-userId");
            var g = new bootstrap.Modal(document.getElementById("event-edit-modal"), {
                        keyboard: !1,
                    });
                    g.show();
        }

        function displayMessage(message) {
            Toastify({
                    text: message,
                    duration: 3000,
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                }).showToast();           
        }
    </script>
@endpush