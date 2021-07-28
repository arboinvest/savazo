function Login() {
	
	var ez = this;
	var serverUrl = null;

	var user = $('#user');
	var pass = $('#pass');
	var btn = $('#btn');
	var checkedValue = 'd1';
	
	this.init = function(_serverUrl) {
		this.serverUrl = _serverUrl;
	}
	

	this.startLogin = function() {
		checkedValue = $("input[name='dd']:checked").val();
		
		$.ajax( {
			// url: ('http://'+ez.serverUrl+'/login/ellenorzes.php')
			url: ('../login/ellenorzes.php')
			, type: "POST"
			, data: { user: user.val(), pass: pass.val() }
			, success: function(result){
				
				if (result.ok) {			
					// window.location.assign('http://'+ez.serverUrl+'/vezerlo.php');
					console.log('checkedValue2: |' + checkedValue + '|');
					
					if (checkedValue == 'd1') {
						window.location.assign('../vezerlo.php');
					} else if (checkedValue == 'd2') {
						window.location.assign('../pid.php');
					}
					
				} else {
					$('#error').show();
				}			
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				console.log("bejelenetkezés hiba");
			}
		
		});
	};
	
	btn.on("click", function() {
		ez.startLogin();
	});
	
}