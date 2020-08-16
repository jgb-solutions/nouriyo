@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Packages</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPackageModal">Add
                        a package
                    </button>
                    <div class="modal fade" id="addPackageModal" tabindex="-1" role="dialog"
                         aria-labelledby="addPackageModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addPackageModalLabel">Add a new Package</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.packages')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.package-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Package</button>
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

        @if($packages->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Products</th>
                        @if(auth()->user()->admin)
                            <th>Price</th>
                        @endif
                        @if(auth()->user()->admin)
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($packages as $package)
                        <tr>
                            <td>
                                <img width="50"
                                     src="{{ $package->image ? $package->image : 'https://via.placeholder.com/50C/O'}}"/>
                            </td>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showPackageModal-{{$package->id}}">
                                    {{$package->name}}
                                </button>
                                <div class="modal fade" id="showPackageModal-{{$package->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showPackageModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showPackageModalLabel">Package's details</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Info</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        <h6 class="mb-1">Name: <b>{{$package->name}}</b></h6>
                                                        <h6 class="mb-1">Quantity: <b>{{$package->quantity}}</b></h6>
                                                        <h6 class="mb-1">Price: <b>{{$package->price}}
                                                                dollars</b>
                                                        </h6>
                                                        <h6 class="mb-1">Description:
                                                            <b>{{$package->description}}</b>
                                                        </h6>
                                                    </div>
                                                </div>

                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Products</div>
                                                        @foreach($package->products as $product)
                                                    <div class="list-group-item list-group-item-action">
                                                            <div class="d-flex w-100 justify-content-between">
                                                                <h5 class="mb-1">Name: <b>{{$product->name}}</b>
                                                                </h5>
                                                            </div>
                                                            <h6 class="mb-1">Quantity: <b>{{$product->quantity}}</b>
                                                            </h6>
                                                    </div>
                                                        @endforeach
                                                </div>
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
                            <td>{{$package->quantity}}</td>
                            <td>{{$package->products_count}}</td>
                            @if(auth()->user()->admin)
                                <td>{{$package->price}}</td>
                            @endif
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editPackageModal-{{$package->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editPackageModal-{{$package->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editPackageModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editPackageModalLabel">
                                                        Edit {{$package->name}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-package', $package->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.package-form-inputs', ['package' => $package])
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
                                    <form method="post" action="{{route('dashboard.delete-package', $package->id)}}">
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
            {{ $packages->links() }}
        @endif
    </main>
@endsection