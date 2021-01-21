<div class="app-header header py-1 d-flex">
	<div class="container-fluid">
		<div class="d-flex">
			<a class="header-brand" href="{{route('dashboard')}}">
				<img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" class="" alt="{{$appSetting->website_name}}" style="height: 50px;">
			</a>
			<a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-toggle="sidebar" href="#"></a>

			@if(Auth::user()->hasRole('admin'))
			<div class="d-lg-block horizontal">
				<ul class="nav">
					<li class="">
						<div class="dropdown d-md-flex border-right">
							<a class="nav-link icon" data-toggle="dropdown" aria-expanded="false">
								<i class="fe fe-mail floating" data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" data-content="Notification" data-original-title=""></i>
							<span class=" nav-unread badge badge-warning  badge-pill">{{Auth::user()->unreadNotifications->count()}}</span>
							</a>
							@if(Auth::user()->unreadNotifications->count()>0)
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
								<a href="{{route('read-all-notification')}}" class="dropdown-item text-center">{{Auth::user()->unreadNotifications->count()}} Nuevo Mensaje</a>
								<div class="dropdown-divider"></div>
								<div class="scroll-div">
									@foreach (Auth::user()->unreadNotifications as $notification)

									<a href="{{route('read-notification',$notification->id)}}" class="dropdown-item d-flex pb-3 split-line">
										<span class="avatar brround mr-3 align-self-center"><img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" class="avatar brround image-mob" alt="{{$appSetting->website_name}}"></span>
										<div>
											@foreach($notification->data as $data)
												{{$data['body']}}
											@endforeach
											<div class="small text-muted">{{($notification->created_at)->diffForHumans()}}</div>
										</div>
									</a>
									@endforeach
								</div>
								<div class="dropdown-divider"></div>
								<a href="{{route('read-all-notification')}}" class="dropdown-item text-center">Mensajes</a>
							</div>
							@endif
						</div>
					</li>
				</ul>
			</div>
			@endif

			<div class="d-flex order-lg-2 ml-auto">
				<div class="dropdown d-none d-md-flex " >
					<a  class="nav-link icon full-screen-link">
						<i class="mdi mdi-arrow-expand-all"  id="fullscreen-button"></i>
					</a>
				</div>
				@if(Auth::user()->hasAnyPermission(['product-list','purchase-order-list','customer-list','sales-order-list']) || Auth::user()->hasRole('admin'))
				<div class="dropdown d-md-flex">
					<a class="nav-link icon" data-toggle="dropdown">
						<i class="fe fe-grid floating"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-shortcut dropdown-menu-right dropdown-menu-arrow p-0">
						<ul class="drop-icon-wrap p-0 m-0">
							@can('product-list')
							<li>
								<a href="{{route('product-list')}}" class="drop-icon-item">
									<i class="si si-notebook"></i>
									<span class="block">Lista de Productos</span>
								</a>
							</li>
							@endcan
							@can('customer-list')
							<li>
								<a href="{{route('customer-list')}}" class="drop-icon-item">
									<i class="si si-people"></i>
									<span class="block">Clientes</span>
								</a>
							</li>
							@endcan
							@can('purchase-order-list')
							<li>
								<a href="{{route('purchase-order-list')}}" class="drop-icon-item">
									<i class="si si-basket-loaded"></i>
									<span class="block">Compras&nbsp;Pedidos</span>
								</a>
							</li>
							@endcan

							@can('sales-order-list')
							<li>
								<a href="{{route('sales-order-list')}}" class="drop-icon-item">
									<i class="si si-basket"></i>
									<span class="block">Ventas&nbsp;Pedidos</span>
								</a>
							</li>
							@endcan
							@can('purchase-order-create')
							<li>
								<a href="{{route('purchase-order-create')}}" class="drop-icon-item">
									<i class="si si-layers"></i>
									<span class="block">NUeva Compra&nbsp;Pedidos</span>
								</a>
							</li>
							@endcan

							@can('sales-order-create')
							<li>
								<a href="{{route('sales-order-create')}}" class="drop-icon-item">
									<i class="si si-bag"></i>
									<span class="block">Nueva Venta&nbsp;Pedido</span>
								</a>
							</li>
							@endcan
						</ul>
					</div>
				</div>
				@endif
				<div class="dropdown">
					<a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
						<span class="avatar avatar-md brround"><img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" alt="{{Auth::user()->name}}" class="avatar avatar-md brround"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow ">
						<div class="text-center">
							<a href="#" class="dropdown-item text-center font-weight-sembold user">{{Auth::user()->name}}</a>

							<div class="dropdown-divider"></div>
						</div>
						<a class="dropdown-item @if(Request::segment(1)==='edit-profile') active @endif" href="{{ route('edit-profile') }}">
							<i class="dropdown-icon mdi mdi-account-outline "></i> Perfil
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
							<i class="dropdown-icon fa fa-lock"></i> Bloquea Pantalla
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
