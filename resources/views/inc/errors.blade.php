@if(count($errors))
    <div class="card bg-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-exclamation-triangle"></i>
                Please fix these errors.
            </h3>
        </div>
        <ul class="list-group">
            @foreach ( $errors->all('<li class="list-group-item">:message</li>') as $error )
                {!! $error !!}
            @endforeach
        </ul>
    </div>
@endif