@extends('layout.app', ['title' => 'Users'])

@section('content')
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layout.header')
        <!-- ========== App Menu ========== -->
        @include('layout.sidebar', ['title' => 'Users'])
            
        <div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-1">
            @forelse ($users as $user)
                
            <div class="col">
                <div class="card card-body">
                    <div class="d-flex mb-4 align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="" class="avatar-sm rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h5 class="card-title mb-1">{{ucfirst($user->name)}}</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted">{{$user->email}}</p>
                    <h6 class="mb-1">Total Activities: {{ count($user->activities)}}</h6>
                    <a href="{{ route('viewUser', $user->id)}}" class="btn btn-primary btn-sm">See Activities</a>
                </div>
            </div><!-- end col -->
            @empty
                <h3>No Users</h3>
            @endforelse
        </div><!-- end row -->

        @include('layout.footer')
    </div>
    <!-- END layout-wrapper -->
@endsection

    
@push('specific-js')
    <!-- prismjs plugin -->
    <script src="{{ asset('assets/libs/prismjs/prism.js') }}"></script>

    <!-- Masonry plugin -->
    <script src="{{ asset('assets/libs/masonry-layout/masonry.pkgd.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/card.init.js') }}"></script>
@endpush