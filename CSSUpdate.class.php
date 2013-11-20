<?php
/** css 更新类,更新css文件内图片的版本
*	Date: 	2013-02-05
*	Author: fdipzone
*	Ver:	1.1
*
*	Func:
*	update();
*
*   Ver:    1.1 增加search_child参数,可遍历子文件夹
*/

class CSSUpdate{ // class start

    private $csstmpl_path = null;
    private $css_path = null;
    private $replacetags = array();
    private $search_child = false;
    private $convert_num = 0;
    private $is_ready = 0;

    /** 初始化
    * @param String  $csstmpl_path css模版路径
    * @param String  $css_path     css目标路径
    * @param Array   $replacetags  需要替换的图片类型
    * @param boolean $search_child 是否遍历子文件夹,默认false
    */
    public function __construct($csstmpl_path, $css_path, $replacetags=array(), $search_child=false){
        if(!is_dir($csstmpl_path) || !is_dir($css_path) || !$replacetags){
            $this->is_ready = 0;
        }else{
            $this->csstmpl_path = $csstmpl_path;
            $this->css_path = $css_path;
            $this->replacetags = $replacetags;
            $this->search_child = $search_child;
            $this->is_ready = 1;
        }
    }


    /** 更新css文件 */
    public function update(){
        if($this->is_ready==0){
            $this->response('csstmpl or csspath or replacetags error');
            return '';
        }

        $this->traversing($this->csstmpl_path);

        $this->response('covert num:'.$this->convert_num);
    }


    /** 遍历文件夹
    * @param String $path 文件路径
    */
    private function traversing($path){
        $handle = opendir($path);
        while(($file=readdir($handle))!==false){
            if($file!='..' && $file!='.'){
                $curfile = $path.'/'.$file;
                
                if(is_dir($curfile)){   // folder
                    if($this->search_child){    // 需要遍历子文件夹
                        $this->traversing($curfile);
                    }
                }elseif($this->checkExt($curfile)){ // css file
                    $dfile = str_replace($this->csstmpl_path, $this->css_path, $curfile);
                    $this->create($curfile, $dfile);
                    $this->response($curfile.' convert to '.$dfile.' success');
                    $this->convert_num ++;
                }

            }
        }
        closedir($handle);
    }


    /** 检查文件后缀 */
    private function checkExt($file){
        $name = basename($file);
        $namefrag = explode('.', $name);
        if(count($namefrag)>=2){
            if(strtolower($namefrag[count($namefrag)-1])=='css'){ // css文件
                return true;
            }
        }

        return false;
    }


    /** 替换模版内容,写入csspath
    * @param String $tmplfile 模版文件
    * @param String $dfile    目标文件
    */
    private function create($tmplfile, $dfile){
        $css_content = file_get_contents($tmplfile);
        
        foreach($this->replacetags as $tag){
            $css_content = str_replace($tag, $tag."?".date('YmdHis'), $css_content);
        }
        
        if(!is_dir(dirname($dfile))){   // 生成目标路径
            mkdir(dirname($dfile), 0755, true);
        }
        
        file_put_contents($dfile, $css_content, true);
    }


    /** 输出 */
    private function response($content){
        echo $content."<br>";
    }

} // class end

?>