<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/player/jwplayer.js"></script> 
    
<script type="text/javascript">

    CKEDITOR.replace( 'ckeditor_content', {
        toolbar: 'Full'
    });
    
    $(document).ready(function () { 
        
        //Set for the Content Box
        $('.content-box .content-box-content div.tab-content').hide(); // Hide the content divs
        $('ul.content-box-tabs li a.default-tab').addClass('current'); // Add the class "current" to the default tab
        $('.content-box-content div.default-tab').show(); // Show the div with class "default-tab"

        $('.content-box ul.content-box-tabs li a').click( // When a tab is clicked...
                function() { 
                        $(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
                        $(this).addClass('current'); // Add class "current" to clicked tab
                        var currentTab = $(this).attr('href'); // Set variable "currentTab" to the value of href of clicked tab
                        $(currentTab).siblings().hide(); // Hide all content divs
                        $(currentTab).show(); // Show the content div with the id equal to the id of clicked tab
                        return false; 
                }
        );

        //Minimize the Box
        $(".content-box-header h3").css({ "cursor":"s-resize" }); // Give the h3 in Content Box Header a different cursor
        $(".closed-box .content-box-content").hide(); // Hide the content of the header if it has the class "closed"
        $(".closed-box .content-box-tabs").hide(); // Hide the tabs in the header if it has the class "closed"
        
        $(".content-box-header h3").click( // When the h3 is clicked...
            function () {
              $(this).parent().next().toggle(); // Toggle the Content Box
              $(this).parent().parent().toggleClass("closed-box"); // Toggle the class "closed-box" on the content box
              $(this).parent().find(".content-box-tabs").toggle(); // Toggle the tabs
            }
        );
        
        
        
    });
    
    <?php if($model->isNewRecord) : ?>
    CopyString('#txt_object_name','#txt_object_slug','slug');
    <?php endif; ?>
    CopyString('#txt_object_name','#txt_object_title','');
    CopyString('#txt_object_excerpt','#txt_object_description','');
    CopyString('#txt_object_tags','#txt_object_keywords','');
    
    $('ul.content-box-tabs li:first a:first').addClass('default-tab');
    $('ul.content-box-tabs li:first a:first').addClass('current');
    
    $('#resource-box-content div.tab-content:first').addClass('default-tab').show();
    
    function insertFileToContent(file_type){                        
        $.prettyPhoto.open('/cms/resource/createframe?parent_call=true&ckeditor='+file_type+'&iframe=true&height=400','<?php echo  Yii::t('cms','上传资源');?>','');        
    }
    
    
            
    function afterUploadResourceWithEditor(resource_id,resource_path,file_type,insert_type,width,height,alt){
        var add_width='';
        var add_height='';
        var add_alt='';
        if(width!='0') add_width='width="'+width+'"';
        if(height!='0') add_height='height="'+height+'"';
        if(alt!='') add_alt='alt="'+alt+'"';   
        if(file_type=='image'){
            CKEDITOR.instances['ckeditor_content'].insertHtml('<img '+add_width+' '+add_height+' '+ add_alt+' src="'+resource_path+'"/>');  
        }
        if(file_type=='video'){
            /*
            if(width!='0') add_width="'width': '"+width+"',";
            if(height!='0') add_height="'height': '"+height+"',";
            
            var video_insert="<div id='mediaplayer"+media_count+"'></div>"+         
             '<script type="text/javascript" src="\'+player_path+\'/jwplayer.js"><'+'/script>'+'<script type="text/javascript">'+
              "jwplayer('mediaplayer"+media_count+"').setup({"+
                "'flashplayer': '\"+player_path+\"/player.swf',"+
                "'id': 'playerID"+media_count+"',"+
                add_width+
                add_height+
                "'file': '"+resource_path+"'"+
              '});'+'<'+'/script>';          
              CKEDITOR.instances['ckeditor_content'].insertHtml(video_insert);
              media_count++;    
              */        
        }
            
        
        $.prettyPhoto.close();
    }
  
</script>  