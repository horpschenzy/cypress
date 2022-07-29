@extends('layout.app', ['title' => 'Activities'])

@push('specific-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layout.header')
        <!-- ========== App Menu ========== -->
        @include('layout.sidebar', ['title' => 'Activities'])
            
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-xl-3">
                        <div>
                            <h5 class="mb-1">Upcoming Events</h5>
                            <p class="text-muted">Don't miss scheduled events</p>
                            <div class="pe-2 me-n1 mb-3" data-simplebar style="height: 400px">
                                <div id="upcoming-event-list">
                                    @forelse ($activities as $activity)
                                        <div class="card mb-3">                        
                                            <div class="card-body">                            
                                                <div class="d-flex mb-3">                                
                                                    <div class="flex-grow-1"><i class="mdi mdi-checkbox-blank-circle me-2 text-info"></i><span class="fw-medium">{{ date('d-m-Y', strtotime($activity->start_date))}} </span></div>          
                                                </div>                            
                                                <h6 class="card-title fs-16"> {{$activity->event_title}}</h6>                            
                                                <p class="text-muted text-truncate-two-lines mb-0"> {{$activity->description}}</p>                        
                                            </div>                    
                                        </div>
                                    @empty
                                    <div class="card mb-3">                        
                                        <div class="card-body">  
                                            <h6 class="card-title fs-16"> No upcoming events</h6> 
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body bg-soft-info">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i data-feather="calendar" class="text-info icon-dual-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fs-15">Welcome to your Calendar!</h6>
                                        <p class="text-muted mb-0">Event that applications book will appear here. Click on an event to see the details and manage applicants event.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-9">
                        <div class="card card-h-100">
                            <div class="card-body">
                                <div id='full_calendar_events'></div>
                            </div>
                        </div>
                    </div><!-- end col -->
                </div>
                <!--end row-->

                <div style='clear:both'></div>

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
                                        
                                        <input type="text"  id="start_date" name="start_date"  hidden/>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description" placeholder="Enter a description" rows="3" spellcheck="false"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Users</label>
                                                <select class="form-select" name="user"  required>
                                                    <option value="ALL USERS">ALL USERS</option>
                                                    @foreach ($users as $user)
                                                     <option value="{{$user->id}}">{{$user->name}}</option>
                                                    @endforeach
                                                </select>
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
                                <form method="POST" action="{{route('editActivity')}}" enctype="multipart/form-data">
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
                                        
                                        <input type="text"  id="start_date" name="start_date"  hidden/>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description" placeholder="Enter a description" rows="3" spellcheck="false" id="edit-description"></textarea>
                                            </div>
                                        </div>
                                        <input type="text" name="id" id="event-id-tag" hidden>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Users</label>
                                                <select class="form-select" name="user" id="edit-user-selection" required>
                                                    <option value="ALL USERS">ALL USERS</option>
                                                    @foreach ($users as $user)
                                                     <option value="{{$user->id}}">{{$user->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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

                <div class="modal fade" id="event-view-modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0">
                            <div class="modal-header p-3 bg-soft-info">
                                <h5 class="modal-title" id="modal-title">Event</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                            </div>
                            <div class="modal-body p-4">
                                <form class="needs-validation" name="event-form" id="form-event" novalidate>
                                    <div class="text-end">
                                        <a href="#" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="edit-event" onclick="editActivity()" role="button">Edit</a>
                                    </div>
                                    <div class="event-details">
                                        <div class="d-flex mb-2">
                                            <div class="flex-grow-1 d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="ri-calendar-event-line text-muted fs-16"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag"></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="ri-discuss-line text-muted fs-16"></i>
                                            </div>
                                            <div class="flex-grow-1" id="event-title-tag">
                                                
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="ri-discuss-line text-muted fs-16"></i>
                                            </div>
                                            <div class="flex-grow-1" id="event-image-tag">
                                                
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="ri-discuss-line text-muted fs-16"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="d-block text-muted mb-0" id="event-description-tag"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" name="id" id="event-id-tag" hidden>
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-soft-danger" onclick="deleteActivity()"><i class="ri-close-line align-bottom"></i> Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end modal-content-->
                    </div> <!-- end modal dialog-->
                </div>
            </div>
        </div> <!-- end row-->

        @include('layout.footer')
    </div>
    <!-- END layout-wrapper -->
@endsection

    
@push('specific-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
 <!-- Sweet Alerts js -->
 <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

 <!-- Sweet alert init js-->
 <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>
    <script>
        // $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }
            });
            var calendar = $('#full_calendar_events').fullCalendar({
                editable: true,
                editable: true,
                events: SITEURL + "/fetch-activities",
                displayEventTime: true,
                eventRender: function (event, element, view) {
                    if (event.allDay === 'true') {
                        event.allDay = true;
                    } else {
                        event.allDay = false;
                    }
                },
                selectable: true,
                selectAllow: function(select) {
                    return moment().diff(select.start, 'days') <= 0
                },
                selectHelper: true,
                select: function (event_start, event_end, allDay) {
                    var event_start = $.fullCalendar.formatDate(event_start, "Y-MM-DD");
                    document.getElementById("start_date").value = event_start;
                    var g = new bootstrap.Modal(document.getElementById("event-modal"), {
                        keyboard: !1,
                    });
                    g.show();
                },
                eventDrop: function (event, delta) {
                    var event_start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    $.ajax({
                        url: SITEURL + '/edit/activity/date',
                        data: {
                            start: event_start,
                            id: event.id
                        },
                        type: "POST",
                        success: function (response) {
                            displayMessage('moved successfully')
                        }
                    });
                },
                eventClick: function (event) {
                    console.log(event);
                    document.getElementById("event-title-tag").innerHTML = event.title;
                    document.getElementById("event-description-tag").innerHTML = event.description;
                    document.getElementById("event-image-tag").innerHTML = '<img class="img-thumbnail" alt="200x200" width="200" src="'+event.event_image+'">';
                    document.getElementById("event-start-date-tag").innerHTML = moment(event.start).format("ddd, MMMM Do YYYY");
                    document.getElementById("event-id-tag").value = event.id;
                    document.getElementById("edit-event-title").value = event.title;
                    document.getElementById("edit-description").value = event.description;
                    var selection = $('#edit-user-selection').val(event.user);
                    $("option[value=' + selection + ']").attr('selected',true);
                    var g = new bootstrap.Modal(document.getElementById("event-view-modal"), {
                        keyboard: !1,
                    });
                    g.show();
                }
            });
            function displayMessage(message) {
                Toastify({
                        text: message,
                        duration: 3000,
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                    }).showToast();           
            }
    
            function editActivity(){
                $('#event-view-modal').modal('toggle');
                var g = new bootstrap.Modal(document.getElementById("event-edit-modal"), {
                        keyboard: !1,
                    });
                    g.show();
            }

            function deleteActivity(){
                var id = document.getElementById("event-id-tag").value;
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
                            url: SITEURL + '/delete/activity',
                            data: {
                                id: id
                            },
                            type: "DELETE",
                            success: function (response) {
                                calendar.fullCalendar('removeEvents', id);
                                displayMessage("Deleted Successfully");
                                $('#event-view-modal').modal('toggle');
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
        // });
    </script>
@endpush