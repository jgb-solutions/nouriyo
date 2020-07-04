@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Agents</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAgentModal">
                        Add an Agent
                    </button>
                    <div class="modal fade" id="addAgentModal" tabindex="-1" role="dialog"
                         aria-labelledby="addAgentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addAgentModalLabel">Add a new Agent</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.agents')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.agent-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Agent</button>
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

        @if($agents->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Business</th>
                        @if(auth()->user()->admin)
                            <th>Country</th>
                        @endif
                        <th>Limit</th>
                        <th>Active</th>
                        @if(auth()->user()->admin)
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($agents as $agent)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showAgentModal-{{$agent->id}}">
                                    {{$agent->fullName}}
                                </button>
                                <div class="modal fade" id="showAgentModal-{{$agent->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showAgentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showAgentModalLabel">{{$agent->fullName}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div><img height="200" src="{{$agent->image_url}}"/></div>
                                                <p>
                                                    {{$agent->description}}
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
                            <td>{{$agent->business}}</td>
                            @if(auth()->user()->admin)
                                <td>{{$agent->country}}</td>
                            @endif
                            <td>{{$agent->limit}}</td>
                            <td>{{$agent->active ? 'Yes' : 'No'}}</td>
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editAgentModal-{{$agent->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editAgentModal-{{$agent->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editAgentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAgentModalLabel">
                                                        Edit {{$agent->fullName}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-agent', $agent->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.agent-form-inputs', ['agent' => $agent])
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
                                    <form method="post" action="{{route('dashboard.delete-agent', $agent->id)}}">
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
            {{ $agents->links() }}
        @endif
    </main>
@endsection