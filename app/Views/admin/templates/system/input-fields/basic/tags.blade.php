<div class="{{$customFullSize or 'col-md-12 col-sm-12'}} {{$customDivClass or ''}} input-container input-container-tags">
    <div class="row">
        @include('admin.templates.system.input-fields.basic.label')

        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="form-group">
                <input {{$validation or ''}} tabindex="{{$tabindex}}" id="{{ $id or 'tags' }}" class="form-control tagging" type="text" value="{{ $value or '' }}" name="{{ $name or 'tags' }}" placeholder="{{ $placeholder or 'Enter your tags here' }}">
            </div>
        </div>

        <div class="col-md-1 hidden-xs hidden-sm padding-0">
			@if(isset($help))
				@include('admin.templates.system.input-fields.help', ['help' => $help, 'extraHelpClass' => 'margin-top-10'])
			@endif
        </div>
    </div>
</div>