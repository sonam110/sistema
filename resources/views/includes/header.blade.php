<div class="app-header header py-1 d-flex">
	<div class="container-fluid">
		<div class="d-flex">
			<a class="header-brand" href="{{route('dashboard')}}">
				<img src="{{ env('CDN_URL').$appSetting->website_logo}}" class="" alt="{{$appSetting->website_name}}" style="height: 50px;">
			</a>
			<a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-toggle="sidebar" href="#"></a>

			<div class="d-flex order-lg-2 ml-auto">
				<div class="dropdown d-none d-md-flex " >
					<a  class="nav-link icon full-screen-link">
						<i class="mdi mdi-arrow-expand-all"  id="fullscreen-button"></i>
					</a>
				</div>
				<div class="dropdown">
					<a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
						<span class="avatar avatar-md brround"><img src="{{ env('CDN_URL').$appSetting->website_logo}}" alt="{{Auth::user()->name}}" class="avatar avatar-md brround"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow ">
						<div class="text-center">
							<a href="#" class="dropdown-item text-center font-weight-sembold user">{{Auth::user()->name}}</a>

							<div class="dropdown-divider"></div>
						</div>
						<a class="dropdown-item @if(Request::segment(1)==='edit-profile') active @endif" href="{{ route('edit-profile') }}">
							<i class="dropdown-icon mdi mdi-account-outline "></i> Profile
						</a>

						@if(Auth::user()->hasRole('admin'))
						<a class="dropdown-item @if(Request::segment(1)==='roles') active @endif" href="{{ route('roles.index') }}">
							<i class="dropdown-icon fa fa-folder"></i> 
							Manage Role
						</a>

                        <a class="dropdown-item @if(Request::segment(1)==='permissions') active @endif" href="{{ route('permissions.index') }}"> 
                        	<i class="dropdown-icon fa fa-folder-open"></i> 
                        	Manage Permission
                        </a>
	                 	@endif
						
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="{{ route('screenlock', [time(), Auth::user()->id, MD5(\Illuminate\Support\Str::random(10))]) }}">
							<i class="dropdown-icon fa fa-lock"></i> Screen Lock
						</a>
						<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="dropdown-icon mdi  mdi-logout-variant"></i>
                            {{ __('Sign out') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>