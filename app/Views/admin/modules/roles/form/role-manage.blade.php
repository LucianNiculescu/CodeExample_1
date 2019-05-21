<div class="clearfix padding-20">
	<div class="permissions_title padding-0 nav-search">
		@include('admin.templates.system.input-fields.help', ['help' => trans('help.role-manage')])
		<div class="input-group form-material search-div">
			<span class="input-group-addon"><i class="fa fa-search"></i></span>
			<input type="text"  id="searchRoleManage" class="form-control search-box" name="search" placeholder="{{trans('admin.search-role-manage')}}">
		</div>
	</div>

	@include('admin.modules.roles.form.permissions-table', [
	'categoryWithPermissions' => ['role-manage' => $categoryWithPermissions['role-manage'] ?? []],
	'category'	=> 'role-manage'
	])
</div>