{{--Looping into Categories to create the tabs--}}
<div class="tab-pane permission-tab active" id="category_default">
	{!! trans('admin.roles-default-tab') !!}
</div>
@foreach($categories as $catId => $category)
	@if(!in_array($category, ['role-manage', 'widgets']) )
		<?php
		//$categoryTitle = ucwords(str_replace('|', " > ", str_replace('-', " ", $category)));
		$categoryTitle = trans('admin.' . $category);
		$categoryValue = str_replace(['|','.'], "", $category);
		?>
		<div class="tab-pane permission-tab" id="category_{{$catId}}_tab">
			<div class="floating-tab">
				{{--Tab Title--}}
				<div class="permissions_title padding-0">
					<h3>
						{{$categoryTitle}}
						<small>@include('admin.templates.system.input-fields.help', ['help' => trans('help.'.$category)])</small>
					</h3>
				</div>

				@include('admin.modules.roles.form.permissions-table')
			</div>
		</div>
	@endif
@endforeach

