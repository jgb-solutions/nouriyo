@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Admins</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                        Add an Admin
                    </button>
                    <div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog"
                         aria-labelledby="addAdminModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addAdminModalLabel">Add a new Admin</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.admins')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.admin-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Admin</button>
                                    </form>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('inc.errors')

        @if($admins->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showAdminModal-{{$admin->id}}">
                                    {{$admin->fullName}}
                                </button>
                                <div class="modal fade" id="showAdminModal-{{$admin->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showAdminModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showAdminModalLabel">{{$admin->fullName}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div><img height="200" src="{{$admin->image_url}}"/></div>
                                                <p>
                                                    {{$admin->description}}
                                                </p>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editAdminModal-{{$admin->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editAdminModal-{{$admin->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editAdminModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAdminModalLabel">
                                                        Edit {{$admin->fullName}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-admin', $admin->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.admin-form-inputs', ['admin' => $admin])
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </form>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="post" action="{{route('dashboard.delete-admin', $admin->id)}}">
                                        @method("delete")
                                        @csrf
                                        <button onclick='return confirm("Are you sure?")' type="submit"
                                                class="btn btn-danger">Delete
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $admins->links() }}
        @endif
    </main>
@endsection