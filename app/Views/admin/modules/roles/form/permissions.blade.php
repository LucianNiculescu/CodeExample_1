<div class="nav-tabs-vertical">
	{{-- Left hand navigation with search and list of Permissions categories --}}
	@include('admin.modules.roles.form.categories-list')

	{{--showing tabs per role--}}
	<div class="tab-content col-xs-8">
		@include('admin.modules.roles.form.permissions-tab')
	</div>
</div>




