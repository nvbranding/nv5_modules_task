<!-- BEGIN: main -->
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th>{LANG.title}</th>
            <th width="200">{LANG.performer}</th>
            <th width="200">{LANG.status}</th>
        </tr>
    </thead>
    <tbody>
        <!-- BEGIN: task -->
        <tr onclick="nv_table_row_click(event, '{TASK_VIEW.link_view}', false);" class="pointer">
            <td>{TASK_VIEW.title}</td>
            <td>{TASK_VIEW.performer_str}</td>
            <td>{TASK_VIEW.status}</td>
        </tr>
        <!-- END: task -->
    </tbody>
</table>
<!-- END: main -->