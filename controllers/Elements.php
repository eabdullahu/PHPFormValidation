<?php
	class Elements {
        private $fields;
        private $has_errors;
        function __construct() {
            $this->fields = array();
            $this->has_errors = false;
        }

		// add an element inside $fields array.
		// if array key already exists it exists the page. Key refers to $data['id'] which it is ID AND NAME of that INPUT
        function add($data){
            if(array_key_exists($data['id'], $this->fields)){
                exit("Field with ID: '".$data['id']."' already exists.");
            }
            if(!isset($data['validate'])){
                $data['validate'] = false;
            }
            if(!isset($data['inline'])){
                $data['inline'] = null;
            }
            if(!isset($data['value'])){
                $data['value'] = null;
            }
            if(!isset($data['readonly'])){
                $data['readonly'] = false;
            }

            $this->fields[$data['id']] = $data;
        }

        // it validates all input fields that have $data['validate'] set to 'true' if not it ignores the input.
        // if an element has any error it sets its border to red.
        // date is validated if it is an actual date and can have min and max values.
        // also passwords are validated and checks if it has an uppercase and number. 
        // you can compare to passwords if they are similar e.x to register form. by adding 
        // $data['compare'] = id of second input and $data['compare'] = id of first input.
        function validateAll(){
            foreach($this->fields as $field){
                $val = $_POST[$field['id']];
                if($field['validate']){
                    switch($field['type']){
                        case "email":
                            if(!filter_var($val, FILTER_VALIDATE_EMAIL)){
                                $this->fields[$field['id']]['class'] .= " error";
                                $this->has_errors = true;
                            }
                        break;
                        case "number":
                            if(!is_numeric($val)){
                                $this->fields[$field['id']]['class'] .= " error";
                                $this->has_errors = true;
                            }else{
                                if(isset($this->fields[$field['id']]['min'])){
                                    if($this->fields[$field['id']]['min'] > $val){
                                        $this->fields[$field['id']]['class'] .= " error";
                                        $this->has_errors = true;
                                    }
                                }
                                if(isset($this->fields[$field['id']]['max'])){
                                    if($this->fields[$field['id']]['max'] < $val){
                                        $this->fields[$field['id']]['class'] .= " error";
                                        $this->has_errors = true;
                                    }
                                }
                            }
                        break;
                        case "password":
                            if($this->fields[$field['id']]['compare']){
                                $compareWith = $this->fields[$field['id']]['compare'];
                                if($val != $_POST[$compareWith]){
                                    $this->fields[$field['id']]['class'] .= " error";
                                    $this->fields[$compareWith]['class'] .= " error";
                                    $this->has_errors = true;
                                }else if(!$this->validatePassword($val) || !$this->validatePassword($_POST[$compareWith])){
                                    $this->fields[$field['id']]['class'] .= " error";
                                    $this->fields[$compareWith]['class'] .= " error";
                                    $this->has_errors = true;
                                }
                            }else{
                                if(!$this->validatePassword($val)){
                                    $this->fields[$field['id']]['class'] .= " error";
                                    $this->has_errors = true;
                                }
                            }
                        break;
                        case "date":
                            if(!$this->validateDate($val)){
                                $this->fields[$field['id']]['class'] .= " error";
                                $this->has_errors = true;
                            }else{
                                if(isset($this->fields[$field['id']]['min'])){
                                    $min = $this->fields[$field['id']]['min'];
                                    if($this->compareDates($min, $val)){
                                        $this->fields[$field['id']]['class'] .= " error";
                                        $this->has_errors = true;
                                    }
                                }
                                if(isset($this->fields[$field['id']]['max'])){
                                    $max = $this->fields[$field['id']]['max'];
                                    if($this->compareDates($val, $max)){
                                        $this->fields[$field['id']]['class'] .= " error";
                                        $this->has_errors = true;
                                    }
                                }
                            }
                        break;
                        case "radio":
                            if(!$val){
                                $this->fields[$field['id']]['error'] .= " error";
                            }
                        break;
                        default:
                            if(!$val){
                                $this->fields[$field['id']]['class'] .= " error";
                            }
                    }
                }
            }
            return ($this->has_errors ? false : true);
        }

        function validatePassword($passwdValue){
            $uppercase = preg_match('@[A-Z]@', $passwdValue);
            $lowercase = preg_match('@[a-z]@', $passwdValue);
            $number    = preg_match('@[0-9]@', $passwdValue);
            return $uppercase && $lowercase && $number && strlen($passwdValue) >= 8;
        }

        function validateDate($date){
            $d = DateTime::createFromFormat('Y-m-d', $date);
            return $d && $d->format('Y-m-d') == $date;
        }
        function compareDates($firstDate, $secondDate){
            $firstDate = strtotime($firstDate);
            $secondDate = strtotime($secondDate);
            if ($firstDate > $secondDate)
                return true;

            return false;
        }

        // renders an element inside HTML by calling this function 
        // excepts input id
        // by default detects what element type and calls other functions to crate that input. 
        function render($id){
            $data = $this->fields[$id];
            switch($data['type']){
                case "select": $this->createSelect($data); break;
                case "radio": $this->createRadio($data); break;
                case "textarea": $this->createTextArea($data); break;
                case "number": case "date": $this->createDateAndNumber($data); break;
                case "password": $this->createPassword($data); break; 
                default: $this->createElement($data);
            }
        }

        function fillData($data){
            foreach($this->fields as $field){
                if($field['type'] == "select"){
                    $this->fields[$field['id']]['selected'] = $data[$field['id']];
                }else if($field['type'] == "radio"){
                    $this->fields[$field['id']]['checked'] = $data[$field['id']];
                }else{
                    $this->fields[$field['id']]['value'] = $data[$field['id']];
                }
            }
        }

        function createElement($data){
            $txt = "<input type='".$data['type']."' validate='".$data['validate']."' value='".$data['value']."' id='".$data['id']."' name='".$data['id']."' class='".$data['class']."' placeholder='".$data['placeholder']."' readonly='".$data['readonly']."' ".$data['inline']." />";
            echo $txt;
        }

        function createDateAndNumber($data){
            $readonly = (isset($data['readonly']) ? "readonly" : "");
            $min = (isset($data['min']) ? "min=".$data['min'] : "");
            $max = (isset($data['max']) ? "max=".$data['max'] : "");

            $txt = "<input type='".$data['type']."' validate='".$data['validate']."' value='".$data['value']."' id='".$data['id']."' name='".$data['id']."' class='".$data['class']."' placeholder='".$data['placeholder']."' readonly='".$data['readonly']."' ".$min." ".$max." ".$data['inline']." />";
            echo $txt;
        }

        function createPassword($data){
            $compare = (isset($data['compare']) ? "compare='".$data['compare']."'" : "");
            $txt = "<input type='password' validate='".$data['validate']."' id='".$data['id']."' name='".$data['id']."' class='".$data['class']."' placeholder='".$data['placeholder']."' ".$compare." ".$data['inline']." />";
            echo $txt;
        }

        function createSelect($data){
            $txt = "<select id='".$data['id']."' validate='".$data['validate']."' name='".$data['id']."' class='".$data['class']."' ".$data['inline'].">";
            $txt .= "<option value=''>[ Choose ]</option>";
            if($data['items']){
                foreach($data['items'] as $key => $val){ 
                    $selected = "";
                    if($data['selected'] && $data['selected'] == $key){
                        $selected = "selected";
                    }
                    $txt .= "<option value='".$key."' ".$selected.">".$val."</option>";
                }
            }
            $txt .= "</select>";
            echo $txt;
        }

        function createRadio($data){
            if($data['items']){
                $txt = "<div class='radio".(isset($data['error']) ? $data['error'] : '')."'>";
                $txt .= "<span>".$data['placeholder'].": </span>";
                foreach($data['items'] as $key => $val){ 
                    $checked = "";
                    if(isset($data['checked']))
                        if($data['checked'] == $key)
                            $checked = " checked ";

                    $new_id = $data['id']."_".$val;
                    $txt .= "<input type='radio' id='".$new_id."' name='".$data['id']."' validate='".$data['validate']."' class='".$data['class']."' value='".$key."' ".$checked." ".$data['inline']." />";
                    $txt .= "<label for='".$new_id."'>$val</label>";
                }
                $txt .= "</div>";
                echo $txt;
            }
        }

        function createTextArea($data){
            $txt = "<textarea id='".$data['id']."' class='".$data['class']."' validate='".$data['validate']."' placeholder='".$data['placeholder']."' readonly='".$data['readonly']."' ".$data['inline'].">".$data['value']."</textarea>";
            echo $txt;
        }

        function pa($data, $color="white"){
            echo "<pre style='background: ".$color.";'>";
            print_r($data);
            echo "</pre>";
        }
    }
?>