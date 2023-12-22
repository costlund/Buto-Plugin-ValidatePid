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
    if($data->get('organisation')){
      $len = 11;
      $format = 'NNNNNN-NNNN';
    }elseif($data->get('skip_delimitator')){
      $len = 12;
      $format = 'YYYYMMDDNNNN';
    }
    /**
     * 
     */
    if(wfArray::get($form, "items/$field/is_valid") && wfPhpfunc::strlen(wfArray::get($form, "items/$field/post_value"))){
      $is_pid = $this->isPid(wfArray::get($form, "items/$field/post_value"), $len);
      if(!$is_pid->get('ok')){
        $form = wfArray::set($form, "items/$field/is_valid", false);
        $form = wfArray::set($form, "items/$field/errors/", $i18n->translateFromTheme($is_pid->get('message'), array('?label' => wfArray::get($form, "items/$field/label"), '?format' => $format)));
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
   * @return PluginWfArray
   */
  public function isPid($pid, $len = null){
    /**
     * len
     */
    if(is_null($len)){
      $len = wfPhpfunc::strlen($pid);
    }
    /**
     * 
     */
    $control = new PluginWfArray();
    /**
     * 
     */
    if($len == 13){
      $match = preg_match("/\d{8}\-\d{4}/", $pid);
    }elseif($len == 12){
      $match = preg_match("/\d{12}/", $pid);
    }elseif($len == 11){
      $match = preg_match("/\d{6}\-\d{4}/", $pid);
    }elseif($len == 10){
      $match = preg_match("/\d{10}/", $pid);
    }else{
      $control->set('ok', false);
      $control->set('message', '?label has a length failure!');
      return $control;
    }
    /**
     * 
     */
    if(wfPhpfunc::strlen($pid)!=$len){
      $control->set('ok', false);
      $control->set('message', '?label has a length failure!');
      return $control;
    }elseif($match){
      /**
       * 
       */
      if(wfPhpfunc::strlen($pid) == 12){
        $pid = wfPhpfunc::substr($pid, 0, 8).'-'.substr($pid, 8);
      }
      /**
       * pid has now format 010203XXXX (ten digits)
       */
      $control->set('pid_original', $pid);
      /**
       * 
       */
      $control->set('coordination_number', null);
      $control->set('born', null);
      $control->set('sex', null);
      /**
       * 
       */
      if($len == 12 || $len == 13){
        $control->set('pid', wfPhpfunc::substr(wfPhpfunc::str_replace('-', '', $pid), 2));
      }else{
        $control->set('pid', wfPhpfunc::str_replace('-', '', $pid));
      }
      /**
       * 
       */
      $prod = 0;
      $coordination_number = false;
      $born = null;
      $sex = null;
      for($i=0;$i<wfPhpfunc::strlen($control->get('pid'))-1;$i++){
        $control->set("pos/$i/value", (wfPhpfunc::substr($control->get('pid'), $i, 1)));
        $mult = 1;
        if(($i % 2)==0){
          $mult = 2;
        }
        $control->set("pos/$i/mult", $mult);
        $control->set("pos/$i/value_mult", $control->get("pos/$i/value")*$mult);
        if(wfPhpfunc::strlen($control->get("pos/$i/value_mult"))==1){
          $control->set("pos/$i/prod", $control->get("pos/$i/value_mult"));
        }else{
          $control->set("pos/$i/prod", wfPhpfunc::substr($control->get("pos/$i/value_mult"), 0, 1)+substr($control->get("pos/$i/value_mult"), 1, 1));
        }
        $prod += $control->get("pos/$i/prod");
        /**
         * coordination_number
         */
        if($i==4){
          if(wfPhpfunc::substr($control->get('pid'), $i, 1)>3){
            $coordination_number = true;
          }
        }
        /**
         * born
         */
        if($i<=5){
          if($i!=4){
            $born .= wfPhpfunc::substr($control->get('pid'), $i, 1);
          }else{
            if(!$coordination_number){
              $born .= wfPhpfunc::substr($control->get('pid'), $i, 1);
            }else{
              $born .= wfPhpfunc::substr($control->get('pid'), $i, 1)-6;
            }
          }
        }
        if($i == 1 || $i==3){
          $born .= '-';
        }
        /**
         * sex
         */
        if($i==8){
          $sex = wfPhpfunc::substr($control->get('pid'), $i, 1);

          if(wfPhpfunc::substr($control->get('pid'), $i, 1) % 2 == 0){ 
            $sex = 'Female';
          }else{
            $sex = 'Male';
          }
        }
      }
      /**
       * born
       */
      if($len < 12){
        $born = null;
      }else{
        $born = wfPhpfunc::substr($control->get('pid_original'), 0, 2) .$born;
      }
      /**
       * 
       */
      $control->set('prod', $prod);
      $control->set('coordination_number', $coordination_number);
      $control->set('born', $born);
      $control->set('sex', $sex);
      $control->set('modulus', 10 - ($control->get('prod') % 10));
      if($control->get('modulus')==10){
        $control->set('modulus', 0);
      }
      $control->set('check', wfPhpfunc::substr($control->get('pid'), 9, 1));
      $control->set('ok', false);
      $control->set('message', '?label has an incorrect control digit!');
      if($control->get('modulus')==$control->get('check')){
        $control->set('ok', true);
        $control->set('message', '?label has a correct control digit!');
      }
      return $control;
    }else{
      $control->set('ok', false);
      $control->set('message', '?label must be formatted as ?format!');
      return $control;
    }
  }
}
