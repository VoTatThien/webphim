"use strict";

//Plaeholder handler
$(function() {
	
if(!Modernizr.input.placeholder){             //placeholder for old brousers and IE
 
  $('[placeholder]').focus(function() {
   var input = $(this);
   if (input.val() == input.attr('placeholder')) {
    input.val('');
    input.removeClass('placeholder');
   }
  }).blur(function() {
   var input = $(this);
   if (input.val() == '' || input.val() == input.attr('placeholder')) {
    input.addClass('placeholder');
    input.val(input.attr('placeholder'));
   }
  }).blur();
  $('[placeholder]').parents('form').submit(function() {
   $(this).find('[placeholder]').each(function() {
    var input = $(this);
    if (input.val() == input.attr('placeholder')) {
     input.val('');
    }
   })
  });
 }
  var emailValid = null;
  var checkingEmail = false;
  var emailTimeout;
  var $emailInput = $('#contact-form [type=email]');

  function checkEmailExist(emailVal, callback) {
    if (!emailVal) {
      emailValid = false;
      if (callback) callback(false, 'Email is empty!');
      return;
    }
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (!emailRegex.test(emailVal)) {
      emailValid = false;
      if (callback) callback(false, 'Email không đúng định dạng!');
      return;
    }

    checkingEmail = true;
    $.getJSON('index.php?act=kiemtra_email_ajax&email=' + encodeURIComponent(emailVal), function(res) {
      checkingEmail = false;
      if (res && res.valid) {
        emailValid = true;
        $emailInput.removeClass('invalid_field');
        $emailInput.parent().find('.inv-em').remove();
        if (callback) callback(true);
      } else {
        emailValid = false;
        var msg = (res && res.message) ? res.message : 'Email không tồn tại thực tế trên Internet!';
        createErrTult(msg, $emailInput);
        if (callback) callback(false, msg);
      }
    }).fail(function() {
      checkingEmail = false;
      emailValid = false;
      if (callback) callback(false, 'Lỗi kết nối kiểm tra email!');
    });
  }

  $emailInput.on('blur', function() {
    var emailVal = $(this).val().trim();
    if (emailVal !== '') {
      checkEmailExist(emailVal);
    }
  });

  $emailInput.on('input', function() {
    emailValid = null;
    $emailInput.removeClass('invalid_field');
    $emailInput.parent().find('.inv-em').remove();
    
    clearTimeout(emailTimeout);
    var emailVal = $(this).val().trim();
    if (emailVal !== '') {
      emailTimeout = setTimeout(function() {
        checkEmailExist(emailVal);
      }, 1000);
    }
  });

  $('#contact-form').submit(function(e) {
		e.preventDefault();	
		var error = 0;
		var self = $(this);
		
	    var $name = self.find('[name=user-name]');
	    var $email = self.find('[type=email]');
	    var $message = self.find('[name=user-message]');
		
		var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		
  		if(!emailRegex.test($email.val())) {
			createErrTult('Email không đúng định dạng!', $email);
			error++;	
		}

		if( $name.val().length>1 &&  $name.val()!= $name.attr('placeholder')  ) {
			$name.removeClass('invalid_field');			
		} 
		else {
			createErrTult('Vui lòng nhập tên của bạn!', $name);
			error++;
		}

		if($message.val().length>2 && $message.val()!= $message.attr('placeholder')) {
			$message.removeClass('invalid_field');
		} 
		else {
			createErrTult('Vui lòng nhập nội dung tin nhắn!', $message);
			error++;
		}
		
		if (error!=0) return;

		if (emailValid === true) {
			submitForm();
		} else {
			$email.parent().find('.inv-em').remove();
			checkEmailExist($email.val().trim(), function(isValid, msg) {
				if (isValid) {
					submitForm();
				}
			});
		}

		function submitForm() {
			self.find('[type=submit]').attr('disabled', 'disabled');
			self.children().fadeOut(300,function(){ $(this).remove() });
			
			var thankYou = "Cảm ơn bạn!";
			var successMsg = "Tin nhắn của bạn đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất qua email.";
			$('<p class="success"><span class="success-huge">' + thankYou + '</span> <br> ' + successMsg + '</p>').appendTo(self)
			.hide().delay(300).fadeIn();

			var formInput = self.serialize();
			$.post(self.attr('action'), formInput, function(data){});
		}
  });

$('.login').submit(function(e) {
      
		e.preventDefault();	
		var error = 0;
		var self = $(this);
		
	    var $email = self.find('[type=email]');
	    var $pass = self.find('[type=password]');
		
				
		var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		
  		if(!emailRegex.test($email.val())) {
			createErrTult("Error! Wrong email!", $email)
			error++;	
		}

		if( $pass.val().length>1 &&  $pass.val()!= $pass.attr('placeholder')  ) {
			$pass.removeClass('invalid_field');			
		} 
		else {
			createErrTult('Error! Wrong password!', $pass)
			error++;
		}
		
		
		
		if (error!=0)return;
		self.find('[type=submit]').attr('disabled', 'disabled');

		self.children().fadeOut(300,function(){ $(this).remove() })
		$('<p class="login__title">sign in <br><span class="login-edition">welcome to A.Movie</span></p><p class="success">You have successfully<br> signed in!</p>').appendTo(self)
		.hide().delay(300).fadeIn();


		// var formInput = self.serialize();
		// $.post(self.attr('action'),formInput, function(data){}); // end post
}); // end submit
		
		

function createErrTult(text, $elem){
			$elem.focus();
			$('<p />', {
				'class':'inv-em alert alert-danger',
				'html':'<span class="icon-warning"></span>' + text + ' <a class="close" data-dismiss="alert" href="#" aria-hidden="true"></a>',
			})
			.appendTo($elem.addClass('invalid_field').parent()) 
			.insertAfter($elem)
			.delay(4000).animate({'opacity':0},300, function(){ $(this).slideUp(400,function(){ $(this).remove() }) });
	}
});
