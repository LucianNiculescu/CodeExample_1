<div id="loginsInLastNWidget" class="bg-guest-reports panel-body">
	@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 240px;', 'spinnerClasses' => 'white'])
	<div id="loginsInLastNContainer">
		<h4>{{ trans('admin.logins-in-last-n-site') }}</h4>
		<ul id="siteLoginsInLastN" class="list-group"></ul>
		<h4>{{ trans('admin.logins-in-last-n-all') }}</h4>
		<ul id="allLoginsInLastN" class="list-group"></ul>
	</div>
</div>
