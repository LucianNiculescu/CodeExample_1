<i
		class="fa fa-question-circle form-help pull-right {{ $extraHelpClass ?? '' }}"
		data-content="{!! str_replace('"', '&#34;', $help) !!}"
		tabindex="-1"
		data-html="true"
		data-trigger="focus"
		data-toggle="popover"
		data-placement="auto right"
		data-original-title="{{ trans('admin.help') }}"
		aria-hidden="true"
		data-container="body"></i>

