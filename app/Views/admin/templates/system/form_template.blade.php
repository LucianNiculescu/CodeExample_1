@extends('admin.templates.system.master')

{{--@push('footer-js')--}}


{{--<script>--}}


    {{--// Editing a new role removes the empty class otherwise the label will float over the role input field--}}
    {{--$("input#role").change(function () {--}}
        {{--$(this).removeClass("empty");--}}
    {{--});--}}
{{--</script>--}}
{{--@endpush--}}


@section('content')

    <form autocomplete="off" class="form-horizontal permissions base_form" method="post">
    {!! csrf_field() !!}
    <input name="_method" type="hidden" >

    {{--top right div with action buttons, add save and cancel--}}
    <div class="form_actions clearfix">

        {{--Cancel button--}}
        <a href="/roles" class="btn btn-default btn-sm pull-right" title="Cancel">
            <i class="fa fa-ban fa_circle"></i>
        </a>

        {{--Save all button--}}
        <button type="submit" class="btn btn-info btn-sm pull-right" title="Save">
            <i class="fa fa-save"></i>
        </button>


    </div>
    <div class="contents_frame">
        <div class="container roles_crud ">
            <div class="row">



                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->

                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Text Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->



                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Text Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div>

            </div><!-- end row -->
            <div class="row">
                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->

                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Text Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->




                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class="col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">File Upload</label>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="input-group input-group-file">
                                <input type="text" class="form-control" readonly="">
                                        <span class="input-group-btn">
                                          <span class="btn btn-primary btn-file">
                                            <i class="icon wb-upload" aria-hidden="true"></i>
                                            <input type="file" name="" multiple="" placeholder="Upload file">
                                          </span>
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>


                </div>
                <div class="row">
                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->

                <div class="col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Email Field</label>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="email" class="form-control" id="inputPlaceholder" placeholder="Enter your email">
                        </div>
                    </div>
                </div>

                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->



                <div class="col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Date Field</label>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="date" class="form-control" id="inputPlaceholder" >
                        </div>
                    </div>
                </div>
                </div>
            <div class="row">

                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class="col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">URL Field</label>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="url" class="form-control" id="inputPlaceholder" placeholder="Enter URL">
                        </div>
                    </div>
                </div>


                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->

                <div class="col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Colour Picker</label>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="asColorpicker form-control" data-plugin="asColorPicker"
                                   data-mode="simple" value="#fa7a7a" />
                        </div>
                    </div>
                </div>
                </div>

            <div class="row">

                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->



                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Slide Picker</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Slide to pick a value" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="asRange" data-plugin="asRange" data-namespace="rangeUi" data-step="1"
                                 data-min="0" data-max="12" data-value="7" data-tip=true></div>
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end col-md-6 -->




                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Dropdown Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <select id="widgetSelect" name="widgetSelect[]" class="form-control chosen-select" placeholder="Select all default widgets">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                            </select>
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div>

            </div><!-- end row -->

            <div class="row">
                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Text Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->


                <!----------------------------------------------------------------------------------------------
                                   ----------------------------------------------------------------------------------------------->


                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Text Field</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->
                </div>

            <div class="row">

                <!----------------------------------------------------------------------------------------------
               ----------------------------------------------------------------------------------------------->



                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">

                        <label for="">Text Field</label>


                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->



                <!----------------------------------------------------------------------------------------------
               ----------------------------------------------------------------------------------------------->



                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">

                        <label for="">Text Field</label>


                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="inputPlaceholder" placeholder="Text Field">
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->

</div>
            <div class="row">
                <!----------------------------------------------------------------------------------------------
               ----------------------------------------------------------------------------------------------->




                <div class=" col-md-12">
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Date Range</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right pull-right"
                           data-content="Add an alpha numeric role name" tabindex="0"
                           data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                           aria-hidden="true"></i>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="input-daterange" data-plugin="datepicker">
                                <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                        </span>
                                    <input type="text" class="form-control" name="start" />
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control" name="end" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <!----------------------------------------------------------------------------------------------
             ----------------------------------------------------------------------------------------------->

                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Textarea</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea class="form-control" id="textareaDefault" rows="5"></textarea>
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->


                <!----------------------------------------------------------------------------------------------
                                 ----------------------------------------------------------------------------------------------->

                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Checkbox</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" id="inputUnchecked">
                                    <label for="inputUnchecked">Unchecked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" id="inputChecked" checked="">
                                    <label for="inputChecked">Checked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" disabled="">
                                    <label>Disabled Unchecked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" disabled="" checked="">
                                    <label>Checked Disabled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->

                <!----------------------------------------------------------------------------------------------
           ----------------------------------------------------------------------------------------------->

            </div>
            <div class="row">


                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Radio Buttons</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="inputRadiosUnchecked" name="inputRadios">
                                    <label for="inputRadiosUnchecked">Unchecked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="inputRadiosChecked" name="inputRadios" checked="">
                                    <label for="inputRadiosChecked">Checked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="inputRadiosDisabled" name="inputRadiosDisabled" disabled="">
                                    <label for="inputRadiosDisabled">Disabled Unchecked</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="inputRadiosDisabledChecked" name="inputRadiosDisabledChecked" disabled="" checked="">
                                    <label for="inputRadiosDisabledChecked">Checked Disabled</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end main form field section -->

                </div><!-- end row -->

                <!----------------------------------------------------------------------------------------------
                                 ----------------------------------------------------------------------------------------------->

                <div class=" col-md-6">
                    <!-- heading and help icon section-->
                    <div class="col-md-10 col-sm-8 col-xs-9 padding-0">
                        <label for="">Checkbox</label>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-3 padding-0">
                        <i class="fa fa-question-circle form-help pull-right"
                                                 data-content="Add an alpha numeric role name" tabindex="0"
                                                 data-trigger="focus" data-toggle="popover" data-original-title="Help" data-placement="auto right"
                                                 aria-hidden="true"></i>
                    </div>
                    <!-- end of heading and help icon section-->
                    <!-- start main form field section -->
                    <div class="col-md-12">
                        <div class="form-group">

                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="pull-left margin-right-20">
                                    <input type="checkbox" id="inputBasicOff" name="inputiCheckBasicCheckboxes" data-plugin="switchery"
                                    />
                                </div>
                                <label class="padding-top-3" for="inputBasicOff">Off</label>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="pull-left margin-right-20">
                                    <input type="checkbox" id="inputBasicOn" name="inputiCheckBasicCheckboxes" data-plugin="switchery"
                                           checked />
                                </div>
                                <label class="padding-top-3" for="inputBasicOn">On</label>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="pull-left margin-right-20">
                                    <input type="checkbox" id="inputBasicDisabledOff" name="inputiCheckBasicCheckboxes"
                                           data-plugin="switchery" data-disabled="true" />
                                </div>
                                <label class="padding-top-3" for="inputBasicDisabledOff">Disabled Off</label>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="pull-left margin-right-20">
                                    <input type="checkbox" id="inputBasicDisabledOn" name="inputiCheckBasicCheckboxes"
                                           data-plugin="switchery" data-disabled="true" checked />
                                </div>
                                <label class="padding-top-3" for="inputBasicDisabledOn">Disabled On</label>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
                <!-- end main form field section -->

            </div><!-- end row -->


        </div>
    </div>
    </div>
    <div class="buttons">
        <a href="/roles" type="button" class="btn btn-default pull-right" title="Cancel">
            <i class="fa fa-ban"></i>
            &nbsp Cancel
        </a>
        <button type="submit" class="btn save_all btn-info pull-right" title="Save">
            <i class="fa fa-save"></i>
            &nbsp Save
        </button>
    </div>

    </form>

    @endsection