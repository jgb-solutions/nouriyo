<ul class="nav flex-column">
    @if(auth()->user()->admin)
        <li class="nav-item">
            <a class="nav-link active" href="{{route('dashboard.index')}}">
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{route('dashboard.orders')}}">
                Orders
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard.products')}}">
            Products
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard.packages')}}">
            Packages
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard.clients')}}">
            Clients
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard.agents')}}">
            Clients
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard.reports')}}">
            Reports
        </a>
    </li>
</ul>