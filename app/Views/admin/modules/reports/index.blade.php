@extends('admin.templates.system.index')

@section('index-contents')
    <div class="csv-reports">
        <form autocomplete="off" class="form-horizontal" method="post" action="/reports/csv" enctype="multipart/form-data">
            {!! csrf_field() !!}

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-inner-border">
						<h3 class="margin-0">
							{{trans('admin.from-to-dates')}}
							<small>
								@include('admin.templates.system.input-fields.help', ['help' => trans('help.csv-reports|fields|date')])
							</small>
						</h3>

						@include('admin.modules.reports.settings')
					</div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="col-inner-border csv-selector">
                        <div class="list-group">
                            @foreach($types as $type => $permission)
                                @can('access', $permission)
                                    <button type="button" class="list-group-item" data-value="{{$type}}">{{ trans('admin.'.$type)}}</button>
                                @endcan
                            @endforeach
                        </div>
                        <input type="hidden" name="type" value="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" id="csv-submit" class="btn save_all btn-info pull-right margin-left-5 " title="Download" disabled>
                        <i class="fa fa-download padding-right-5"></i>
                        {{trans('admin.download')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection