<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css" />
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2-bootstrap.min.css" />
<link type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<div class="well">
    <form action="{NV_BASE_SITEURL}index.php" method="get">
        <input type="hidden" name="search" value="1" /><input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" /> <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" /> <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
        <div class="row">
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <input class="form-control" type="text" value="{SEARCH.q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <select class="form-control select2" name="useradd">
                        <option value="0">---{LANG.useradd_select}---</option>
                        <!-- BEGIN: useradd -->
                        <option value="{USERADD.userid}"{USERADD.selected}>{USERADD.fullname}</option>
                        <!-- END: useradd -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <select class="form-control select2" name="userid">
                        <option value="0">---{LANG.performer_select}---</option>
                        <!-- BEGIN: user -->
                        <option value="{USER.userid}"{USER.selected}>{USER.fullname}</option>
                        <!-- END: user -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="-1">---{LANG.status_select}---</option>
                        <!-- BEGIN: status -->
                        <option value="{STATUS.index}"{STATUS.selected}>{STATUS.value}</option>
                        <!-- END: status -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-4">
                <div class="form-group">
                    <select name="priority" class="form-control">
                        <option value="">---{LANG.priority_select}---</option>
                        <!-- BEGIN: priority -->
                        <option value="{PRIORITY.index}"{PRIORITY.selected}>{PRIORITY.value}</option>
                        <!-- END: priority -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <select name="datesource" class="form-control">
                        <option value="">---{LANG.datesource_select}---</option>
                        <!-- BEGIN: datesource -->
                        <option value="{DATESOURCE.index}"{DATESOURCE.selected}>{DATESOURCE.value}</option>
                        <!-- END: datesource -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control datepicker" value="{SEARCH.from}" type="text" name="from" placeholder="{LANG.filter_from}" /> <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <em class="fa fa-calendar fa-fix">&nbsp;</em>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-24 col-md-5">
                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control datepicker" value="{SEARCH.to}" type="text" name="to" placeholder="{LANG.filter_to}" /> <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <em class="fa fa-calendar fa-fix">&nbsp;</em>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
                </div>
            </div>
        </div>
    </form>
</div>
<form class="form-inline m-bottom">
    <select class="form-control" id="action-top">
        <!-- BEGIN: action_top -->
        <option value="{ACTION.key}">{ACTION.value}</option>
        <!-- END: action_top -->
    </select>
    <button class="btn btn-primary" onclick="nv_list_action( $('#action-top').val(), '{BASE_URL}', '{LANG.error_empty_data}' ); return false;">{LANG.perform}</button>
    <a href="{URL_ADD}" class="btn btn-primary">{LANG.task_add}</a> <a href="{URL_EXCEL}" target="_blank" class="btn btn-primary <!-- BEGIN: btn_disabled -->disabled<!-- END: btn_disabled -->"><em class="fa fa-file-excel-o">&nbsp;</em>{LANG.task_export}</a>
</form>
<form action="{NV_BASE_SITEURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="50" class="text-center"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);"></th>
                    <th>{LANG.title}</th>
                    <th width="130">{LANG.begintime_s}</th>
                    <th width="130">{LANG.exptime_s}</th>
                    <th width="150">{LANG.useradd}</th>
                    <th>{LANG.performer}</th>
                    <th width="130">{LANG.status}</th>
                    <th width="100">{LANG.priority}</th>
                    <th width="70">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="10">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr onclick="nv_table_row_click(event, '{VIEW.link_view}', false);" class="pointer <!-- BEGIN: warning -->danger<!-- END: warning -->">
                    <td class="text-center"><input type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{VIEW.id}" name="idcheck[]" class="post"></td>
                    <td class="form-tooltip">{VIEW.title} <!-- BEGIN: project --> <a href="{VIEW.project.link}"><em class="fa fa-briefcase pull-right" data-toggle="tooltip" data-original-title="{LANG.project}: {VIEW.project.title}">&nbsp;</em></a> <!-- END: project -->
                    </td>
                    <td>{VIEW.begintime_start}</td>
                    <td>{VIEW.exptime}</td>
                    <td>{VIEW.useradd_str}</td>
                    <td>{VIEW.performer_str}</td>
                    <td>{VIEW.status}</td>
                    <td class="{VIEW.priority_class}">{VIEW.priority}</td>
                    <td class="text-center form-tooltip">
                        <!-- BEGIN: admin --> <a href="{VIEW.link_edit}" class="btn btn-default btn-xs" data-toggle="tooltip" data-original-title="{LANG.edit}"><i class="fa fa-edit"></i></a> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);" class="btn btn-default btn-xs" data-toggle="tooltip" data-original-title="{LANG.delete}"><em class="fa fa-trash-o"></em></a> <!-- END: admin -->
                    </td>
                </tr>
                <tr class="percent">
                    <td colspan="9">
                        <!-- BEGIN: percent -->
                        <div style="background-color: blue; height: 4px; width: {VIEW.percent"></div> <!-- END: percent --> <!-- BEGIN: nonepercent -->
                        <div style="background-color: red; height: 4px; width: {VIEW.percent"></div> <!-- END: nonepercent -->
                    </td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<form class="form-inline m-bottom">
    <select class="form-control" id="action-bottom">
        <!-- BEGIN: action_bottom -->
        <option value="{ACTION.key}">{ACTION.value}</option>
        <!-- END: action_bottom -->
    </select>
    <button class="btn btn-primary" onclick="nv_list_action( $('#action-bottom').val(), '{BASE_URL}', '{LANG.error_empty_data}' ); return false;">{LANG.perform}</button>
</form>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/i18n/{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.ui.datepicker-{NV_LANG_INTERFACE}.js"></script>
<script>
    $(".datepicker").datepicker({
        dateFormat : "dd/mm/yy",
        changeMonth : !0,
        changeYear : !0,
        showOtherMonths : !0,
        showOn : "focus",
        yearRange : "-90:+0"
    });
    
    $('.select2').select2({
        language : '{NV_LANG_INTERFACE}',
        theme : 'bootstrap'
    });
</script>
<!-- END: main -->