<div class="container" id="page">                        
    <div class="page-content">
    <div style="width:100%;float:left" id="inner">
        <div class="form">
        <form method="post" action="/upload/index" id="resource-form" enctype="multipart/form-data">
            <div class="clear"></div>
            <div style="border:2px dotted #CCC;  background:#fff; padding:5px  ; ">
                <div class="row">
                        <label for="ResourceUploadForm_上传">上传</label> 
                         <input type="hidden" name="ResourceUploadForm[upload]" value="" id="ytResourceUploadForm_upload"><input type="file" class="error" id="ResourceUploadForm_upload" name="ResourceUploadForm[upload]" onchange="return fileActive();">                    <div id="ResourceUploadForm_upload_em_" class="errorMessage">Choose File before Upload</div>                    <label for="ResourceUploadForm_链接">链接</label>                    <input type="text" value="" id="ResourceUploadForm_link" name="ResourceUploadForm[link]" onchange="return linkActive();">                    <div style="display:none" id="ResourceUploadForm_link_em_" class="errorMessage"></div>                     
                </div>
            </div>
            <div style="display:none;" class="row">
                    <label for="ResourceUploadForm_name">资源名称</label>                
                    <input type="text" value="" id="ResourceUploadForm_name" name="ResourceUploadForm[name]">                <div style="display:none" id="ResourceUploadForm_name_em_" class="errorMessage"></div>                
            </div>
            <div style="display:none;" class="row">
                    <label for="ResourceUploadForm_body">描述</label>                
                    <textarea id="ResourceUploadForm_body" name="ResourceUploadForm[body]" style="min-height:25px !important;"></textarea>                <div style="display:none" id="ResourceUploadForm_body_em_" class="errorMessage"></div>                
            </div>
            <div>
                <div style="float:left; width:70px" class="row">
                    <label>Width</label>
                    <input type="text" style="width:50px!important;" value="" name="width">
                </div>
                <div style="float:left; width:70px" class="row">
                    <label>Height</label>
                    <input type="text" style="width:50px!important;" value="" name="height">
                </div>
                <div style="float:left; width:120px" class="row">
                    <label>Alt</label>
                    <input type="text" style="width:100px!important;" value="" name="alt">
                </div>
                <div class="clear"></div>
            </div>
            <div class="row buttons">
                 <input type="submit" value="Save" name="yt0" class="bebutton">        
            </div>

        </form>  
        </div><!-- form -->
    </div>
    </div>
</div>