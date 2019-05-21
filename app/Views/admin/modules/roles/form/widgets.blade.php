<div class="clearfix padding-20">
	<div class="permissions_title padding-0 nav-search">
		@include('admin.templates.system.input-fields.help', ['help' => trans('help.widgets')])
		<div class="input-group form-material search-div">
			<span class="input-group-addon"><i class="fa fa-search"></i></span>
			<input type="text"  id="searchWidgets" class="form-control search-box" name="search" placeholder="{{trans('admin.search-widgets')}}">
		</div>
	</div>


	<div id="activeWidgets" class="widgets-section">
		<h4>{{trans('admin.allowed-default-widgets')}}</h4>
		@include('admin.modules.roles.form.permissions-table', [
			'categoryWithPermissions' => ['widgets' => $activeWidgets],
			'category'	=> 'widgets'
			])
	</div>

	<div id="inactiveWidgets" class="widgets-section">
		<h4>{{trans('admin.allowed-widgets')}}</h4>
		@include('admin.modules.roles.form.permissions-table', [
			'categoryWithPermissions' => ['widgets' => $inactiveWidgets],
			'category'	=> 'widgets'
			])
	</div>

	<div id="disallowedWidgets" class="widgets-section">
		<h4>{{trans('admin.disallowed-widgets')}}</h4>
		@include('admin.modules.roles.form.permissions-table', [
			'categoryWithPermissions' => ['widgets' => $disallowedWidgets],
			'category'	=> 'widgets'
			])
	</div>
</div>
