<ul class="nav nav-tabs horizontal-tabs">
	<li class="@if(is_null($currentRole)) active @endif">
		<a data-toggle="tab" href="#role-info">
			{{trans('admin.role-info')}}
		</a>
	</li>
	<li class="@if(!is_null($currentRole)) active @endif">
		<a data-toggle="tab" href="#permissions">
			{{trans('admin.permissions')}}
		</a>
	</li>
	<li class="">
		<a data-toggle="tab" href="#role-manage">
			{{trans('admin.role-manage')}}
		</a>
	</li>
	<li class="">
		<a data-toggle="tab" href="#widgets">
			{{trans('admin.widgets')}}
		</a>
	</li>
</ul>

<div class="tab-content ">
	<div class="tab-pane clearfix fade @if(is_null($currentRole)) active in @endif" id="role-info">
		@include('admin.modules.roles.form.role-info')
	</div>
	<div class="tab-pane  clearfix fade @if(!is_null($currentRole)) active in @endif permission-tab" id="permissions">
		@include('admin.modules.roles.form.permissions')
	</div>
	<div class="tab-pane clearfix  fade " id="role-manage">
		@include('admin.modules.roles.form.role-manage')
	</div>
	<div class="tab-pane  clearfix fade " id="widgets">
		@include('admin.modules.roles.form.widgets')
	</div>
</div>
