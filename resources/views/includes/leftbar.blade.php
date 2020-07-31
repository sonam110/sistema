<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body">
			<div>
				<img src="{{ env('CDN_URL').$appSetting->website_logo}}" alt="{{Auth::user()->name}}" class="avatar avatar-xl brround mCS_img_loaded">
				<a href="{{ route('edit-profile') }}" class="profile-img">
					<span class="fa fa-pencil" aria-hidden="true"></span>
				</a>
			</div>
			<div class="user-info mb-2">
				<h4 class="font-weight-semibold text-dark mb-1">{{Auth::user()->name}}</h4>
			</div>
			<a href="{{ route('screenlock', [time(), Auth::user()->id, MD5(\Illuminate\Support\Str::random(10))]) }}" title="" class="user-button"  data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Screen Lock"><i class="fa fa-lock"></i></a>
			<a href="{{ route('logout') }}" title="" class="user-button"  data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Sign Out" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i></a>
		</div>
	</div>
	<ul class="side-menu">
		<li>
			<a class="side-menu__item menu-c" href="{{ route('dashboard') }}"><i class="side-menu__icon si si-home"></i><span class="side-menu__label">Dashboard</span></a>
		</li>
		@can('employee-list')
		<li>
			<a class="side-menu__item menu-c" href="{{ route('employee-list') }}"><i class="side-menu__icon si si-people"></i><span class="side-menu__label">Employee List</span></a>
		</li>
		@endcan

		@can('product-list')
		<li>
			<a class="side-menu__item menu-c" href="{{route('product-list')}}"><i class="side-menu__icon si si-notebook"></i><span class="side-menu__label">Products List</span></a>
		</li>
		@endcan

		@if(Auth::user()->hasAnyPermission(['supplier-list','purchase-order-list','purchase-return-list']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-basket-loaded"></i><span class="side-menu__label">Purchase</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('supplier-list')
				<li>
					<a href="{{route('supplier-list')}}" class="slide-item menu-c">Suppliers</a>
				</li>
				@endcan
				@can('purchase-order-list')
				<li>
					<a href="{{route('purchase-order-list')}}" class="slide-item menu-c">Purchase Orders</a>
				</li>
				@endcan
				@can('purchase-order-received-list')
				<li>
					<a href="{{route('purchase-order-received-list')}}" class="slide-item menu-c">Product Received List</a>
				</li>
				@endcan
				@can('purchase-order-return-list')
				<li>
					<a href="{{route('purchase-order-return-list')}}" class="slide-item menu-c">Product Return List</a>
				</li>
				@endcan
			</ul>
		</li>
		@endif

		@if(Auth::user()->hasAnyPermission(['customer-list','sales-order-list','sales-return-list']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-basket"></i><span class="side-menu__label">Sales</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">

				@can('customer-list')
				<li>
					<a href="{{route('customer-list')}}" class="slide-item menu-c">Customers</a>
				</li>
				@endcan
				@can('sales-order-list')
				<li>
					<a href="#" class="slide-item menu-c">Sale</a>
				</li>
				@endcan
				@can('sales-return-list')
				<li>
					<a href="#" class="slide-item menu-c">Sale Return</a>
				</li>
				@endcan

			</ul>
		</li>
		@endif


		@if(Auth::user()->hasAnyPermission(['reports-daily','reports-weekly','reports-monthly','reports-custom','reports-all']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-pie-chart"></i><span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('reports-daily')
				<li>
					<a href="#" class="slide-item menu-c">Daily</a>
				</li>
				@endcan

				@can('reports-weekly')
				<li>
					<a href="#" class="slide-item menu-c">Weekly</a>
				</li>
				@endcan

				@can('reports-monthly')
				<li>
					<a href="#" class="slide-item menu-c">Monthly</a>
				</li>
				@endcan

				@can('reports-custom')
				<li>
					<a href="#" class="slide-item menu-c">Custom</a>
				</li>
				@endcan

				@can('reports-all')
				<li>
					<a href="#" class="slide-item menu-c">All Time</a>
				</li>
				@endcan
			</ul>
		</li>
		@endif

	</ul>
</aside>