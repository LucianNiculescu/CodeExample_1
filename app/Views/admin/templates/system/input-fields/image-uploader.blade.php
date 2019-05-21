
<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} input-container input-container-input">
	<div class="row">
		@include('admin.templates.system.input-fields.basic.label')

		<div class="{{$customInputSize or 'col-md-8'}} col-sm-12 col-xs-12   {{$extraClass or ''}}">
			<div class="file-upload row  {{ $errors->has($columnName) ? ' has-error' : '' }}">
				<div class="@if(!isset($readonly)) fileinput pull-right @endif  @if(isset($preview) and $preview != '') fileinput-exists @else fileinput-new @endif" data-provides="fileinput">

					@if(isset($default) and $default != '')
						<div class="col-sm-12 col-md-12 fileinput-new thumbnail text-center ">
							<img src="{{$default}}">
							<input type="hidden" name="{{$columnName}}-default" id="{{$columnName}}-default" value="{{$default}}">
						</div>
					@endif

					<div class="col-sm-12 col-md-12 fileinput-preview thumbnail text-center " data-trigger="fileinput" style="{{$style or ''}}" id="{{$columnName}}-preview">
						@if(isset($preview) and $preview != '')
							<img src="{{$preview}}?{{rand()}}">
							<input type="hidden" name="{{$columnName}}-exists" id="{{$columnName}}-exists" value="{{$preview}}">
						@endif
					</div>

					@if(!isset($readonly))
						<div class="col-sm-12 col-md-12 padding-right-0 margin-bottom-10">
							<a href="#" class="btn btn-default fileinput-exists pull-right margin-left-5" data-dismiss="fileinput">{{trans('admin.remove-image')}}</a>
							<span class="btn btn-default btn-file pull-right">
								<span class="fileinput-new">{{trans('admin.select-image')}}</span>
								<span class="fileinput-exists">{{trans('admin.change-image')}}</span>
								<input type="file" name="{{$columnName}}" id="{{$columnName}}" class="validate-ignore" accept="{{$extension or '*'}}">
							</span>
						</div>
					@endif

				</div>

				{{--Showing Validation Error--}}
				@if ($errors->has($columnName))
					<span class="help-block">
						<strong>{{ $errors->first($columnName) }}</strong>
					</span>
				@endif
			</div>
		</div>

		<div class="col-md-1 hidden-xs hidden-sm padding-0">
			@if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
			@endif
		</div>
	</div>
</div>

