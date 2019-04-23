<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/timepicker/jquery.timepicker.min.css">
<form class="form-horizontal" action="{NV_BASE_SITEURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post" id="frm-submit">
    <div class="panel panel-default">
        <div class="panel-body">
            <input type="hidden" name="id" value="{ROW.id}" /> <input type="hidden" name="redirect" value="{ROW.redirect}" />
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.title}</strong> <span class="red">(*)</span></label>
                <div class="col-sm-19 col-md-20">
                    <input class="form-control" type="text" name="title" id="title" value="{ROW.title}" required="required" oninvalid="setCustomValidity( nv_required )" oninput="setCustomValidity('')" />
                </div>
            </div>
            <!-- BEGIN: allow_useradd -->
            <div class="form-group" id="useradd">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.useradd}</strong> <span class="red">(*)</span></label>
                <div class="col-sm-19 col-md-20">
                    <select class="form-control select2" name="useradd" style="width: 100%">
                        <option value="0">---{LANG.useradd_select}---</option>
                        <!-- BEGIN: useradd -->
                        <option value="{USERADD.userid}"{USERADD.selected}>{USERADD.fullname}</option>
                        <!-- END: useradd -->
                    </select>
                </div>
            </div>
            <!-- END: allow_useradd -->
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.performer}</strong> <span class="red">(*)</span></label>
                <div class="col-sm-19 col-md-20">
                    <div class="row">
                        <div class="col-xs-24 col-sm-12 col-md-12">
                            <select class="form-control select2" name="performer[]" multiple="multiple" style="width: 100%">
                                <option value="0">---{LANG.performer_select}---</option>
                                <!-- BEGIN: leader -->
                                <optgroup label="{LANG.leader}">
                                    <!-- BEGIN: loop -->
                                    <option value="{USER.userid}"{USER.selected}>&nbsp;&nbsp;&nbsp;&nbsp;{USER.space}{USER.fullname}</option>
                                    <!-- END: loop -->
                                </optgroup>
                                <!-- END: leader -->
                                <!-- BEGIN: member -->
                                <optgroup label="{LANG.member}">
                                    <!-- BEGIN: loop -->
                                    <option value="{USER.userid}"{USER.selected}>&nbsp;&nbsp;&nbsp;&nbsp;{USER.space}{USER.fullname}</option>
                                    <!-- END: loop -->
                                </optgroup>
                                <!-- END: member -->
                            </select>
                        </div>
                        <div class="col-xs-24 col-sm-12 col-md-12">
                            <select class="form-control select2" name="groups_made[]" multiple="multiple" style="width: 100%">
                                <option value="0">---{LANG.groups_made_select}---</option>
                                <!-- BEGIN: groups -->
                                <option value="{GROUP.index}"{GROUP.selected}>{GROUP.value}</option>
                                <!-- END: groups -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.begintime}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="input-group">
                                <input class="form-control datepicker" type="text" name="begindate" value="{ROW.begindate}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" autocomplete="new-password" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <em class="fa fa-calendar fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="input-group">
                                <input class="form-control timepicker" type="text" name="begintime" value="{ROW.begintime}" autocomplete="new-password" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <em class="fa fa-clock-o fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.exptime}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="input-group">
                                <input class="form-control datepicker" type="text" name="expdate" value="{ROW.expdate}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" autocomplete="new-password" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <em class="fa fa-calendar fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="input-group">
                                <input class="form-control timepicker" type="text" name="exptime" value="{ROW.exptime}" autocomplete="new-password" /> <span class="input-group-btn">
                                    <button class="btn btn-default" type="button">
                                        <em class="fa fa-clock-o fa-fix"> </em>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.description}</strong></label>
                <div class="col-sm-19 col-md-20">{ROW.description}</div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.status}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <select name="status" class="form-control">
                        <!-- BEGIN: status -->
                        <option value="{STATUS.index}"{STATUS.selected}>{STATUS.value}</option>
                        <!-- END: status -->
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.priority}</strong></label>
                <div class="col-sm-19 col-md-20">
                    <select class="form-control" name="priority">
                        <!-- BEGIN: looppriority -->
                        <option value="{VALUE.index}"{VALUE.selected}>{VALUE.value}</option>
                        <!-- END: looppriority -->
                    </select>
                </div>
            </div>
            <!-- BEGIN: field -->
            <!-- BEGIN: loop -->
            <div class="form-group">
                <label class="col-sm-5 col-md-4 control-label"> <strong>{FIELD.title}</strong> <!-- BEGIN: required --> <span class="red">(*)</span> <!-- END: required -->
                </label>
                <div class="col-sm-19 col-md-20">
                    <!-- BEGIN: textbox -->
                    <input class="form-control {FIELD.required}" type="text" name="custom_fields[{FIELD.field}]" id="{FIELD.field}" value="{FIELD.value}" />
                    <!-- END: textbox -->
                    <!-- BEGIN: date -->
                    <div class="input-group">
                        <input class="form-control datepicker {FIELD.required}" id="{FIELD.field}" type="text" name="custom_fields[{FIELD.field}]" value="{FIELD.value}" pattern="^[0-9]{2,2}\/[0-9]{2,2}\/[0-9]{1,4}$" /> <span class="input-group-btn">
                            <button class="btn btn-default" type="button">
                                <em class="fa fa-calendar fa-fix"> </em>
                            </button>
                        </span>
                    </div>
                    <!-- END: date -->
                    <!-- BEGIN: textarea -->
                    <textarea rows="5" cols="70" class="form-control" id="{FIELD.field}" name="custom_fields[{FIELD.field}]">{FIELD.value}</textarea>
                    <!-- END: textarea -->
                    <!-- BEGIN: editor -->
                    {EDITOR}
                    <!-- END: editor -->
                    <!-- BEGIN: select -->
                    <select class="form-control" id="{FIELD.field}" name="custom_fields[{FIELD.field}]">
                        <!-- BEGIN: loop -->
                        <option value="{FIELD_CHOICES.key}"{FIELD_CHOICES.selected}>{FIELD_CHOICES.value}</option>
                        <!-- END: loop -->
                    </select>
                    <!-- END: loopselect -->
                    <!-- BEGIN: radio -->
                    <label for="lb_{FIELD_CHOICES.id}"> <input type="radio" name="custom_fields[{FIELD.field}]" value="{FIELD_CHOICES.key}" id="lb_{FIELD_CHOICES.id}"{FIELD_CHOICES.checked}> {FIELD_CHOICES.value}
                    </label>
                    <!-- END: radio -->
                    <!-- BEGIN: checkbox -->
                    <label for="lb_{FIELD_CHOICES.id}"> <input type="checkbox" name="custom_fields[{FIELD.field}][]" value="{FIELD_CHOICES.key}" id="lb_{FIELD_CHOICES.id}"{FIELD_CHOICES.checked}> {FIELD_CHOICES.value}
                    </label>
                    <!-- END: checkbox -->
                    <!-- BEGIN: multiselect -->
                    <select class="form-control" id="{FIELD.field}" name="custom_fields[{FIELD.field}][]" multiple="multiple">
                        <!-- BEGIN: loop -->
                        <option value="{FIELD_CHOICES.key}"{FIELD_CHOICES.selected}>{FIELD_CHOICES.value}</option>
                        <!-- END: loop -->
                    </select>
                    <!-- END: multiselect -->
                    <small class="help-block"><em>{FIELD.description}</em></small>
                </div>
            </div>
            <!-- END: loop -->
            <!-- END: field -->
        </div>
    </div>
    <div class="form-group text-center button_fixed_bottom">
        <input type="hidden" name="submit" value="1" /> <input type="hidden" name="ajax" value="{ROW.ajax}" /> <input type="hidden" name="projectid" value="{ROW.projectid}" /> <input class="btn btn-primary loading" type="submit" id="btn-submit" value="{LANG.save}" />
    </div>
</form>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/timepicker/jquery.timepicker.min.js"></script>
<script type="text/javascript">
    //<![CDATA[
    $('.select2').select2({
        language : '{NV_LANG_INTERFACE}',
        theme : 'bootstrap'
    });
    
    $('.timepicker').timepicker({
        timeFormat : 'HH:mm',
        interval : 30,
        minTime : '30',
        maxTime : '11:59pm',
        defaultTime : 'value',
        startTime : '07:00',
        dynamic : false,
        dropdown : true,
        scrollbar : true
    });
    
    $(".datepicker").datepicker({
        dateFormat : "dd/mm/yy",
        changeMonth : true,
        changeYear : true,
        showOtherMonths : true,
        showOn : "focus",
        yearRange : "-90:+5",
    });
    
    $('#frm-submit').submit(function(e) {
        e.preventDefault();
        
        if (typeof (CKEDITOR) !== "undefined") {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
        
        $.ajax({
            type : 'POST',
            url : script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=content&nocache=' + new Date().getTime(),
            data : $(this).serialize(),
            beforeSend : function() {
                $('#btn-submit').prop('disabled', true);
            },
            success : function(json) {
                if (json.msg) {
                    alert(json.msg);
                }
                if (json.error) {
                    $('#' + json.input).focus();
                    $('#btn-submit').prop('disabled', false);
                    $('body').removeClass('ajax-load-qa');
                } else if (json.ajax && json.projectid > 0) {
                    window.parent.nv_projects_task_list(json.projectid);
                    $('#sitemodal').modal('toggle');
                } else {
                    window.location.href = json.redirect;
                }
            }
        });
    })

    //]]>
</script>
<!-- END: main -->