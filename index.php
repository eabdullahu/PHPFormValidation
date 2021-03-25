<!DOCTYPE html>
<?php
	require "controllers/Elements.php";

	$elements = new Elements();
    $elements->add(array("id" => "emri", "validate" => true, "type" => "text", "placeholder" => "Emri", "class" => "input"));
    $elements->add(array("id" => "mbiemri", "validate" => true, "type" => "text", "placeholder" => "Mbiemri", "class" => "input"));
    $elements->add(array("id" => "email", "validate" => true, "type" => "email", "placeholder" => "Email", "class" => "input"));
    $elements->add(array("id" => "qyteti", "validate" => true, "type" => "select", "placeholder" => "Qyteti", "items" => array(1 => "baba", 2 => "baboo"), "selected" => 0, "class" => "input"));
    $elements->add(array("id" => "addresa", "validate" => true, "type" => "text", "placeholder" => "Addresa", "class" => "input"));
    $elements->add(array("id" => "zip", "validate" => true, "type" => "number", "placeholder" => "ZIP", "class" => "input"));
    $elements->add(array("id" => "gjinia", "validate" => true, "type" => "radio", "items" => array(1 => "baba", 2 => "baboo"), "placeholder" => "Sex", "class" => "input"));
    $elements->add(array("id" => "pershkrimi", "validate" => true, "type" => "textarea", "placeholder" => "Enisi", "class" => "input"));

    $elements->add(array("id" => "mosha", "validate" => true, "type" => "number", "placeholder" => "Mosha", "class" => "input", "min" => 5, "max" => 10));

    $elements->add(array("id" => "password", "validate" => true, "type" => "password", "placeholder" => "Fjalekalimi", "class" => "input", "compare" => "password2"));
    $elements->add(array("id" => "password2", "validate" => true, "type" => "password", "placeholder" => "Konfirmo fjalekalimin", "class" => "input", "compare" => "password"));

    $elements->add(array("id" => "ditelindja", "validate" => true, "type" => "date", "placeholder" => "Ditelindja", "class" => "input", "min" => "2020-10-20", "max" => "2020-10-25"));
    
    if(isset($_POST['submit'])){
        if(!$elements->validateAll()){
            $elements->fillData($_POST);
        }
    }
?>
<html>
    <head>
        <title>Test php form</title>
		<link rel="stylesheet" href="css/style.css" />
    </head>
    <body>
        <div class="container">
            <div id="lockmodal"></div>
            <div id="loader"></div>
            <h2 class="header">Contact form</h2>
            <form method="post" action="" id="submitForm">
                <div class="inputContainer g50">
                    <? $elements->render("emri"); ?>
                    <? $elements->render("mbiemri"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("email"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("qyteti"); ?>
                </div>

                <div class="inputContainer g50">
                    <? $elements->render("addresa"); ?>
                    <? $elements->render("zip"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("gjinia"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("pershkrimi"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("mosha"); ?>
                </div>

                <div class="inputContainer g50">
                    <? $elements->render("password"); ?>
                    <? $elements->render("password2"); ?>
                </div>

                <div class="inputContainer">
                    <? $elements->render("ditelindja"); ?>
                </div>

                <div class="inputContainer">
                    <input type="submit" name="submit" class="input" value="SUBMIT" />
                </div>
            </form>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="js/main.js"></script>

        <script>
        	$("#submitForm").submit(function(event){
		        event.preventDefault();

                $("#lockmodal").css("display", "block");
                $("#loader").css("display", "block");

                setTimeout(function() {
                    $("#lockmodal").css("display", "none");
                    $("#loader").css("display", "none");

                    console.log($(this));
                    if($("#submitForm").validateAll())
                        $("#submitForm").unbind("submit").submit();
                }, 3000);
        	});
        </script>
    </body>
</html>