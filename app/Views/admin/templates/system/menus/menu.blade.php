<div class="site-menubar" id="main-menu">
    <div class="site-menubar-body">
        <div>
            <div >
				<ul class="site-menu">

					{{-- Looping in the main menu --}}
					@foreach($menu as $item => $settings)

						{{--has submenu--}}
						@if( isset($settings['links']) && !empty($settings['links']))

							{{--Adding active and open class if menuitem is active--}}
							<li class="site-menu-item has-sub {{ ($settings['active']? 'open active' : '') }}" >
								<a href="#" title="{{ trans( $settings['help'] ) }}">
									<i class="site-menu-icon fa {{ $settings['icon'] }}"></i>
									<span class="site-menu-title">{{ trans( $settings['title'] ) }}</span>
									<span class="site-menu-arrow"></span>
								</a>
								<ul class="site-menu-sub">

									{{--Showing submenu items--}}
									@foreach($settings['links'] as $subItem => $subSettings)

										{{--Making the submenu item active--}}
										<li class="site-menu-item {{ ( $subSettings['active'] ? 'active' : '') }}" >
											<a class="animsition-link" href="{{$subSettings['url']}}" title="{{ trans( $subSettings['help'] ) }}">
												<span class="site-menu-title">{{ trans( $subSettings['title'] ) }}</span>
											</a>
										</li>
									@endforeach

								</ul>
							</li>

						{{--no submenu--}}
						@else

							{{--Adding active class when active--}}
							<li class="site-menu-item {{ ( $settings['active']? 'active' : '' ) }}" >
								<a href="{{ $settings['url'] }}" title="{{ trans($settings['help']) }}">
									<i class="site-menu-icon fa {{ $settings['icon'] }}"></i>
									<span class="site-menu-title">{{ trans( $settings['title'] ) }}</span>
								</a>
							</li>
						@endif

					@endforeach
				</ul>

				{{-- TODO: Move to array --}}
				{{--$url is the current route--}}
				<?php $url = '/'. \Request::path(); ?>

                @if(\Gate::allows('access', 'all-messages') or \Gate::allows('access', 'all-gateways') or \Gate::allows('access', 'all-roles-and-permissions') or \Gate::allows('access', 'all-sites') or \Gate::allows('access', 'all-translations') or \Gate::allows('access', 'adjet-templates') or \Gate::allows('access', 'all-users') )
                    <ul class="dev-menu site-menu">
                        <li class="site-menu-category">{{trans('admin.sys-admin-tools')}}</li>

						@can('access', 'all-messages')
							<li class="site-menu-item {{(substr($url, 0, strlen('/messages')) === '/messages' ? 'active' : '')}}">
								<a href="/messages">
									<span class="site-menu-title">{{trans('admin.all-messages')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'all-gateways')
							<li class="site-menu-item {{(substr($url, 0, strlen('/gateways')) === '/gateways' ? 'active' : '')}}">
								<a href="/gateways">
									<span class="site-menu-title">{{trans('admin.all-gateways')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'all-roles-and-permissions')
							<li class="site-menu-item {{(substr($url, 0, strlen('/roles-and-permissions')) === '/roles-and-permissions' ? 'active' : '')}}">
								<a href="/roles-and-permissions">
									<span class="site-menu-title">{{trans('admin.all-roles-and-permissions')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'all-sites')
							<li class="site-menu-item {{(substr($url, 0, strlen('/sites')) === '/sites' ? 'active' : '')}}">
								<a href="/sites">
									<span class="site-menu-title">{{trans('admin.all-sites')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'translations')
							<li class="site-menu-item {{(substr($url, 0, strlen('/translations')) === '/translations' ? 'active' : '')}}">
								<a href="/translations">
									<span class="site-menu-title">{{trans('admin.translations')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'languages')
							<li class="site-menu-item {{(substr($url, 0, strlen('/languages')) === '/languages' ? 'active' : '')}}">
								<a href="/languages">
									<span class="site-menu-title">{{trans('admin.languages')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'adjet-templates')
							<li class="site-menu-item {{(substr($url, 0, strlen('/injectionjet-templates')) === '/injectionjet-templates' ? 'active' : '')}}">
								<a href="/injectionjet-templates">
									<span class="site-menu-title">{{trans('admin.all-adjet-templates')}}</span>
								</a>
							</li>
						@endcan

						@can('access', 'all-users')
							<li class="site-menu-item {{(substr($url, 0, strlen('/users')) === '/users' ? 'active' : '')}}">
								<a href="/users">
									<span class="site-menu-title">{{trans('admin.all-users')}}</span>
								</a>
							</li>
						@endcan

                    </ul>
                @endif

				{{-- TODO: Move to array --}}
				{{-- Only show for Dev --}}
                @if(session('admin.user.role_id') == "0")
					<ul class="dev-menu site-menu">
						<li class="site-menu-category">{{trans('admin.dev-tools')}}</li>
						{{--Adding active class to the link--}}
						<li class="site-menu-item {{($url=='/migrations'? 'active' : '')}}">
							<a href="/migrations">
								<span class="site-menu-title">DB Migration</span>
							</a>
						</li>
						{{--Adding active class to the link--}}
						<li class="site-menu-item {{($url=='/cache'? 'active' : '')}}">
							<a href="/cache">
								<span class="site-menu-title">Cache</span>
							</a>
						</li>
						{{--Adding active class to the link--}}
						<li class="site-menu-item {{($url=='/services'? 'active' : '')}}">
							<a href="/services">
								<span class="site-menu-title">Services</span>
							</a>
						</li>
					</ul>
                @endif
			</div>
		</div>
    </div>
</div>

@include('admin.templates.system.menus.widget_menu')


