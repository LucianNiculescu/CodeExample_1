<div id="genderWidget" class="bg-guest-reports panel-body">

	<div id="genderContainer" class="">

		<div class="row">
			<div class="col-xs-2 ">
				<span class=" gender-icon " ><i class="fa fa-female white" aria-hidden="true"></i></span>
			</div>
			<div class="col-xs-10 margin-top-10">
				<span class="white percentage" id="female-percentage"></span><span class="gender white">{{trans('admin.female')}} </span>
				<div class="progress">
					<div class="progress-bar female-bar" style=" width: 0%;"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-2 ">
				<span class=" gender-icon " ><i class="fa fa-male white" aria-hidden="true"></i></span>
			</div>
			<div class="col-xs-10 margin-top-10">
				<span class="white percentage" id="male-percentage"></span><span class="gender white">{{trans('admin.male')}}</span>
				<div class="progress">
					<div class="progress-bar male-bar" style=" width:0%;"></div>
				</div>

			</div>
		</div>
	</div>
</div>

<script>

	var genderDataArray = new Array(0,0);

	@if(isset($dashboardData))
		<?php
			// TODO: Move to the logic!!!!!!!
			$oneHundredPercent = $dashboardData->reg_users_female + $dashboardData->reg_users_male;

			if( $oneHundredPercent != 0 )
			{
				$female = round(($dashboardData->reg_users_female / $oneHundredPercent) * 100);
				$male = 100 - $female;
			}
			else
			{
				$female = 0;
				$male = 0;
			}
		?>
		genderDataArray = new Array( {{$female }}, {{ $male }});
	@endif

</script>