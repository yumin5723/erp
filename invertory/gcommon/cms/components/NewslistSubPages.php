<?php
namespace gcommon\cms\components;

class NewslistSubPages extends SubPages{
    function subPageCss2() {
        $subPageCss2Str = "";
        // $subPageCss2Str.='<div class="fr paging_page">';
        if ($this->current_page > 1) {
            $firstPageUrl = $this->subPage_link . "index.html";
            if($this->current_page - 1 == 1){
                $prewPageUrl = $firstPageUrl;
            }else{
                $prewPageUrl = $this->subPage_link . ($this->current_page - 1).".html";
            }
            $subPageCss2Str.="<a href='$firstPageUrl'>首页</a> ";
            $subPageCss2Str.="<a href='$prewPageUrl'>上一页</a> ";
        } else {
            //$subPageCss2Str ='';
        }

        $a = $this->construct_num_Page();
        if(count($a) > 1){
            for ($i = 0; $i < count($a); $i++) {
                $s = $a[$i];
                if ($s == $this->current_page) {
                    $subPageCss2Str.="<span class='paging_number'>" . $s . "</span>";
                } else {
                    if($s == 1){
                        $url = $this->subPage_link . "index.html";
                    }else{
                        $url = $this->subPage_link . $s.".html";
                    }
                    $subPageCss2Str.="<a class='paging_number' href='$url'>" . $s . "</a>";
                }
            }
            $key = count($a)-1;
            $maxnum = $a[$key];
            if($maxnum<$this->pageNums){
                $subPageCss2Str.="<a class='paging_number'>" . '...' . "</a>";
            }
        }
        if ($this->current_page < $this->pageNums) {
            $lastPageUrl = $this->subPage_link . $this->pageNums.".html";
            $nextPageUrl = $this->subPage_link . ($this->current_page + 1).".html";
            $subPageCss2Str.=" <a href='$nextPageUrl'>下一页</a> ";
            $subPageCss2Str.="<a href='$lastPageUrl'>尾页</a>";
        } else {
            
        }
        // $subPageCss2Str.='</div>';
        return $subPageCss2Str;
    }

}
