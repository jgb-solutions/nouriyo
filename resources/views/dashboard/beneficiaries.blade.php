@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Beneficiaries</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBeneficiaryModal">
                        Add a Beneficiary
                    </button>
                    <div class="modal fade" id="addBeneficiaryModal" tabindex="-1" role="dialog"
                         aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addBeneficiaryModalLabel">Add a new Beneficiary</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.add-beneficiaries')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.beneficiary-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Beneficiary</button>
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

        @if($beneficiaries->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Orders</th>
                        @if(auth()->user()->admin)
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($beneficiaries as $beneficiary)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showBeneficiaryModal-{{$beneficiary->id}}">
                                    {{$beneficiary->fullName}}
                                </button>
                                <div class="modal fade" id="showBeneficiaryModal-{{$beneficiary->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showBeneficiaryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showBeneficiaryModalLabel">{{$beneficiary->fullName}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div><img height="200" src="{{$beneficiary->image_url}}"/></div>
                                                <p>
                                                    {{$beneficiary->description}}
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
                            <td>{{$beneficiary->phone}}</td>
                            <td>{{$beneficiary->address}}</td>
                            <td>{{$beneficiary->orders_count}}</td>
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editBeneficiaryModal-{{$beneficiary->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editBeneficiaryModal-{{$beneficiary->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editBeneficiaryModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBeneficiaryModalLabel">
                                                        Edit {{$beneficiary->fullName}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-beneficiary', $beneficiary->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.beneficiary-form-inputs', ['beneficiary' => $beneficiary])
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
                                    <form method="post" action="{{route('dashboard.delete-beneficiary', $beneficiary->id)}}">
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
            {{ $beneficiaries->links() }}
        @endif
    </main>
@endsection