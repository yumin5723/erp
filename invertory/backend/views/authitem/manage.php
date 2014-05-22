<?php 
$sidebar_name ='';
include(__DIR__."/../layouts/base_sidebar_authitem.php");?>
<div class="controlPanel marginBottom" style="text-align:left" id="wizardButton">
  <a id="buttonManage" href="#" name="buttonManage">
      <img border="0" alt="Manage AuthItem*" src="" title="Manage AuthItem*" class="icon"> </a>
      <a id="buttonAuto" href="#" name="buttonAuto">
    <img border="0" alt="Autocreate Auth Items*" src="/assets/813f532f/wizard.png" title="Autocreate Auth Items*" class="icon"> </a>  
    <a id="buttonAllowed" href="#" name="buttonAllowed">
    <img border="0" alt="Edit always allowed list*" src="/assets/813f532f/allow.png" title="Edit always allowed list*" class="icon"> </a>  
    <a id="buttonClear" href="#" name="buttonClear">
    <img border="0" alt="Clear obsolete authItems*" src="/assets/813f532f/eraser.png" title="Clear obsolete authItems*" class="icon"> </a>
</div>

<div id="wizard">
  <table align="center" class="srbacDataGrid">
    <tbody><tr>
      <th width="50%">Auth items*</th>
      <th>Actions*</th>
    </tr>
    <tr>
      <td style="vertical-align: top;text-align: center">
        <div id="list">
            <form method="post" action="/srbac/authitem/manage">       <div class="controlPanel">
         <div class="iconBox">
<a id="yt0" href="#"><img border="0" alt="Create*" src="/assets/813f532f/create.png" title="Create*" class="icon">Create*</a>     </div>
     <div style="margin: 0px">
    Search*: &nbsp; <input type="text" name="name" id="name" autocomplete="off" class="ac_input">    <input type="image" border="0" id="yt1" value="submit" name="yt1" src="/assets/813f532f/preview.png" title="Search*">     </div>
   </div>
   <br>
   <table class="srbacDataGrid">
     <tbody><tr>
       <th>Name*</th>
       <th>
      <select id="selectedType" name="selectedType">
<option value="">All*</option>
<option value="0">Operation</option>
<option value="1">Task</option>
<option value="2">Role</option>
</select>     </th>
     <th colspan="2">Actions*</th>
   </tr>
         <tr class="odd">
           <td><a id="yt2" href="#" title="Authorizer">Authorizer</a></td>
       <td>Role</td>
       <td>
      <a id="yt3" href="#"><img border="0" alt="Update*" src="/assets/813f532f/update.png" title="Update*"></a>       </td>
       <td>
                   </td>
           </tr>
           <tr class="even">
           <td><a id="yt4" href="#" title="Administrator">Administrator</a></td>
       <td>Role</td>
       <td>
      <a id="yt5" href="#"><img border="0" alt="Update*" src="/assets/813f532f/update.png" title="Update*"></a>       </td>
       <td>
            <a id="yt6" href="#"><img border="0" alt="Delete*" src="/assets/813f532f/delete.png" title="Delete*"></a>             </td>
           </tr>
           <tr class="odd">
           <td><a id="yt7" href="#" title="editor">editor</a></td>
       <td>Role</td>
       <td>
      <a id="yt8" href="#"><img border="0" alt="Update*" src="/assets/813f532f/update.png" title="Update*"></a>       </td>
       <td>
            <a id="yt9" href="#"><img border="0" alt="Delete*" src="/assets/813f532f/delete.png" title="Delete*"></a>             </td>
           </tr>
         </tbody></table>
  </form>       <br>
       <div class="simple">
</div>
        </div>
      </td>
      <td style="vertical-align: top;text-align: center">
        <div id="preview">

        </div>
      </td>
    </tr>
  </tbody></table>
</div>