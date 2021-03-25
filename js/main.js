$.fn.validateAll = function() {
  var valid = true;
	$(this).find("[validate='1']").each(function (){
		var element = $(this);
		var type = element.attr("type");
		if(type == "email"){
			validateEmail(element) ? '' : valid = false;
		}else if(type == "number"){
			validateNumber(element) ? '' : valid = false;
		}else if(type == "password"){
			validatePasswords(element) ? '' : valid = false;
		}else if(type == "radio"){
			validateRadio(element) ? '' : valid = false;
		}else{
			validateOthers(element) ? '' : valid = false;
		}
	});
	return valid;
}

function validateOthers(element) {
	if(element.val()){
		element.removeClass("error");
		return true;
	}else{
		element.addClass("error");
		return false;
	}
}

function validatePasswords(element){
	var other = element.attr("compare");
	if(other){
		other = $("#"+other);
		if(element.val() != other.val()){
			element.addClass("error");
			other.addClass("error");
			return false;
		}else if(!validatePassword(element.val()) || !validatePassword(other.val())){
			element.addClass("error");
			other.addClass("error");
			return false;
		}else{
			element.removeClass("error");
			other.removeClass("error");
			return true;
		}
	}else{
		if(!validatePassword(element.val())) {
			element.addClass("error");
			return false;
		}else{
			element.removeClass("error");
			return true;
		}
	}
}

function validatePassword(value) {
	var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;
	return (regex.test(value) ? true : false);
}

function validateNumber(element) {
	var regex = /^\d+$/;
	if(regex.test(element.val())){
		if(element.attr("min")){
			if(element.attr("min") > element.val()){
				element.addClass("error");
				return false;
			}else{
				element.removeClass("error");
				return true;
			}
		}
		if(element.attr("max")){
			if(element.attr("max") < element.val()){
				element.addClass("error");
				return false;
			}else{
				element.removeClass("error");
				return true;
			}
		}
		element.removeClass("error");
		return true;
	}else{
		element.addClass("error");
		return false;
	}
}

function validateEmail(element) {
	var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(regex.test(element.val())){
		element.removeClass("error");
		return true;
	}else{
		element.addClass("error");
		return false;
	}
}

function validateRadio(element) {
	var d = "input[name='" + element.attr('name') + "']:checked";
	if(!!$(d).val()){
		element.parent().removeClass("error");
		return true;
	}else{
		element.parent().addClass("error");
		return false;
	}
}