<?php
class PluginValidatePid{
  public function validate_pid($field, $form, $data = array()){
    /**
     * 
     */
    wfPlugin::includeonce('i18n/translate_v1');
    $i18n = new PluginI18nTranslate_v1();
    $i18n->path = '/plugin/validate/pid/i18n';
    /**
     * 
     */
    $data = new PluginWfArray($data);
    /**
     * 
     */

    $len = 13;
    $format = 'YYYYMMDD-NNNN';
    if($data->get('skip_delimitator')){
      $len = 12;
      $format = 'YYYYMMDDNNNN';
    }
    /**
     * 
     */
    if(wfArray::get($form, "items/$field/is_valid") && strlen(wfArray::get($form, "items/$field/post_value"))){
      if(!$this->isPid(wfArray::get($form, "items/$field/post_value"), $len)){
        $form = wfArray::set($form, "items/$field/is_valid", false);
        $form = wfArray::set($form, "items/$field/errors/", $i18n->translateFromTheme("?label must be formatted as ?format!", array('?label' => wfArray::get($form, "items/$field/label"), '?format' => $format)));
      }
    }
    /**
     * 
     */
    return $form;
  }
  /**
   * Check if string has length of 13 (or 12) and is swe pid.
   * @param string $pid
   * @return boolean
   */
  private function isPid($pid, $len){
    /**
     * 
     */
    if($len == 13){
      $match = preg_match("/\d{8}\-\d{4}/", $pid);
    }elseif($len == 12){
      $match = preg_match("/\d{12}/", $pid);
    }
    /**
     * 
     */
    if(strlen($pid)!=$len){
      return false;
    }elseif($match){
      /**
       * 
       */
      if(strlen($pid) == 12){
        $pid = substr($pid, 0, 8).'-'.substr($pid, 8);
      }
      /**
       * 
       */
      //https://sv.wikipedia.org/wiki/Personnummer_i_Sverige
      $control = new PluginWfArray();
      $control->set('pid', substr(str_replace('-', '', $pid), 2));
      $control->set('pid_original', $pid);
      $prod = 0;
      for($i=0;$i<strlen($control->get('pid'))-1;$i++){
        $control->set("pos/$i/value", (substr($control->get('pid'), $i, 1)));
        $mult = 1;
        if(($i % 2)==0){
          $mult = 2;
        }
        $control->set("pos/$i/mult", $mult);
        $control->set("pos/$i/value_mult", $control->get("pos/$i/value")*$mult);
        if(strlen($control->get("pos/$i/value_mult"))==1){
          $control->set("pos/$i/prod", $control->get("pos/$i/value_mult"));
        }else{
          $control->set("pos/$i/prod", substr($control->get("pos/$i/value_mult"), 0, 1)+substr($control->get("pos/$i/value_mult"), 1, 1));
        }
        $prod += $control->get("pos/$i/prod");
      }
      $control->set('prod', $prod);
      $control->set('modulus', 10 - ($control->get('prod') % 10));
      if($control->get('modulus')==10){
        $control->set('modulus', 0);
      }
      $control->set('check', substr($control->get('pid'), 9, 1));
      $control->set('ok', false);
      if($control->get('modulus')==$control->get('check')){
        $control->set('ok', true);
      }
      return $control->get('ok');
    }else{
      return false;
    }
  }
}
