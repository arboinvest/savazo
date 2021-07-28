function PassReplace() {
	/// paraméterek
	var ez = this;
	var uid = 0;

	var oldpass = $('#oldpass');
	var newpass = $('#newpass');
	var newpass2 = $('#newpass2');
	var submit = $('#submit');
	var backbtn = $('#backbtn');


	this.init = function(_uid) {
		this.uid = parseInt(_uid);
	};

	this.reset = function() {
		oldpass.val('');
		newpass.val('');
		newpass2.val('');
	};

	submit.on("click", function() {
		oldpass.val(oldpass.val().trim());
		newpass.val(newpass.val().trim());
		newpass2.val(newpass2.val().trim());
		if (oldpass.val() == '' || newpass.val() == '' || newpass2.val() == '') {
			alert('Nincs kitöltve minden adatmező!');
			return;
		}
		if (newpass.val() != newpass2.val()) {
			alert('A két jelszó nem egyezik!');
			return;
		}
		let formData = { cmd: '4', uid: ez.uid, oldpass: oldpass.val(), newpass: newpass.val() };
		$.ajax({
			url: './login/usrmngtcontroller.php'
			, method:"POST"
			, data: formData
			, success: function(result) {
				if (result == '1') {
					alert('Kész.');
					ez.reset();
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

