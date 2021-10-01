<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">
    <!-- Brand -->
    <div class="sidenav-header d-flex align-items-center">
      <a class="navbar-brand" href="{{ route('admin.dashboard')}}">
        {{ (!empty(config('app.name')) ? config('app.name') : 'Laravel Dashboard') }}
      </a>
      <div class="ml-auto">
        <!-- Sidenav toggler -->
        <div class="sidenav-toggler d-none d-xl-block" data-action="sidenav-unpin" data-target="#sidenav-main">
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="navbar-inner">
      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <!-- Nav items -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a href="{{ route('admin.dashboard')}}" class="nav-link {{ !empty($menu) ? ($menu == "dashboard" ? 'active' : '') : '' }}">
              <i class="ni ni-shop text-primary"></i>
              <span class="nav-link-text">Dashboard</span>
            </a>
          </li>

          @can('maps-list')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.map.view') }}">
                <i class="ni ni-map-big text-primary"></i>
                <span class="nav-link-text">Maps</span>
              </a>
            </li>
          @endcan

          @can('reports-list')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.reports.index')}}">
                <i class="ni ni-chat-round text-orange"></i>
                <span class="nav-link-text">Reports</span>
              </a>
            </li>
          @endcan

          @can('subscriber-list')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.subscribers.index')}}">
                <i class="ni ni-book-bookmark text-info"></i>
                <span class="nav-link-text">Subscriber</span>
              </a>
            </li>
          @endcan

          @if (auth()->user()->can('admin-list') || auth()->user()->can('roles-list'))
            <li class="nav-item">
              <a class="nav-link {{ !empty($menu) ? ($menu == "accounts" ? 'active' : '') : '' }}" href="#navbar-accounts" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="navbar-accounts">
                <i class="ni ni-circle-08 text-primary"></i>
                <span class="nav-link-text">Accounts</span>
              </a>
              <div class="collapse {{ !empty($menu) ? ($menu == "accounts" ? 'show' : '') : '' }}" id="navbar-accounts">
                <ul class="nav nav-sm flex-column">
                  @if(auth()->user()->can('admin-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'admins' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.admin.index') }}" class="nav-link">Users</a>
                    </li>
                  @endif
                  
                  @if(auth()->user()->can('roles-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'roles' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.role.index') }}" class="nav-link">Role</a>
                    </li>
                  @endif
                </ul>
              </div>
            </li>
          @endif

          @if (auth()->user()->can('provinces-list') || auth()->user()->can('regencies-list') || auth()->user()->can('districts-list') || auth()->user()->can('villages-list'))
            <li class="nav-item">
              <a class="nav-link {{ !empty($menu) ? ($menu == "address" ? 'active' : '') : '' }}" href="#navbar-address" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="navbar-address">
                <i class="ni ni-book-bookmark text-success"></i>
                <span class="nav-link-text">Address</span>
              </a>
              <div class="collapse {{ !empty($menu) ? ($menu == "address" ? 'show' : '') : '' }}" id="navbar-address">
                <ul class="nav nav-sm flex-column">
                  @if(auth()->user()->can('provinces-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'provinces' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.provinces.index')}}" class="nav-link">Provinces</a>
                    </li>
                  @endif

                  @if(auth()->user()->can('regencies-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'regencies' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.regencies.index') }}" class="nav-link">Regencies</a>
                    </li>
                  @endif

                  @if(auth()->user()->can('districts-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'districts' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.districts.index')}}" class="nav-link">Districts</a>
                    </li>
                  @endif

                  @if(auth()->user()->can('villages-list'))
                    <li class="nav-item {{ !empty($submenu) ? ($submenu == 'villages' ? 'show' : '') : '' }}">
                      <a href="{{ route('admin.villages.index') }}" class="nav-link">Villages</a>
                    </li>
                  @endif
                </ul>
              </div>
            </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</nav>