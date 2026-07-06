<aside class="app-sidebar ps ps--active-y">
	<div class="app-sidebar__logo">
		<a class="header-brand" href="{{url('/e')}}">
			<img src="{{URL::asset('assets/images/brand/logo.png')}}" class="header-brand-img desktop-lgo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/logo1.png')}}" class="header-brand-img dark-logo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/favicon.png')}}" class="header-brand-img mobile-logo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/favicon1.png')}}" class="header-brand-img darkmobile-logo" alt="Admintro logo">
		</a>
	</div>
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{URL::asset('assets/images/users/2.jpg')}}" alt="user-img" class="avatar-xl rounded-circle mb-1">
			</div>
			<div class="user-info">
				<h5 class=" mb-1">{{Str::title(auth()->user()->name)}} <i class="ion-checkmark-circled  text-success fs-12"></i></h5>
				<span class="text-muted app-sidebar__user-name text-sm">{{Str::title(auth()->user()->role)}}</span>
			</div>
		</div>
	</div>
	<ul class="side-menu app-sidebar3">
		<li class="side-item side-item-category mt-4">Main</li>
		<li class="slide">
			<a class="side-menu__item"  href="{{route('dashboard', ['username' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
			<span class="side-menu__label">Dashboard</span></a>
		</li>
		@canany(['view.setting'])
		<li class="slide">
			<a class="side-menu__item" href="{{route('setting.index', ['username' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.62l-1.92-3.32c-.12-.22-.39-.3-.61-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.48.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.09-.49 0-.61.22L2.74 8.87c-.12.21-.08.48.12.62l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.62l1.92 3.32c.12.22.39.3.61.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.48-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.49 0 .61-.22l1.92-3.32c.12-.22.07-.48-.12-.62l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
			<span class="side-menu__label">Settings</span></a>
		</li>
		@endcan
		@can('view.global_contacts')
		<li class="slide">
			<a class="side-menu__item" href="{{route('global_contacts.index', ['username' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 12.5c-2.49 0-4.5-2.01-4.5-4.5s2.01-4.5 4.5-4.5 4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5z"></path></svg>
			<span class="side-menu__label">Global Contacts</span></a>
		</li>
		@endcan
		@can('view.contacts')
		<li class="slide">
			<a class="side-menu__item" href="{{route('contacts.index', ['username' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 12.5c-2.49 0-4.5-2.01-4.5-4.5s2.01-4.5 4.5-4.5 4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5z"></path></svg>
			<span class="side-menu__label">My Contacts</span></a>
		</li>
		@endcan
		@can('view.project')
			<li class="slide">
				<a class="side-menu__item"  href="{{route('project.index', ['username' => $siteSlug])}}">
				<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
				<span class="side-menu__label">Project</span></a>
			</li>
		@endcan
		@canany(['view.role', 'view.permission'])
		<li class="slide">
			<a class="side-menu__item" data-toggle="slide" href="#">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M16.66 4.52l2.83 2.83-2.83 2.83-2.83-2.83 2.83-2.83M9 5v4H5V5h4m10 10v4h-4v-4h4M9 15v4H5v-4h4m7.66-13.31L11 7.34 16.66 13l5.66-5.66-5.66-5.65zM11 3H3v8h8V3zm10 10h-8v8h8v-8zm-10 0H3v8h8v-8z"></path></svg>
			<span class="side-menu__label">Authorization</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu ">
				@can('view.role')
					<li><a href="{{route('role.index', ['username' => $siteSlug])}}" class="slide-item">Roles</a></li>
				@endcan
				@can('view.permission')
				<li><a href="{{route('permission.index', ['username' => $siteSlug])}}" class="slide-item">Permissions</a></li>
				@endcan
			</ul>
		</li>
		@endcan
		
		<li class="side-item side-item-category mt-4">
			Customer
		</li>
		@php
			$userType = auth()->user()->type;
		@endphp
		
		@can('view.admin')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['username' => $siteSlug]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
					<span class="side-menu__label">Admins</span>
				</a>
			</li>
		@endcan
		@can('view.public_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.publicVendor', ['username' => $siteSlug]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
					<span class="side-menu__label">Public Vendors</span>
				</a>
			</li>
		@endcan

		@can('view.private_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.privateVendor', ['username' => $siteSlug]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg>
					<span class="side-menu__label">Private Vendors</span>
				</a>
			</li>
		@endcan
		@can('view.admin')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.users', ['username' => $siteSlug]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.89 1.97 1.74 1.97 2.95V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
					<span class="side-menu__label">Users</span>
				</a>
			</li>
		@endcan
		
		@canany(['view.game', 'view.wheel'])
			<li class="side-item side-item-category mt-4">
				Fortune Wheel
			</li>
			@can('view.game')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('game.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2z"/></svg>
					<span class="side-menu__label">Games</span></a>
				</li>
			@endcan
			@can('view.wheel')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('wheel.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg>
					<span class="side-menu__label">Wheels</span></a>
				</li>
			@endcan
		@endcan
		
		@canany(['view.store_view'])
			<li class="side-item side-item-category mt-4">
				Configuration
			</li>
			@can('view.store_view')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('store_view.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M18.36 9l.6 3H5.04l.6-3M20 4h-3V3h-2v1h-4V3H9v1H6l2 15h12l2-15zm-12 11h-2v-2h2v2zm0-3h-2V9h2v3zm3 3h-2v-2h2v2zm0-3h-2V9h2v3zm3 3h-2v-2h2v2zm0-3h-2V9h2v3z"/></svg>
					<span class="side-menu__label">Store View</span></a>
				</li>
			@endcan
			@can('view.category')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('category.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.53 6.12L22 9.27l-7 6.43 1.65 9.63L12 17.77l-8.65 7.56L5 15.7 -2 9.27l6.47-.95L12 2z" transform="translate(2 0)"/></svg>
					<span class="side-menu__label">Category</span></a>
				</li>
			@endcan
			@can('view.page')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('page.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V4.5L17.5 9H13z"/></svg>
					<span class="side-menu__label">Page</span></a>
				</li>
			@endcan
			@can('view.slide')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('slide.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
					<span class="side-menu__label">Slide</span></a>
				</li>
			@endcan
			@can('view.testimonial')
				<li class="testimonial">
					<a class="side-menu__item"  href="{{route('testimonial.index', ['username' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 21c0 1.1.9 2 2 2h4c1.1 0 2-.9 2-2v-8H3v8zm8-18c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm9 11c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 8c1.1 0 2-.9 2-2v-8h-8v8c0 1.1.9 2 2 2h4z"/></svg>
					<span class="side-menu__label">Testimonial</span></a>
				</li>
			@endcan
	
		@endcan

        @canany(['view.country','view.unit','view.element'])
            <li class="side-item side-item-category mt-4">
                {{__('Global Data')}}
            </li>
            @can('view.country')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('country.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.9-1.6-4.6-2.8V7z"/>
                        </svg>
                        <span class="side-menu__label">{{__('Country')}}</span></a>
                </li>
            @endcan
            @can('view.unit')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('unit.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M15 9H9v6h6V9zm-2 4h-2v-2h2v2zm7-7h-1V2h-2v4h-4V2h-2v4H8V2H6v4H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v12z"/>
                        </svg>
                        <span class="side-menu__label">{{__('Unit')}}</span></a>
                </li>
            @endcan
            @can('view.page')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('element.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.3-1.54L7 17h10l-3.04-4.71z"/>
                        </svg>
                        <span class="side-menu__label">{{__('Element')}}</span></a>
                </li>
            @endcan

        @endcan

        @canany(['view.component','view.compo_price'])
            <li class="side-item side-item-category mt-4">
                {{__('Animal Nutrition')}}
            </li>
            @can('view.component')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('component.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2l5.5 9.5h-11z M12 22L6.5 12.5h11z M22 12l-9.5 5.5v-11z M2 12l9.5 5.5v-11z"/>
                        </svg>
                        <span class="side-menu__label">{{__('Component')}}</span></a>
                </li>
            @endcan
            @can('view.compo_price')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('compo_price.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                        </svg>
                        <span class="side-menu__label">{{__('Compo Price')}}</span></a>
                </li>
            @endcan

    @endcanany

        @canany(['view.farm', 'view.hangar', 'view.feed_mill', 'view.slaughter', 'view.chicks_supplier'])
            <li class="side-item side-item-category mt-4">
                ADD2CARE Farm
            </li>
            @can('view.farm')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('farm.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M10 10.5h8v3h-8z M17 7.5h1v6h-1z M10 4h3v3.5h-3z M13 4h3v3.5h-3z M16 4h3v3.5h-3z M10 13.5h8v6h-8z M4 4v16h2V4z"/>
                        </svg>
                        <span class="side-menu__label">Farms</span></a>
                </li>
            @endcan
            @can('view.hangar')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('hangar.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v12h2V4h12v12h2V4c0-1.1-.9-2-2-2zm0 14H4v2h16v-2zm-4 4H8v-2h8v2z"/>
                        </svg>
                        <span class="side-menu__label">Hangars</span></a>
                </li>
            @endcan
            @can('view.feed_mill')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('feed-mill.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2l8 4v5c0 5.55-3.84 10.74-9 12-5.16-1.26-9-6.45-9-12V6l8-4zm-2 16h4v-6h-4v6z M10 6h4v4h-4V6z"/>
                        </svg>
                        <span class="side-menu__label">Feed Mills</span></a>
                </li>
            @endcan
            @can('view.slaughter')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('slaughter.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm9 9c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zM3 13c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 6c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                        <span class="side-menu__label">Slaughters</span></a>
                </li>
            @endcan
            @can('view.chicks_supplier')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('chicks-supplier.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M19.5 13c1.93 0 3.5-1.57 3.5-3.5S21.43 6 19.5 6 16 7.57 16 9.5s1.57 3.5 3.5 3.5zm-12 0c1.93 0 3.5-1.57 3.5-3.5S9.43 6 7.5 6 4 7.57 4 9.5 5.57 13 7.5 13zm6-8c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm0 8c2.33 0 7 1.17 7 3.5V21H6v-2.5c0-2.33 4.67-3.5 7-3.5z"/>
                        </svg>
                        <span class="side-menu__label">Chicks Suppliers</span></a>
                </li>
            @endcan
            @can('view.flock')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('flock.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-3-13h2v6h-2zm4 0h2v6h-2z"/>
                        </svg>
                        <span class="side-menu__label">Flocks</span></a>
                </li>
            @endcan
            @can('view.chicken_sale')
                <li class="slide">
                    <a class="side-menu__item" href="{{route('chicken-sale.index', ['username' => $siteSlug])}}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                        <span class="side-menu__label">Chicken Sales</span></a>
                </li>
            @endcan
        @endcanany

</aside>
<!--aside closed-->