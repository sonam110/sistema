<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body">
			<div>
				<img src="{{ env('CDN_URL') }}/imagenes/{!! $appSetting->website_logo !!}" alt="{{Auth::user()->name}}" class="avatar avatar-xl brround mCS_img_loaded">
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
			<a class="side-menu__item menu-c" href="{{ route('employee-list') }}"><i class="side-menu__icon si si-people"></i><span class="side-menu__label">Lista de Empleados</span></a>
		</li>
		@endcan

		@if(Auth::user()->hasAnyPermission(['product-list','price-change-ml']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-notebook"></i><span class="side-menu__label">Productos</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('product-list')
				<li>
					<a href="{{route('product-list')}}" class="slide-item menu-c">Lista de Productos</a>
				</li>
				@endcan

				@can('price-change-ml')
				<li>
					<a href="{{route('price-change-ml')}}" class="slide-item menu-c">Cambio de precios ML</a>
				</li>
				<li>
					<a href="{{route('update-price-excel')}}" class="slide-item menu-c">Actualizar precio manualmente</a>
				</li>

				<li>
					<a href="{{route('dimension-change-ml')}}" class="slide-item menu-c">Cambio de dimensión ML</a>
				</li>

				<li>
					<a href="{{route('ml-list-shipping-mode-me1')}}" class="slide-item menu-c">ML list shipping mode ME1</a>
				</li>

				<li>
					<a href="{{route('add-products-on-ml')}}" class="slide-item menu-c">Add Products on ML</a>
				</li>
				@endcan
			</ul>
		</li>
		@endif

		@if(Auth::user()->hasAnyPermission(['supplier-list','purchase-order-list','purchase-order-received-list','purchase-order-return-list']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-basket-loaded"></i><span class="side-menu__label">Compras</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('supplier-list')
				<li>
					<a href="{{route('supplier-list')}}" class="slide-item menu-c">Lista de Proveedores</a>
				</li>
				@endcan
				@can('purchase-order-list')
				<li>
					<a href="{{route('purchase-order-list')}}" class="slide-item menu-c">Ordenes de Compra</a>
				</li>
				@endcan
				@can('purchase-order-received-list')
				<li>
					<a href="{{route('purchase-order-received-list')}}" class="slide-item menu-c">Productos Recibidos</a>
				</li>
				@endcan
				@can('purchase-order-list')
				<li>
					<a href="{{route('products-ordered-but-not-received')}}" class="slide-item menu-c">Productos pedidos pero no recibidos</a>
				</li>
				@endcan
				@can('purchase-order-return-list')
				<li>
					<a href="{{route('purchase-order-return-list')}}" class="slide-item menu-c">Productos Devueltos</a>
				</li>
				@endcan
				@can('purchase-invoice-list')
				<li>
					<a href="{{route('purchase-invoice-list')}}" class="slide-item menu-c">Facturas</a>
				</li>
				@endcan
				@if (Auth::user()->hasRole('admin'))
				<li>
					<a href="{{route('concept-list')}}" class="slide-item menu-c">Conceptos</a>
				</li>
				@endif
			</ul>
		</li>
		@endif

		@if(Auth::user()->hasAnyPermission(['customer-list','sales-order-list','sales-return-list']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-basket"></i><span class="side-menu__label">Ventas</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">

				@can('customer-list')
				<li>
					<a href="{{route('customer-list')}}" class="slide-item menu-c">Lista de Clientes</a>
				</li>
				@endcan
				@can('sales-order-list')
				<li>
					<a href="{{route('sales-order-list')}}" class="slide-item menu-c">Ventas</a>
				</li>
				@endcan
				@can('sales-order-return-list')
				<li>
					<a href="{{route('sales-order-return-list')}}" class="slide-item menu-c">Ventas Devueltas</a>
				</li>
				@endcan

			</ul>
		</li>
		@endif

		@if(Auth::user()->hasAnyPermission(['direct-sales-return','direct-purchase-return']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-logout"></i><span class="side-menu__label">Devoluciones</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('direct-purchase-return')
				<li>
					<a href="{{route('direct-purchase-return')}}" class="slide-item menu-c">Compra</a>
				</li>
				@endcan

				@can('direct-sales-return')
				<li>
					<a href="{{route('direct-sales-return')}}" class="slide-item menu-c">Venta</a>
				</li>
				@endcan

			</ul>
		</li>
		@endif

		@if(Auth::user()->hasAnyPermission(['installment-order-list','installment-paid-history','installment-receive']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-calculator"></i><span class="side-menu__label">Saldos</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('installment-order-list')
				<li>
					<a href="{{route('installment-order-list')}}" class="slide-item menu-c">Pedidos con Saldo pendiente</a>
				</li>
				@endcan
				@can('installment-receive')
				<li>
					<a href="{{route('installment-receive')}}" class="slide-item menu-c">Pagos parciales Recibidos</a>
				</li>
				@endcan
			</ul>
		</li>
		@endif


		@if(Auth::user()->hasAnyPermission(['sales-report','purchase-report','short-stock-item-report']) || Auth::user()->hasRole('admin'))
		<li class="slide">
			<a class="side-menu__item menu-c" data-toggle="slide" href="#"><i class="side-menu__icon si si-pie-chart"></i><span class="side-menu__label">Reportes</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				@can('sales-report')
				<li>
					<a href="{!! route('sales-report-new') !!}" class="slide-item menu-c">Reporte de Cobranzas</a>
				</li>
				<li>
					<a href="{!! route('sales-report') !!}" class="slide-item menu-c">Listado de Ventas</a>
				</li>
				<li>
					<a href="{!! route('product-sales-report') !!}" class="slide-item menu-c">Reporte de Ventas y detalle de productos</a>
				</li>
				<li>
					<a href="{!! route('product-stock-report') !!}" class="slide-item menu-c">Informe de Existencias</a>
				</li>
				@endcan

				@can('purchase-report')
				<li>
					<a href="{!! route('purchase-report') !!}" class="slide-item menu-c">Listado de Pedidos</a>
				</li>
				@endcan

				@can('purchase-report')
				<li>
					<a href="{!! route('purchase-concept-report') !!}" class="slide-item menu-c">Facturas de Compras x concepto</a>
				</li>
				@endcan

				@can('short-stock-item-report')
				<li>
					<a href="{!! route('short-stock-item-report') !!}" class="slide-item menu-c">Short Stock Item</a>
				</li>
				@endcan
			</ul>
		</li>
		@endif

	</ul>
</aside>
