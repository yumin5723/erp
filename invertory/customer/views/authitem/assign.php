<?php 
$sidebar_name ='';
include(__DIR__."/../layouts/base_sidebar_authitem.php");?>
<div class="horTab">
    <div class="yiiTab" id="yw0">
<ul class="tabs">
<li><a class="active" href="#tab1">Users*</a></li>
<li><a href="#tab2">Roles*</a></li>
<li><a href="#tab3">Tasks*</a></li>
</ul>
<div id="tab1" class="view">
<!-- USER -> ROLES -->
<div class="srbac">
  <form method="post" action="/srbac/authitem/assign">    <table width="100%">
    <tbody><tr><th colspan="2">Assign Roles to Users*</th></tr>
    <tr>
      <th width="50%">
      <label for="user">User*</label></th>
      <td width="50%" rowspan="2">
        <div id="roles">
          <table width="100%">
  <tbody><tr>
    <th>Assigned Roles*</th>
    <th>&nbsp;</th>
    <th>Not Assigned Roles*</th>
  </tr>
  <tr><td width="45%">
      <select id="AuthItem_name_revoke" name="AuthItem[name][revoke][]" class="dropdown" multiple="multiple" size="10">
</select>    </td>
    <td width="10%" align="center">
      <input type="button" id="yt0" value="&lt;&lt;" name="yt0" disabled="disabled">      <input type="button" id="yt1" value="&gt;&gt;" name="yt1" disabled="disabled">    </td>
    <td width="45%">
      <select id="AuthItem_name_assign" name="AuthItem[name][assign][]" class="dropdown" multiple="multiple" size="10">
</select>    </td></tr>
</tbody></table>
<div id="loadMess" class="message">
  &nbsp;</div>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <td><select id="Manager_id" name="Manager[id]" class="dropdown" size="10">
<option value="1">admin</option>
<option value="21">wang</option>
</select>      </td>
    </tr>
  </tbody></table>
  <br>
  </form></div>
</div><!-- tab1 -->
<div style="display:none" id="tab2" class="view">
<!-- ROLES -> TASKS -->
<div class="srbac">
  <form method="post" action="/srbac/authitem/assign">    <table width="100%">
    <tbody><tr><th colspan="2">Assign Tasks to Roles*</th></tr>
    <tr>
      <th width="50%">
      <label for="role">Role*</label></th>
      <td width="50%" rowspan="2">
        <div id="tasks">
          <table width="100%">
  <tbody><tr>
    <th>Assigned Tasks*</th>
    <th>&nbsp;</th>
    <th>Not Assigned Tasks*</th>
  </tr>
  <tr><td width="45%">
      <select id="AuthItem_name_revoke" name="AuthItem[name][revoke][]" class="dropdown" multiple="multiple" size="10">
</select>    </td>
    <td width="10%" align="center">
      <input type="button" id="yt2" value="&lt;&lt;" name="yt2" disabled="disabled">      <input type="button" id="yt3" value="&gt;&gt;" name="yt3" disabled="disabled">    </td>
    <td width="45%">
      <select id="AuthItem_name_assign" name="AuthItem[name][assign][]" class="dropdown" multiple="multiple" size="10">
</select>    </td></tr>
</tbody></table>
<div class="message" id="loadMessRole">
  &nbsp;</div>
        </div>
      </td>
    </tr>
    <tr valign="top">
      <td><select id="AuthItem_name_0" name="AuthItem[name][0]" class="dropdown" size="10">
<option value="Administrator">Administrator</option>
<option value="Authorizer">Authorizer</option>
<option value="editor">editor</option>
</select>      </td>
    </tr>
  </tbody></table>
  <br>
  </form></div></div><!-- tab2 -->
<div style="display:none" id="tab3" class="view">
<!-- TASKS -> OPERATIONS -->
<div class="srbac">
  <form method="post" action="/srbac/authitem/assign">    <table width="100%">
    <tbody><tr><th colspan="2">Assign Operations to Tasks*</th></tr>
    <tr>
      <th width="50%">
      <label for="task">Task*</label></th>
      <td width="50%" rowspan="2">
        <div id="operations">
          <table width="100%">
  <tbody><tr>
    <th>Assigned Operations*</th>
    <th>&nbsp;</th>
    <th>Not Assigned Operations*</th>
  </tr>
  <tr><td width="45%">
      <select id="AuthItem_name_revoke" name="AuthItem[name][revoke][]" class="dropdown" multiple="multiple" size="10">
</select>    </td>
    <td width="10%" align="center">
      <input type="button" id="yt4" value="&lt;&lt;" name="yt4" disabled="disabled">      <input type="button" id="yt5" value="&gt;&gt;" name="yt5" disabled="disabled">    </td>
    <td width="45%">
      <select id="AuthItem_name_assign" name="AuthItem[name][assign][]" class="dropdown" multiple="multiple" size="10">
</select>    </td></tr>
</tbody></table>
<div class="message" id="loadMessTask">
  &nbsp;</div>        </div>
      </td>
    </tr>
    <tr valign="top">
      <td><select id="Assignments_itemname" name="Assignments[itemname]" class="dropdown" size="10">
</select>        <div>
          Clever Assigning*:
          <input type="checkbox" id="clever" name="clever" value="1">        </div>
      </td>
    </tr>
  </tbody></table>
  <br>

  <div id="loadMessTask" class="message">
      </div>
  </form></div>
</div><!-- tab3 -->
</div>  </div>