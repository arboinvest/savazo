function UserMngtController() {
	/// paraméterek
	var ez = this;

	var activeDiv = $('#activeDiv');
	var user = $('#user');
	var fullname = $('#fullname');
	var pass = $('#pass');
	var pass2 = $('#pass2');
	var btn = $('#btn');
	var iauser = $('#iauser');
	var iabtn = $('#iabtn');
	var pruser = $('#pruser');
	var prpass = $('#prpass');
	var prpass2 = $('#prpass2');
	var prbtn = $('#prbtn');
	var backbtn = $('#backbtn');


	this.init = function() {
		ez.refresh();
	};


	this.refresh = function() {
		let formData = { cmd: '0' };

		$.ajax({
			url: './login/usrmngtcontroller.php'
			, method:"POST"
			, data: formData
			, success: function(result) {
				result = result.split(',');
				html = '';
				result.forEach( v => {
					html += v + '<br>';
				});
				activeDiv.html(html);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('A hálózati kapcsolat megszűnt!\nA helyes működéshez újra be kell jelentkezni!');
			}
		});

	};


	this.reset = function() {
		user.val('');
		fullname.val('');
		pass.val('');
		pass2.val('');
		iauser.val('');
		pruser.val('');
		prpass.val('');
		prpass2.val('');
	};


	btn.on("click", function() {
		pass.val(pass.val().trim());
		pass2.val(pass2.val().trim());
		user.val(user.val().trim());
		fullname.val(fullname.val().trim());
		if (pass.val() == '' || pass2.val() == '' || user.val() == '' || fullname.val() == '') {
			alert('Nincs kitöltve minden adatmező!');
			return;
		}
		if (pass.val() != pass2.val()) {
			alert('A két jelszó nem egyezik!');
			return;
		}
		let formData = { cmd: '1', name: user.val(), fullname: fullname.val(), pass: pass.val() };
		$.ajax({
			url: './login/usrmngtcontroller.php'
			, method:"POST"
			, data: formData
			, success: function(result) {
				if (result == '1') {
					alert('Kész.');
					ez.reset();
					ez.refresh();
				} else {
					alert(result);
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('A hálózati kapcsolat megszűnt!\nA helyes működéshez újra be kell jelentkezni!');
			}
		});
	});

	iabtn.on("click", function() {
		iauser.val(iauser.val().trim());
		if (iauser.val() == '') {
			alert('Nincs kitöltve minden adatmező!');
			return;
		}
		if (iauser.val() == 'admin') {
			alert('Az admin nem inaktiválható!');
			return;
		}
		let formData = { cmd: '2', name: iauser.val() };
		$.ajax({
			url: './login/usrmngtcontroller.php'
			, method:"POST"
			, data: formData
			, success: function(result) {
				if (result == '1') {
					alert('Kész.');
					ez.reset();
					ez.refresh();
				} else {
					alert(result);
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('A hálózati kapcsolat megszűnt!\nA helyes működéshez újra be kell jelentkezni!');
			}
		});
	});

	prbtn.on("click", function() {
		pruser.val(pruser.val().trim());
		prpass.val(prpass.val().trim());
		prpass2.val(prpass2.val().trim());
		if (pruser.val() == '' || prpass.val() == '' || prpass2.val() == '') {
			alert('Nincs kitöltve minden adatmező!');
			return;
		}
		if (prpass.val() != prpass2.val()) {
			alert('A két jelszó nem egyezik!');
			return;
		}
		let formData = { cmd: '3', name: pruser.val(), pass: prpass.val() };
		$.ajax({
			url: './login/usrmngtcontroller.php'
			, method:"POST"
			, data: formData
			, success: function(result) {
				if (result == '1') {
					alert('Kész.');
					ez.reset();
					ez.refresh();
				} else {
					alert(result);
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('A hálózati kapcsolat megszűnt!\nA helyes működéshez újra be kell jelentkezni!');
			}
		});
	});

	backbtn.on("click", function() {
		window.location.replace('./vezerlo.php');
	});


}

