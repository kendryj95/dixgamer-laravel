
// Verifica si el email existe
function check_email_ajax(page,email){

	 document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";

    $.post(page, {'email':email}, function(data) {
      if (data) {
        document.getElementById("user-result").className = 'fa fa-ban';
      }else{
        document.getElementById("user-result").className = 'fa fa-check';
      }
    	var test = document.getElementById("user-result");
    	var testClass = test.className;

    	switch(testClass){
        case "fa fa-ban": document.getElementById("user-result-div").className = "input-group form-group has-error";
        document.getElementById('submiterInsert').disabled=true; break;
      	case "fa fa-check": document.getElementById("user-result-div").className = "input-group form-group has-success";
      	document.getElementById('submiterInsert').disabled=false; break;
      }

  	});

}


function getPageAjax(path,modal,param=null){
	// Modal
	var modal = $(modal);
	// Limpiamos la seccion

	if (param == null) {
		param = '';
	} else {
		param = '/'+param;
	}
	console.log('path', path);
	modal.find('.modal-body').html('');
	$.ajax({
		url: path+param,
		type: 'GET',
		dataType: 'HTML',
		beforeSend: function(){
			modal.find('.modal-body').html('Cargando');
		},
		success:function(data){
			modal.find('.modal-body').html(data);
		}
	})
	.fail(function() {
		console.log("error");
	});

}


// Verifica que el usuario de mercado libre exista
function check_ml_user_ajax(page,ml_user){

	document.getElementById("ml-user-result").className = "fa fa-spinner fa-pulse fa-fw";

    $.post(page, {'ml_user':ml_user}, function(data) {

      if (data) {
        document.getElementById("ml-user-result").className = 'fa fa-ban';
      }else{
        document.getElementById("ml-user-result").className = 'fa fa-check';
      }
    	var test = document.getElementById("ml-user-result");
    	var testClass = test.className;

    	switch(testClass){
        case "fa fa-ban": document.getElementById("ml-user-result-div").className = "input-group form-group has-error";
        document.getElementById('submiterInsert').disabled=true; break;
    	  case "fa fa-check": document.getElementById("ml-user-result-div").className = "input-group form-group has-success";
    	  document.getElementById('submiterInsert').disabled=false; break;
      }

  	});

}


function check_mail_fake_account_ajax(mail_fake){
	document.getElementById("ml-user-result").className = "fa fa-spinner fa-pulse fa-fw";

  $.post('/account_ctrl_column', {'column':'mail_fake','word':mail_fake}, function(data) {

		if (data) {
			document.getElementById("ml-user-result").className = 'fa fa-ban';
		}else{
			document.getElementById("ml-user-result").className = 'fa fa-check';
		}
		var test = document.getElementById("ml-user-result");
		var testClass = test.className;

		switch(testClass){
	    case "fa fa-ban": document.getElementById("ml-user-result-div").className = "input-group form-group has-error"; break;
			case "fa fa-check": document.getElementById("ml-user-result-div").className = "input-group form-group has-success"; break;
		}

	});
}

function check_account_mail_ajax(mail){
	document.getElementById("user-result").className = "fa fa-spinner fa-pulse fa-fw";

  $.post('/account_ctrl_column', {'column':'mail','word':mail}, function(data) {

			if (data) {
				document.getElementById("user-result").className = 'fa fa-ban';
			}else{
				document.getElementById("user-result").className = 'fa fa-check';
			}
			var test = document.getElementById("user-result");
			var testClass = test.className;

			switch(testClass){
		    case "fa fa-ban": document.getElementById("user-result-div").className = "input-group form-group has-error"; break;
				case "fa fa-check": document.getElementById("user-result-div").className = "input-group form-group has-success"; break;
			}

	});
}

function highlightDuplicates() {
	// loop over all input fields in table
	$('form[name="form_store_codes"]').find('input').each(function() {
		// check if there is another one with the same value
		if ($('form').find('input[value="' + $(this).val() + '"]').size() > 1) {
			// highlight this
			if ($(this).val() != ''){
			$(this).parent().addClass('has-error');
			}
			else { $(this).parent().removeClass('has-error'); }
		} else {
			// otherwise remove
			$(this).parent().removeClass('has-error');
		}
	});
}

function createColumn(selector,type = 'code'){
	// Inicializamos el template


	var str = selector.val();
	if (str != '') {
		var nstr = str.replace(/-/g, '');
		try {
			var arr = [];
			if (type == 'code_g') {
				arr = nstr.split("\n");
			}else{
				arr = nstr.match(/Voucher Code: \w+/g).map(function( m ){
					return m.replace('Voucher Code: ','');
				});
			}
			let html;
			html = '';

			let index = 0;
			for(let word of arr){
				// Creamos el input

				word = format(word, [4, 4, 4, 4, 4, 4, 4, 4, 4], "-");

				let input =`
				<div class="input-group form-group" style="margin:0px;width:80%;">
					<input class="form-control corregircodigo" type="text" name="codes[]" placeholder="${index+1}" value="${word}" style="height: 20px;">
				</div>
				`;

				// Cada multiplo de 10 creara una fila nueva
				if (index%20 == 0){
					if (index != 0) {
						html += '</div>';
					}
					html += '<div class="col-md-4">';
					html += input;
				}else{
					html += input;
				}

				index++;
			}


			html += '</div>';
			$('#container_codes').html(html);
            $('#cant_giftcards').html(index);

		} catch (e) {

		} finally {

		}
	}

}

// $("#code5").val(format(cells[4], [4, 4, 4, 4, 4, 4, 4, 4, 4], "-"));

//  - FUNCION PARA AGREGAR EL DASH " - " cada 4 caracteres -->
// Primero establezco el formato, luego quito los dash que tenga actualmente, para finalmente agregar el dash cada 4 caracteres


function format(input, format, sep) {
 var output = "";
 var idx = 0;

 for (var i = 0; i < format.length && idx < input.length; i++) {
	 output += input.substr(idx, format[i]);
	 if (idx + format[i] < input.length) output += sep;
	 idx += format[i];
 }

 output += input.substr(idx);
 return output;
}






$(document).ready(function() {
  // Pasamos token a las llamadas AJAX
  $.ajaxSetup(
  {
      headers:
      {
          'X-CSRF-Token': $('meta[name="_token"]').attr('content')
      }
  });

	// Calculo el precio en pesos
	$('body').on('keyup', '#multiplicando', function(event) {
		event.preventDefault();

		let multiplicando = $(this).val();
		let multiplicador = $("#multiplicador").val();
		let r = multiplicando*multiplicador;
		$('#resultado').val(r);
	});

    $('body').on('input', '#multiplicador', function(event) {
        event.preventDefault();

        let multiplicador = $(this).val();
        let multiplicando = $("#multiplicando").val();
        let r = multiplicando*multiplicador;
        $('#resultado').val(r);
    });

	// Sacar codigo de text area
	$('body').on('keyup', 'textarea[name=cod_bruto]', function(event) {
		event.preventDefault();
		/* Act on the event */

		createColumn($(this));

	});


	$('body').on('keyup', '#excel_data', function(event) {
		event.preventDefault();
		/* Act on the event */
		var foo = $(this).val().replace(/-/g, "");
		$(this).val(foo);

		createColumn($(this),'code_g');
	});



	$('body').on('keyup', '.corregircodigo', function(event) {
		event.preventDefault();
		/* Act on the event */
		var foo = $(this).val().replace(/-/g, "");
		if (foo.length > 0) {
				 foo = format(foo, [4, 4, 4, 4, 4, 4, 4, 4, 4], "-");
		}

		$(this).val(foo);
	});

	var isFormValid = false;
	$('form[name="form_store_codes"]').submit(function() {
		if (!isFormValid) {
			if($('#multiplicando').val() == ''){
				document.getElementById("div_costo_usd").className = "input-group form-group has-error";
				isFormValid = true;
						return false;
			}
		}
	});

});
