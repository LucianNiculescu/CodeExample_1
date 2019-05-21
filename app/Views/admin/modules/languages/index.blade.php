@extends('admin.templates.system.master')

@section('content')
	<div class="languages">
		<div >
			<h2 class="title">
				{{$title}}<small>{{$description}}</small>
			</h2>

			<div class="form_actions">
				@include('admin.help-pages.button')
			</div>

		</div>
		<form autocomplete="off" id="languagesForm"	class="form-horizontal"	method="post" action="/languages">
			{!! csrf_field() !!}
			<table class="table table-striped table-bordered hover dataTable no-footer">
				<thead>
					<tr>
						<th>
							{{trans('admin.key')}}
						</th>
						<th>
							{{trans('admin.name')}}
						</th>
						<th>
							{{trans('admin.admin')}}
						</th>
						<th>
							{{trans('admin.portal')}}
						</th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td>
						en
					</td>
					<td>
						English
					</td>
					<td>
						@include('admin.templates.system.input-fields.basic.switch',
								[
									'columnName'    	=> 'admin',
									'value'         	=> 'true',
									'customDivClass'    => 'disabled',
								])
					</td>
					<td>
						@include('admin.templates.system.input-fields.basic.switch',
								[
									'columnName'    	=> 'portal',
									'value'         	=> 'true',
									'customDivClass'    => 'disabled',
								])
					</td>
				</tr>
					@foreach($languages as $language)
						<tr>
							<td>
								{{$language['key']}}
							</td>
							<td>
								{{$language['name']}}
							</td>
							<td>
								@include('admin.templates.system.input-fields.basic.switch',
								[
									'columnName'    	=> 'admin' . '-' . $language['key'],
									'value'         	=> $language['admin'],
									'trueValue'			=> 1,
									'falseValue'		=> 0,

								])
							</td>
							<td>

								@include('admin.templates.system.input-fields.basic.switch',
								[
									'columnName'    	=> 'portal' . '-' . $language['key'],
									'value'         	=> $language['portal'],
									'trueValue'			=> 1,
									'falseValue'		=> 0,

								])
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<button type="submit" tabindex="112" class="btn save_all btn-info pull-right margin-top-20 submit-btn" title="{{trans('admin.save')}}" >
				<i class="fa fa-save padding-right-5"></i>
				{{trans('admin.save')}}
			</button>
		</form>
	</div>

@endsection