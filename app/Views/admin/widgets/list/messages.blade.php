
<div class="padding-0">
    <div class="height-450 width-full padding-0 system-scrollbar" >
            <div class="system-scrollbar-inner">
                <ul class="list-group ">
                    <!-- json message pull in here-->
                </ul>
            </div>
    </div>

	<div class="loading">
		<p class="center-text black">{{trans('admin.loading')}}</p>
		<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
		<span class="sr-only">{{trans('admin.loading')}}</span>
	</div>

</div>

	<div class="panel-footer" style="margin-bottom:0;height: 41px;">
		@can('access', 'system.messages')
			<a href="/system/messages" class="">{{trans('admin.see-messages')}}</a>
		@endcan
	</div>

