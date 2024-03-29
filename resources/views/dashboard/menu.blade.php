<ul class="nav flex-column mt-4">
    @if(auth()->user()->admin)
        <li class="nav-item">
            <a class="nav-link {{request()->route()->getName() == 'dashboard.index' ? 'active' : ''}}"
               href="{{route('dashboard.index')}}">
                Dashboard
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link {{request()->route()->getName() == 'dashboard.orders' ? 'active' : ''}}"
           href="{{route('dashboard.orders')}}">
            Orders
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{request()->route()->getName() == 'dashboard.products' ? 'active' : ''}}"
           href="{{route('dashboard.products')}}">
            Products
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{request()->route()->getName() == 'dashboard.packages' ? 'active' : ''}}"
           href="{{route('dashboard.packages')}}">
            Packages
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{request()->route()->getName() == 'dashboard.clients' ? 'active' : ''}}"
           href="{{route('dashboard.clients')}}">
            Clients
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{request()->route()->getName() == 'dashboard.beneficiaries' ? 'active' : ''}}"
           href="{{route('dashboard.beneficiaries')}}">
            Beneficiaries
        </a>
    </li>

    @if(auth()->user()->agent)
        <li class="nav-item">
            <hr/>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()->route()->getName() == 'dashboard.agent-report' ? 'active' : ''}}"
               href="{{route('dashboard.agent-report')}}">
                Business Report
            </a>
        </li>
    @endif

    @if(auth()->user()->admin)
        <li class="nav-item">
            <hr/>
        </li>

        <li class="nav-item">
            <a class="nav-link {{request()->route()->getName() == 'dashboard.admins' ? 'active' : ''}}"
               href="{{route('dashboard.admins')}}">
                Admins
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()->route()->getName() == 'dashboard.agents' ? 'active' : ''}}"
               href="{{route('dashboard.agents')}}">
                Agents
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{request()->route()->getName() == 'dashboard.settings' ? 'active' : ''}}"
               href="{{route('dashboard.settings')}}">
                Orders' Settings
            </a>
        </li>
    @endif
</ul>