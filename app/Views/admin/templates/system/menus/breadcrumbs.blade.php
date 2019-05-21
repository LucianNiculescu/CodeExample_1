<div class="breadcrumbs fluid-container">
    <div class="layout-boxed clearfix">
        <div class="col-xs-7 col-sm-8 col-md-10 pull-left breadcrumbs-inner padding-left-0" >
            <ul class="nav nav-pills">
                @if($siteData['sitesAvailable'] == 'yes')
                    <li class="dropdown active" role="presentation">
                        <a class="dropdown-toggle white padding-left-0" data-toggle="dropdown" href="#" aria-expanded="false" role="button">
                             {{$sites_array[$key]['name'].' ('.$sites_array[$key]['id'].')'}}
                            @if($count > 1)
                                <span class="caret"></span>
                            @endif
                        </a>
                        @if($count > 1)
                            <ul class="dropdown-menu" role="menu">
                                <!-- sortByDesc shows the sites in the opposite order -->
                                @foreach($siteData['sites']->sortByDesc('id') as $siteData)
                                    @if(session('admin.site.loggedin') != $siteData->id)
                                        <li role="presentation">
                                            <a href="/dashboard/{{ $siteData->id }}/true" role="menuitem">{{ $siteData->name }} ({{ $siteData->id }})</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @foreach ($route as $key => $val)
                        <li role="presentation"><i class="fa fa-chevron-right" aria-hidden="true"></i></li>
                        <li role="presentation">
                            @if($currentPage['title'] != $val['title'])
								<!-- show the dropdown of breadcrumb -->
								@if(isset($val['hasDropdown']))
									<li class="dropdown active" role="presentation">
										<a class="dropdown-toggle white padding-left-0" data-toggle="dropdown" href="#" aria-expanded="false" role="button">
											{{trans("admin." . $val['title'])}}
											<span class="caret"></span>
										</a>
										<ul class="dropdown-menu" role="menu">
											@foreach($val['options'] as $option)
												<li role="presentation">
													<a href="{{$option['url']}}" role="menuitem">{{trans($option['title'])}}</a>
												</li>
											@endforeach
										</ul>
									</li>
								@elseif(!empty($val['url']))
                                	<a href="{{url($val['url'])}}" class="white">{{trans("admin." . $val['title'])}}</a>
								@else
									<a class="white unclickable">{{trans("admin." . $val['title'])}}</a>
								@endif
                            @else
                                <a class="current no-hover">
                                    {{trans("admin." . $val['title'])}}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
                @if($siteData['sitesAvailable'] == 'no')
                    <li role="presentation" class="active">
                        <a href="/estate/" class="padding-left-0">{{trans('admin.my-estate')}}</a>
                    </li>
                @endif
            </ul>
		</div>
		<div  class="col-xs-5 col-sm-4 col-md-2  pull-right breadcrumbs-inner padding-right-0">
{{--			<form id="guestSearch" class="guest-search">
				<input class="sb-search-input" placeholder="Enter your search term..." type="text" value="" name="search" id="search">
				<input class="sb-search-submit" type="submit" value="">
				<span class="sb-icon-search"> <i class="input-search-icon wb-search expand-search-icon" aria-hidden="true"></i></span>
			</form>--}}
			@include('admin.search.form')
		</div>
	</div>
</div>