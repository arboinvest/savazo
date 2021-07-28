function Vezerlok() {
	/// paraméterek
	var ez = this;
	var serverUrl = null;
	var nyofURL = null;
	var nevek = null;
	var uid = 0;
	/// változók
	var checkSum1 = "";
	var checkSum2 = "";
	var checkSum3 = "";
	var checkSumGeneral = "";
	var checkSumStatus = "";
	var frissites = false;
	var vezerles = false;
	var statusFrissites = false;
	// időzítők
	var refreshInterval = null;
	var refreshInterval2 = null;
	var refreshSessionInterval = null;
	var statusDataObj = null;
	var clock = null;
	var keziUzemAllapot = false;
	var keziUzemElozoAllapot = true;
	var lastData = null;

	var p1Aktiv = true;
	var p2Aktiv = true;
	var p3Aktiv = true;
	var p4Aktiv = true;
	
	var nyfalarm = false;
	var waiting = false;

	/// konstansok
	const ZOLD = 1;
	const SARGA = 2;
//	const PIROS = 3;
//	const SZURKE = 0;

	const BG_DANGER = "bg-danger";
	const BG_WARNING = "bg-warning";
	const BG_SUCESS = "bg-success";

	const BTN_DANGER = "btn-danger";
	const BTN_WARNING = "btn-warning";
	const BTN_SUCESS = "btn-success";
	const BTN_SECONDARY = "btn-secondary";
	const INACTIVE_LINK = "inactive-link";

	const INAKT_VEZ_SZIN = "#d0d0d0";


	/// html
	var p1 = $("#p1");
	var p2 = $("#p2");
	var p3 = $("#p3");
	var v1 = $("#v1");
	var v2 = $("#v2");
	var v3 = $("#v3");
	var v4 = $("#v4");
	var v5 = $("#v5");
	var v6 = $("#v6");
	var v7 = $("#v7");
	var v8 = $("#v8");
	var ksz = $("#ksz");
	var p1m = $("#p1m");
	var p2m = $("#p2m");
	var p3m = $("#p3m");
	var gstm = $("#gst");
	var time = $("#time");
	var ldata = $("#ldata");
	var p1d = $("#p1d");
	var p2d = $("#p2d");
	var p3d = $("#p3d");
//	var p4d = $("#p4d");
	var p1rs = $("#p1rs");
	var p2rs = $("#p2rs");
	var p4reset = $("#p4reset");
	var usrmngmt = $("#usrmngmt");
	var passreplace = $("#passreplace");
	var username = $("#username");
	var gh1 = document.getElementById('gh1');
	var gh2 = document.getElementById('gh2');
	var $div2blink = $("#status");
	var logout = $("#logout");
	var nyfe1 = $("#nyfe1");
	//var nyfe2 = $("#nyfe2");
	var nyfe3 = $("#nyfe3");
	var nyfe4 = $("#nyfe4");
	var nyfetm = $("#nyfetm");
	var nyfr = $("#nyfr");
	var nyfi = $("#nyfi");
	var nyflog = $("#nyflog");
	var pid = $("#pid");
	var waitnetw = $("#waitnetw");
	
	// vezérlők
	var trVez = $("#trVez");
	var aVez2 = null;
	var aVez3 = null;
	var aVezKeziUzem = null;
	var nyofUtemezes = 0;

	var p1restart = $("#p1restart");
	var p2restart = $("#p2restart");
	var p1ping = $("#p1ping");
	var p2ping = $("#p2ping");
	var cmdout = $("#cmdout");
	
	this.init = function(_serverUrl, _nyofURL, _nevek, _uid) {
		this.serverUrl = _serverUrl;
		this.nyofURL = _nyofURL;
		this.p4Aktiv = _nyofURL != '';

		this.nevek = new Object();
		let nevSorok = _nevek.split(',');
		for (i = 0; i < nevSorok.length; ++i) {
			let kulcsErtek = nevSorok[i].split(':');
			this.nevek[ parseInt(kulcsErtek[0])] = kulcsErtek[1];

			console.log('' + kulcsErtek[0] + this.nevek[parseInt(kulcsErtek[0])]);

		}
		this.nevek[0] = 'Rendszer';
		this.uid = _uid;
		username.text(this.nevek[this.uid]);

		if (trVez && trVez != null) {
		    var vezHeight = trVez.height();
			var tabVez1 = $("#tabVez1");
			var tabVez2 = $("#tabVez2");
		    tabVez1.css("height", vezHeight);
		    tabVez2.css("height", vezHeight);
			var vezPadding = ($("#tdVez").height() / 2) - 12;
			for (var i = 0; i < 2; ++i) for (var j = 0; j < 2; ++j) {
				$("#aVez0" + i + "" + j).css("padding-top", vezPadding);
				$("#aVez1" + i + "" + j).css("padding-top", vezPadding);
			}
			vezPadding = (vezHeight / 2);
			aVez2 = $("#aVez2");
			aVez3 = $("#aVez3");
			aVezKeziUzem = $("#aVezKeziUzem");

			aVez2.css("height", vezHeight);
			aVez3.css("height", vezHeight);
			aVez2.css("padding-top", vezPadding-12);
			aVez3.css("padding-top", vezPadding-24);

			vezHeight = $("#v1v3").height();
			vezPadding = (vezHeight / 2);
			aVezKeziUzem.css("height", vezHeight+1);
			aVezKeziUzem.css("padding-top", vezPadding-24);

			v1.css("height", vezHeight+1);
			v1.css("padding-top", vezPadding-24);
			v3.css("height", vezHeight+1);
			v3.css("padding-top", vezPadding-24);
			v5.css("height", vezHeight+1);
			v5.css("padding-top", vezPadding-24);
			v7.css("height", vezHeight+1);
			v7.css("padding-top", vezPadding-24);

			vezHeight = $("#v2v4").height();
			vezPadding = (vezHeight / 2);

			v2.css("height", vezHeight+1);
			v2.css("padding-top", vezPadding-24);
			v4.css("height", vezHeight+1);
			v4.css("padding-top", vezPadding-24);
			v6.css("height", vezHeight+1);
			v6.css("padding-top", vezPadding-24);
			v8.css("height", vezHeight+1);
			v8.css("padding-top", vezPadding-24);

			this.addVezerloEsemenyek();
		}
		
		ksz.onmouseover = function() { 
			v4.removeClass("hover");
			v4.style.pointerEvents = "auto";
		};
		
		$div2blink.toggleClass("bg-success");
		time.text(new Date().toLocaleString());
		
		ez.refreshJsonData(false);
		nyfr.css('background-color','#5f875a');
		ez.nyofTablaFrissites();
		refreshInterval = setInterval(function() {

			$div2blink.toggleClass("bg-success");
			time.text(new Date().toLocaleString());

			ez.refreshJsonData(true);
			++ nyofUtemezes;
			if (nyofUtemezes > 4) {
				nyofUtemezes = 0;
				ez.nyofTablaFrissites();
			}

		},1000);
		
		if (trVez) {
			ez.refreshStatus();
			refreshInterval2 = setInterval(function(){
				ez.refreshStatus(false);
			},1300);
		}

		refreshSessionInterval = setInterval(function() {
//			console.log("refreshSessions()");
			ez.refreshSessions();
		}, 1200000 );

		
	};


	this.refreshSessions = function() {
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo.php'
			url: '../vezerlo.php'
			, success: function(result) {
				// drop this!
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('A hálózati kapcsolat megszűnt!\nA helyes működéshez újra be kell jelentkezni!');
			}
		});
	};


	this.refreshJsonData = function(reDraw) {
		if (frissites) return false;
		
		$.ajax({
			url: '../data.php'
			, success: function(result){
				if (! frissites) {
//					console.log(JSON.stringify(result));
					frissites = true;

					clock = parseInt( (new Date()).getTime() / 1000);

					ez.refreshTable(result);
					ez.refreshChart(result, reDraw);
					
					if (checkSum1 != result.p1.checksum) {
						checkSum1 = result.p1.checksum;
					}
					if (checkSum2 != result.p2.checksum) {
						checkSum2 = result.p2.checksum;
					}
					
					frissites = false;
					
					if (waiting) {
						waiting = false;
						waitnetw.hide();
					}
					
					
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				frissites = false;
				// console.log('data.php hiba');
				if (! waiting) {
					waiting = true;				
					waitnetw.show();
				}				
			}
		});
		
		return true;
	};
	
	this.refreshTable = function(data) {
		lastData = data;
		
		p1.removeClass(BG_DANGER);
		p1.removeClass(BG_WARNING);
		p1.removeClass(BG_SUCESS);
		p2.removeClass(BG_DANGER);
		p2.removeClass(BG_WARNING);
		p2.removeClass(BG_SUCESS);
		p3.removeClass(BG_DANGER);
		p3.removeClass(BG_WARNING);
		p3.removeClass(BG_SUCESS);
		v1.removeClass(BTN_DANGER);
		v1.removeClass(BTN_WARNING);
		v1.removeClass(BTN_SUCESS);
		
		v2.removeClass(BTN_DANGER);
		v2.removeClass(BTN_WARNING);
		v2.removeClass(BTN_SUCESS);
		
		v3.removeClass(BTN_DANGER);
		v3.removeClass(BTN_WARNING);
		v3.removeClass(BTN_SUCESS);
		
		v4.removeClass(BTN_DANGER);
		v4.removeClass(BTN_WARNING);
		v4.removeClass(BTN_SUCESS);
		
		v5.removeClass(BTN_DANGER);
		v5.removeClass(BTN_WARNING);
		v5.removeClass(BTN_SUCESS);
		
		v6.removeClass(BTN_DANGER);
		v6.removeClass(BTN_WARNING);
		v6.removeClass(BTN_SUCESS);
		
		v7.removeClass(BTN_DANGER);
		v7.removeClass(BTN_WARNING);
		v7.removeClass(BTN_SUCESS);
		
		v8.removeClass(BTN_DANGER);
		v8.removeClass(BTN_WARNING);
		v8.removeClass(BTN_SUCESS);

		p1.addClass(data.p1.class);
		p2.addClass(data.p2.class);
		p3.addClass(data.p3.class);
		v1.addClass(data.general.controllers.v1);
		v2.addClass(data.general.controllers.v2);
		v3.addClass(data.general.controllers.v3);
		v4.addClass(data.general.controllers.v4);
		v5.addClass(data.general.controllers.v5);
		v6.addClass(data.general.controllers.v6);
		v7.addClass(data.general.controllers.v7);
		v8.addClass(data.general.controllers.v8);

		// todo: nincs bekötve
		//ksz.removeClass("bg-danger", "bg-warning", "bg-success");
		//ksz.addClass(data.general.controllers.ksz);
		
		if (checkSum1 != data.p1.checksum) {
			p1Aktiv = data.p1.class.substr(3,1) == 's';
			
			var p1mnd = "";
			for (var i = 0; i < data.p1.measurements.length; i++) { 
				if (p1Aktiv) {
					switch (data.p1.measurements[i].port) {
						case 'G25' :
						case 'G27' :
							if (data.p1.measurements[i].value.charAt(0) == '0') {
								p1mnd += "<tr style='background-color: " + INAKT_VEZ_SZIN + "'>";
							} else {
								p1mnd += "<tr>";
							}
							break;
						default:
							p1mnd += "<tr>";
					}
				} else {
					p1mnd += "<tr style='background-color: " + INAKT_VEZ_SZIN + "'>";
				}
				p1mnd += "<td class='text-center'>"+data.p1.measurements[i].virtual_name+"</td>";
				switch (data.p1.measurements[i].unit) {
					case 1:
						p1mnd += "<td class='text-center'>"+ (parseInt(data.p1.measurements[i].value) == 1 ? 'H' : 'L') +"</td>";
						break;
					case 2:
					case 3:
					case 4:
					case 5:
						p1mnd += "<td class='text-center'>"+ parseFloat(data.p1.measurements[i].value).toFixed(1) +"</td>";
						break;
					case 6:
						p1mnd += "<td class='text-center'>"+ (parseInt(data.p1.measurements[i].value) == 1 ? 'Ki' : 'Be') +"</td>";
						break;
				}
				/*if (data.p1.measurements[i].virtual_name.substr(0, 1) == 'V') {
					p1mnd += "<td class='text-center'>"+ (parseInt(data.p1.measurements[i].value) == 1 ? 'H' : 'L') +"</td>";
				} else {
					p1mnd += "<td class='text-center'>"+ parseFloat(data.p1.measurements[i].value).toFixed(1) +"</td>";
				}*/
				p1mnd += "<td class='text-right' id='p1MesTime" + i + "'></td>";
				p1mnd += "</tr>";
			}
			p1mnd += "<tr style='height:5px'>";
			p1mnd += "<td class='text-center' colspan='3'></td>";
			p1mnd += "</tr>";
			p1m.html( p1mnd);
			
			var p1dc = "";
			p1dc += `
				<tr>
					<td class='text-left' style='width:135px'><span class='h4'>P1</span></td>
					<td class='text-right' style='width:150px'><span class='h4' id='p1LastLogin'></span></td>
				</tr>
				<tr>
					<td class='text-left'><span class='h6'>`+ (data.p1.instructions[0].instruction.charAt(0) != 'v' || (data.p1.instructions[0].state != 2 && data.p1.instructions[0].state != 9) ? data.p1.instructions[0].instruction : '&nbsp;') +`</span></td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
			`;
			for (var i = 0; i < data.p1.instructions.length; i++) { 
				p1dc += "<tr>";
				p1dc += "<td class='text-left'>"+data.p1.instructions[i].instruction+"</td>";
				p1dc += "<td class='text-right'>"+data.p1.instructions[i].date+"</td>";
				p1dc += "</tr>";
			}
			p1dc += `
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>`+data.p1.last_message+`</td>
					<td class='text-right' id='p1LastMessage'></td>
				</tr>
			`;
			
			p1d.html( p1dc);
			
			p1rs.removeClass(BG_WARNING);
			p1rs.removeClass(BG_SUCESS);

			switch (parseInt(data.p1.reset)) {
				case 0:
					p1rs.addClass(BG_SUCESS);
					break;
				case 1:
					p1rs.addClass(BG_WARNING);
					break;
			}
			

		}
		
		$('#p1LastMessage').text((parseInt(clock) - parseInt(data.p1.last_message_diff)) +' mp');
		$('#p1LastLogin').text( parseFloat( (parseFloat(clock) - parseFloat(data.p1.last_login)) / 3600).toFixed(1) +' óra');

		var p1mt = 0;
		for (var i = 0; i < data.p1.measurements.length; ++i) {
			p1mt = (parseInt(clock) - parseInt(data.p1.measurements[i].date) + 1);
			if (p1mt < 0) {
				p1mt = 0;
			} else if (p1mt > 99999) {
				p1mt = p1mt % 100000;
			}
			$("#p1MesTime" + i).text(p1mt /*+'mp'*/);
		}
		
		if (checkSum2 != data.p2.checksum) {
			p2Aktiv = data.p2.class.substr(3,1) == 's';
			var p2mnd = "";
			for (var i = 0; i < data.p2.measurements.length; i++) { 
				if (p2Aktiv) {

					switch (data.p2.measurements[i].port) {
						case 'G25' :
						case 'G27' :
							if (data.p2.measurements[i].value.charAt(0) == '0') {
								p2mnd += "<tr style='background-color: " + INAKT_VEZ_SZIN + "'>";
							} else {
								p2mnd += "<tr>";
							}
							break;
						default:
							p2mnd += "<tr>";
					}

				} else {
					p2mnd += "<tr style='background-color: " + INAKT_VEZ_SZIN + "'>";
				}
				p2mnd += "<td class='text-center'>"+data.p2.measurements[i].virtual_name+"</td>";

				switch (data.p2.measurements[i].unit) {
					case 1:
						p2mnd += "<td class='text-center'>"+ (parseInt(data.p2.measurements[i].value) == 1 ? 'H' : 'L') +"</td>";
						break;
					case 2:
					case 3:
					case 4:
					case 5:
						p2mnd += "<td class='text-center'>"+ parseFloat(data.p2.measurements[i].value).toFixed(1) +"</td>";
						break;
					case 6:
						p2mnd += "<td class='text-center'>"+ (parseInt(data.p2.measurements[i].value) == 1 ? 'Ki' : 'Be') +"</td>";
						break;
				}

				/*if (data.p2.measurements[i].virtual_name.substr(0, 1) == 'V') {
					p2mnd += "<td class='text-center'>"+ (parseInt(data.p2.measurements[i].value) == 1 ? 'H' : 'L') +"</td>";
				} else {
					p2mnd += "<td class='text-center'>"+ parseFloat(data.p2.measurements[i].value).toFixed(1) +"</td>";
				}*/
				p2mnd += "<td class='text-right' id='p2MesTime" + i + "'></td>";
				p2mnd += "</tr>";
			}
			p2mnd += "<tr style='height:5px'>";
			p2mnd += "<td class='text-center' colspan='3'></td>";
			p2mnd += "</tr>";
			p2m.html( p2mnd);
			
			var p2dc = "";
			p2dc += `
				<tr>
					<td class='text-left' style='width:135px'><span class='h4'>P2</span></td>
					<td class='text-right' style='width:150px'><span class='h4' id='p2LastLogin'></span></td>
				</tr>
				<tr>
					<td class='text-left'><span class='h6'>`+(data.p2.instructions[0].instruction.charAt(0) != 'v' || (data.p2.instructions[0].state != 2 && data.p2.instructions[0].state != 9) ? data.p2.instructions[0].instruction : '&nbsp;' )+`</span></td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
			`;
			for (var i = 0; i < data.p2.instructions.length; i++) { 
				p2dc += "<tr>";
				p2dc += "<td class='text-left'>"+data.p2.instructions[i].instruction+"</td>";
				p2dc += "<td class='text-right'>"+data.p2.instructions[i].date+"</td>";
				p2dc += "</tr>";
			}
			p2dc += `
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>`+data.p2.last_message+`</td>
					<td class='text-right' id='p2LastMessage'></td>
				</tr>
			`;
			p2d.html( p2dc);
			
			
			p2rs.removeClass(BG_WARNING);
			p2rs.removeClass(BG_SUCESS);
			switch (parseInt(data.p2.reset)) {
				case 0:
					p2rs.addClass(BG_SUCESS);
					break;
				case 1:
					p2rs.addClass(BG_WARNING);
					break;
			}


		}

		$('#p2LastMessage').text((parseInt(clock) - parseInt(data.p2.last_message_diff)) +' mp');
		$('#p2LastLogin').text( parseFloat( (parseFloat(clock) - parseFloat(data.p2.last_login)) / 3600.0).toFixed(1) +' óra');
		var p2mt = 0;
		for (var i = 0; i < data.p2.measurements.length; ++i) {
			p2mt = (parseInt(clock) - parseInt(data.p2.measurements[i].date) + 1);
			if (p2mt < 0) {
				p2mt = 0;
			} else if (p2mt > 99999) {
				p2mt = p2mt % 100000;
			}
			$("#p2MesTime" + i).text( p2mt /*+'mp'*/);
		}

		if (checkSum3 != data.p3.checksum) {
			p3Aktiv = data.p3.class.substr(3,1) == 's';
			checkSum3 = data.p3.checksum;
			
			var p3mnd = "";
			/*var betu = null;*/
			for (var i = 0; i < data.p3.measurements.length; i++) { 

				if (p3Aktiv) {
					switch (data.p3.measurements[i].port) {
						case 'G5':
						case 'G12':
						case 'G6':
						case 'G17':
						case 'G16':
							if (data.p3.measurements[i].value == 0) {
								p3mnd += "<tr style='background-color: #ce342c'>";
							} else {
								p3mnd += "<tr style='background-color: #5f875a'>";
							}
							break;
						default:
							p3mnd += "<tr>";
					}
				} else {
					p3mnd += "<tr style='background-color: " + INAKT_VEZ_SZIN + "'>";
				}

				p3mnd += "<td class='text-center'>"+data.p3.measurements[i].virtual_name+"</td>";

				switch (data.p3.measurements[i].unit) {
					case 1:
						p3mnd += "<td class='text-center'>"+ ( parseInt(data.p3.measurements[i].value) == 1 ? 'H' : 'L') + "</td>";
						break;
					case 2:
					case 3:
					case 4:
					case 5:
						p3mnd += "<td class='text-center'>"+ parseFloat(data.p3.measurements[i].value).toFixed(1) +"</td>";
						break;
					case 6:
						p3mnd += "<td class='text-center'>"+ ( parseInt(data.p3.measurements[i].value) == 1 ? 'Ki' : 'Be') + "</td>";
						break;
				}

				/*betu = data.p3.measurements[i].virtual_name.substr(0, 1);
				if (betu == 'K' || betu == 'Á' || data.p3.measurements[i].virtual_name.slice(-5) == 'reset') {
					p3mnd += "<td class='text-center'>"+ ( parseInt(data.p3.measurements[i].value) == 1 ? 'H' : 'L') + "</td>";
				} else if (betu == 'S') {
					p3mnd += "<td class='text-center'>"+ ( parseInt(data.p3.measurements[i].value) == 1 ? 'Ki' : 'Be') + "</td>";
				}  else {
					p3mnd += "<td class='text-center'>"+ parseFloat(data.p3.measurements[i].value).toFixed(1) +"</td>";
				}*/
				p3mnd += "<td class='text-right' id='p3MesTime" + i + "'></td>";
				p3mnd += "</tr>";
			}
			p3mnd += "<tr style='height:5px'>";
			p3mnd += "<td class='text-center' colspan='3'></td>";
			p3mnd += "</tr>";
			p3m.html( p3mnd);
			
			var p3dc = "";
			p3dc += `
				<tr>
					<td class='text-left' style='width:135px'><span class='h4'>P3</span></td>
					<td class='text-right' style='width:150px'><span class='h4' id='p3LastLogin'></span></td>
				</tr>
				<tr>
					<td class='text-left'><span class='h6'>`+data.p3.instructions[0].instruction+`</span></td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
			`;
			for (var i = 0; i < data.p3.instructions.length; i++) { 
				p3dc += "<tr>";
				p3dc += "<td class='text-left'>"+data.p3.instructions[i].instruction+"</td>";
				p3dc += "<td class='text-right'>"+data.p3.instructions[i].date+"</td>";
				p3dc += "</tr>";
			}
			p3dc += `
				<tr>
					<td class='text-left'>&nbsp;</td>
					<td class='text-right'></td>
				</tr>
				<tr>
					<td class='text-left'>`+data.p3.last_message+`</td>
					<td class='text-right' id='p3LastMessage'></td>
				</tr>
			`;
			p3d.html( p3dc);
			
		}

		$('#p3LastMessage').text((parseInt(clock) - parseInt(data.p3.last_message_diff)) +' mp');
		$('#p3LastLogin').text( parseFloat( (parseFloat(clock) - parseFloat(data.p3.last_login)) / 3600.0).toFixed(1) +' óra');
		var p3mt = 0;
		for (var i = 0; i < data.p3.measurements.length; ++i) {
			p3mt = (parseInt(clock) - parseInt(data.p3.measurements[i].date) + 1);
			if (p3mt < 0) {
				p3mt = 0;
			} else if (p3mt > 99999) {
				p3mt = p3mt % 100000;
			}
			$("#p3MesTime" + i).text( p3mt /*+'mp'*/);
		}


//		console.log("checkSumGeneral: "+checkSumGeneral);
//		console.log("data.general.checksum: "+data.general.checksum);
		if (checkSumGeneral != data.general.checksum) {
			checkSumGeneral = data.general.checksum;
			
			var gmnd = "";
			for (var i = 0; i < data.general.statuses.length; i++) { 
				gmnd += "<tr class='"+data.general.statuses[i].class+"'>";
				gmnd += "<td class='text-center'>"+data.general.statuses[i].task+"</td>";
				gmnd += "<td class='text-center'>"+data.general.statuses[i].wdate+"</td>";
				gmnd += "<td class='text-center'>"+ (! (''+data.general.statuses[i].sdate).startsWith('1') ? data.general.statuses[i].sdate : '---') +"</td>";
				gmnd += "<td class='text-center'>"+ (! (''+data.general.statuses[i].rdate).startsWith('1') ? data.general.statuses[i].rdate : '---') +"</td>";
				gmnd += "<td class='text-center'>"+ (data.general.statuses[i].result != null ? parseFloat(data.general.statuses[i].result).toFixed(1) : '-----') + "</td>";
				gmnd += "<td class='text-center'>"+data.general.statuses[i].status+"</td>";
				gmnd += "<td class='text-center'>"+ this.nevek[ data.general.statuses[i].userid ]+"</td>";
				gmnd += "</tr>";
			}
			gstm.html( gmnd);
			
			var ldatac = "";
			for (var i = 0; i < data.general.logs.length; i++) { 
				ldatac += "<tr>";
				ldatac += "<td class='text-center'>"+data.general.logs[i].details+"</td>";
				ldatac += "<td class='text-right' id='gnrTime" + i + "'></td>";
				ldatac += "</tr>";
			}
			ldatac += "<tr style='height:5px'>";
			ldatac += "<td class='text-center' colspan='2'></td>";
			ldatac += "</tr>";
			ldata.html( ldatac);
		}
		
		for (var i = 0; i < data.general.logs.length; ++i) {
			$("#gnrTime" + i).text( (parseFloat( (parseFloat(clock) - parseFloat(data.general.logs[i].date_diff)) / 3600.0).toFixed(1)) +' óra');
		}

//// ez egy inaktív blokk      
////		
//		var p4dc = "";
//		p4dc += `
//			<tr>
//				<td class='text-left' style='width:135px'><span class='h4'>P4</span></td>
//				<td class='text-right' style='width:150px'><span class='h4'></span></td>
//			</tr>
//			<tr>
//				<td class='text-left'><span class='h6'>`+data.p4.instructions[0].instruction+`</span></td>
//				<td class='text-right'></td>
//			</tr>
//			<tr>
//				<td class='text-left'>&nbsp;</td>
//				<td class='text-right'></td>
//			</tr>
//		`;
//		for (i = 0; i < data.p4.instructions.length; i++) { 
//			p4dc += "<tr>";
//			p4dc += "<td class='text-left'>"+data.p4.instructions[i].instruction+"</td>";
//			p4dc += "<td class='text-right'>"+data.p4.instructions[i].date+"</td>";
//			p4dc += "</tr>";
//		}
//		p4dc += `
//			<tr>
//				<td class='text-left'>&nbsp;</td>
//				<td class='text-right'></td>
//			</tr>
//			<tr>
//				<td class='text-left'>`+data.p4.last_message+`</td>
//				<td class='text-right'>`+data.p4.last_message_diff+` mp</td>
//			</tr>
//		`;
//		var p4d = $("#p4d");
//		p4d.html( p4dc);

	};
	

	this.ertek = function(r, idx) {
		if (r.length <= idx) {
			return { date : "" , result : 0.0 };
		}
		return r[idx];
	};

	
	this.refreshChart = function(data, reDraw) {

//		console.log("checkSum1: " + checkSum1);
//		console.log("data.p1.checksum: " + data.p1.checksum);
//		console.log("checkSum2: " + checkSum2);
//		console.log("data.p2.checksum: " + data.p2.checksum);
		
		var data1 = null;
		if (checkSum1 != data.p1.checksum) {
			var r1 = data.p1.resistance;
//			console.log(JSON.stringify(r1));
//			console.log(JSON.stringify(this.ertek(r1,0)));
			data1 = [
				["Mérések", "Ellenállás", { role: "style" }, "Optimális", "Savazási határ" ],
				["", 0, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,0).date, this.ertek(r1,0).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,1).date, this.ertek(r1,1).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,2).date, this.ertek(r1,2).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,3).date, this.ertek(r1,3).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,4).date, this.ertek(r1,4).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,5).date, this.ertek(r1,5).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,6).date, this.ertek(r1,6).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,7).date, this.ertek(r1,7).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r1,8).date, this.ertek(r1,8).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				["", 0, "opacity: 0.9; color: #4c90ff", 0.05, 0.2]
			];
//			console.log(JSON.stringify(data1));
		}
		
		
		var data2 = null;
		if (checkSum2 != data.p2.checksum) {
			var r2 = data.p2.resistance;
			data2 = [
				["Mérések", "Ellenállás", { role: "style" }, "Optimális", "Savazási határ" ],
				["", 0, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,0).date, this.ertek(r2,0).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,1).date, this.ertek(r2,1).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,2).date, this.ertek(r2,2).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,3).date, this.ertek(r2,3).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,4).date, this.ertek(r2,4).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,5).date, this.ertek(r2,5).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,6).date, this.ertek(r2,6).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,7).date, this.ertek(r2,7).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				[ this.ertek(r2,8).date, this.ertek(r2,8).result, "opacity: 0.9; color: #4c90ff", 0.05, 0.2],
				["", 0, "opacity: 0.9; color: #4c90ff", 0.05, 0.2]
			];
//			console.log(JSON.stringify(data2));
		}
		
		if (! reDraw) {
			google.charts.load("current", {packages:['corechart']});
			google.charts.setOnLoadCallback(function() { ez.drawChart(data1, data2); });
		} else {
			ez.drawChart(data1, data2);
		}
	};
		
	
	this.drawChart = function(data1, data2) {
		if (data1 != null) {
			data1 = google.visualization.arrayToDataTable(data1);

			var view = new google.visualization.DataView(data1);
			view.setColumns([0, 1,
				{ calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation" },
				2, 3, 4]);
			var options = {
				backgroundColor: '#5c5d5f',
				colors: ['#4c90ff','red'],
				hAxis: {
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					ticks: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
					viewWindow: {
						min: 1,
						max: 10
					}
				},
				vAxis: {
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					ticks: [0, 0.05, 0.10, 0.15, 0.20, 0.25, 0.30],
					viewWindow: {
						min: 0,
						max: 0.3
					}
				},
				series: {
					1: {
						type: 'line',
						color: '#ff8520'
					},
					2: {
						type: 'line',
						color: '#ea4139'
					}
				},
				title: "I. Hőcserélő",
				titleTextStyle: {
					color: 'white',
					bold: true
				},
				width: 569,
				height: 300,
				bar: {
					groupWidth: "70%"
				},
				legend: { 
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					position: "top" 
				},
				chartArea: {
					left: '10%',
					width: '85%'
				}
			};
			var chart = new google.visualization.ColumnChart(gh1);
			chart.draw(view, options);
		}
		
		
		if (data2 != null) {
			data2 = google.visualization.arrayToDataTable(data2);
			
			var view2 = new google.visualization.DataView(data2);
			view2.setColumns([0, 1,
				{ calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation" },
				2, 3, 4]);
			var options2 = {
				backgroundColor: '#5c5d5f',
				colors: ['#4c90ff','red'],
				hAxis: {
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					ticks: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
					viewWindow: {
						min: 1,
						max: 10
					}
				},
				vAxis: {
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					ticks: [0, 0.05, 0.10, 0.15, 0.20, 0.25, 0.30],
					viewWindow: {
						min: 0,
						max: 0.3
					}
				},
				series: {
					1: {
						type: 'line',
						color: '#ff8520'
					},
					2: {
						type: 'line',
						color: '#ea4139'
					}
				},
				title: "II. Hőcserélő",
				titleTextStyle: {
					color: 'white',
					bold: true
				},
				width: 568,
				height: 300,
				bar: {
					groupWidth: "70%"
				},
				legend: { 
					textStyle: {
						bold: true,
						color: '#ffffff'
					},
					position: "top" 
				},
				chartArea: {
					left: '10%',
					width: '85%'
				}
			};
			var chart2 = new google.visualization.ColumnChart(gh2);
			chart2.draw(view2, options2);
			
		}
		
	};


	this.vezerloCella = function(v, status) {
		var link = $("#aVez" + v);
		link.removeClass("btn-success");
		link.removeClass("btn-warning");
		link.removeClass("btn-danger");
		link.removeClass("btn-secondary");
		switch(status) {
			case 0:
				// szürke
				link.addClass("btn-secondary");
				link.addClass("disabled");
				break;
			case 1:
				// zöld
				link.addClass("btn-success");
				link.removeClass("disabled");
				break;
			case 2:
				// sárga
				link.addClass("btn-warning");
				link.removeClass("disabled");
				break;
			case 3:
				// piros
				link.addClass("btn-danger");
				link.removeClass("disabled");
				break;
			default:
				link.addClass("btn-secondary");
				link.addClass("disabled");
				break;
		}

	};


	this.refreshStatus = function(forced) {
		if (! forced && statusFrissites) return;
		if (! forced && vezerles) return;
		statusFrissites = true;

		// console.log('ajax: ' + 'http://'+ez.serverUrl+'/vezerlo/vezerlesStatus.php');
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/vezerlesStatus.php'
			url: '../vezerlo/vezerlesStatus.php'
			, success: function(result) {
				ez.refreshStatusDraw(result);
				statusFrissites = false;
				if (forced && vezerles) {
					setTimeout(function() {
						vezerles = false;
					}, 100);
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				//alert('refreshStatus xhr hiba!');
				statusFrissites = false;
			}
		});
	};


	this.refreshStatusDraw = function(data) {
		if (data.checksum != checkSumStatus) {
			statusDataObj = data;

			var p1 = data.p1;
			var p2 = data.p2;
			
			ez.vezerloCella('000', p1.Savazas);
			ez.vezerloCella('001', p1.Meres);
			ez.vezerloCella('010', p1.Reset);
			ez.vezerloCella('011', p1.Lezaras);

			ez.vezerloCella('100', p2.Savazas);
			ez.vezerloCella('101', p2.Meres);
			ez.vezerloCella('110', p2.Reset);
			ez.vezerloCella('111', p2.Lezaras);

			ez.vezerloCella('2', data.KenyszerUzemallapot);
			ez.vezerloCella('3', data.AutomataVezerles);

			ez.vezerloCella('KeziUzem', data.KeziVezerles);
			keziUzemAllapot = data.KeziVezerles == SARGA;

			if (keziUzemAllapot != keziUzemElozoAllapot) {
				if (!keziUzemAllapot) {
					v1.addClass(INACTIVE_LINK);
					v2.addClass(INACTIVE_LINK);
					v3.addClass(INACTIVE_LINK);
					v4.addClass(INACTIVE_LINK);
					v5.addClass(INACTIVE_LINK);
					v6.addClass(INACTIVE_LINK);
					v7.addClass(INACTIVE_LINK);
					v8.addClass(INACTIVE_LINK);
				} else {
					v1.removeClass(INACTIVE_LINK);
					v2.removeClass(INACTIVE_LINK);
					v3.removeClass(INACTIVE_LINK);
					v4.removeClass(INACTIVE_LINK);
					v5.removeClass(INACTIVE_LINK);
					v6.removeClass(INACTIVE_LINK);
					v7.removeClass(INACTIVE_LINK);
					v8.removeClass(INACTIVE_LINK);
				}
				keziUzemElozoAllapot = keziUzemAllapot;
			}

			switch (parseInt(data.AutomataVezerles)) {
				case SARGA:
					aVez3.html('AUTOMATA VEZÉRLÉS<br>KIKAPCSOLÁSA');
					break;
				case ZOLD:
					aVez3.html('AUTOMATA VEZÉRLÉS<br>BEKAPCSOLÁSA');
					break;
			}
			
			checkSumStatus = data.checksum;
		}
	};


	/// ESEMÉNYEK

	/// vezérlő események
	this.addVezerloEsemenyek = function() {
		
		$("#aVez000").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo000();
			return false;
		});
		$("#aVez001").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo001();
			return false;
		});

		$("#aVez010").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo010();
			return false;
		});
		$("#aVez011").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo011();
			return false;
		});
		$("#aVez100").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo100();
			return false;
		});
		$("#aVez101").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo101();
			return false;
		});
		$("#aVez110").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo110();
			return false;
		});
		$("#aVez111").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo111();
			return false;
		});
		$("#aVez2").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo2();
			return false;
		});
		$("#aVez3").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.vezerlo3();
			return false;
		});
		$("#aVezKeziUzem").on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.keziUzem();
			return false;
		});
		v1.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(1, v1);
			return false;
		});
		v2.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(2, v2);
			return false;
		});
		v3.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(3, v3);
			return false;
		});
		v4.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(4, v4);
			return false;
		});
		v5.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(5, v5);
			return false;
		});
		v6.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(6, v6);
			return false;
		});
		v7.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(7, v7);
			return false;
		});
		v8.on("click", function(event) {
			event.preventDefault();
			if (! vezerles) ez.szelepVezerles(8, v8);
			return false;
		});

	};


	/// 1.savaz
	this.vezerlo000 = function() {
		vezerles = true;
		$("aVez000").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p1Savazas.php'
			url: '../vezerlo/p1Savazas.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo000 xhr hiba!');
				vezerles = false;
			}
		});

	};
	/// 1.mér
	this.vezerlo001 = function() {
		vezerles = true;
		$("aVez001").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p1Meres.php'
			url: '../vezerlo/p1Meres.php'
			, success: function(result) {
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo001 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 1.reset
	this.vezerlo010 = function() {
		let hibak = ez.funkcioEllenorzes();
		if (hibak != '') {
			alert(hibak);
			return;
		}
		vezerles = true;
		$("aVez010").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p1Reset.php'
			url: '../vezerlo/p1Reset.php'
			, success: function(result) {
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo010 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 1.lezár
	this.vezerlo011 = function() {
		vezerles = true;
		$("aVez011").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p1Lezaras.php'
			url: '../vezerlo/p1Lezaras.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo011 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 2.savaz
	this.vezerlo100 = function() {
		vezerles = true;
		$("aVez100").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p2Savazas.php'
			url: '../vezerlo/p2Savazas.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo100 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 2.mér
	this.vezerlo101 = function() {
		vezerles = true;
		$("aVez101").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p2Meres.php'
			url: '../vezerlo/p2Meres.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo101 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 2.reset
	this.vezerlo110 = function() {
		let hibak = ez.funkcioEllenorzes();
		if (hibak != '') {
			alert(hibak);
			return;
		}
		vezerles = true;
		$("aVez110").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p2Reset.php'
			url: '../vezerlo/p2Reset.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo110 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// 2.lezár
	this.vezerlo111 = function() {
		vezerles = true;
		$("aVez111").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/p2Lezaras.php'
			url: '../vezerlo/p2Lezaras.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('vezerlo111 xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// üzemállapot
	this.vezerlo2 = function() {
		vezerles = true;
		$("aVez2").addClass("disabled");
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/kenyszerUzemallapot.php'
			url: '../vezerlo/kenyszerUzemallapot.php'
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('üzemállapot xhr hiba!');
				vezerles = false;
			}
		});
	};
	/// automata vezérlés
	this.vezerlo3 = function() {
		vezerles = true;
		$("aVez3").addClass("disabled");
		if (statusDataObj == null) return;
		var api = null;
		switch (parseInt(statusDataObj.AutomataVezerles)) {
			case SARGA:
				// api = 'http://'+ez.serverUrl+'/vezerlo/automataVezerlesKikapcsolasa.php';
				api = '../vezerlo/automataVezerlesKikapcsolasa.php';
				break;
			case ZOLD:
				// api = 'http://'+ez.serverUrl+'/vezerlo/automataVezerlesBekapcsolasa.php';
				api = '../vezerlo/automataVezerlesBekapcsolasa.php';
				break;
		}
//		console.log(api);
		$.ajax({
			url: api
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('automataVezérlés xhr hiba!');
				vezerles = false;
			}
		});
	};

	this.keziUzem = function() {
		vezerles = true;		
		$("aVez2").addClass("disabled");
		let keziVezPostfix = keziUzemAllapot ? 'Ki.php' : 'Be.php';
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/keziUzemmod' + keziVezPostfix
			url: '../vezerlo/keziUzemmod' + keziVezPostfix
			, success: function(result){
				ez.refreshStatus(true);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('üzemállapot xhr hiba!');
				vezerles = false;
			}
		});

	};


	this.koztesAllapotraSzinez = function(v1, v2) {
		v1.removeClass(BTN_DANGER);
		v1.removeClass(BTN_SUCESS);
		v1.removeClass(BTN_WARNING);
		v2.removeClass(BTN_DANGER);
		v2.removeClass(BTN_SUCESS);
		v2.removeClass(BTN_WARNING);
		v1.addClass(BTN_WARNING);
		v2.addClass(BTN_WARNING);
	};


	this.szelepVezerles = function(id, control) {

		let data = JSON.parse(JSON.stringify(lastData));

		//vezerles = true;
		console.log('szelepVezerles  id:' + id );
		

		switch (id) {
			case 1:
				if (data.general.controllers.v1 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v1, v3);
					ez.keziUtasitastKuld('p1', 'v1_v3_nyitas', 30);
				} else if (data.general.controllers.v1 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v1, v3);
					ez.keziUtasitastKuld('p1', 'v1_v3_zaras', 31);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 2:
				if (data.general.controllers.v2 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v2, v4);
					ez.keziUtasitastKuld('p1', 'v2_v4_nyitas', 32);
				} else if (data.general.controllers.v2 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v2, v4);
					ez.keziUtasitastKuld('p1', 'v2_v4_zaras', 33);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 3:
				if (data.general.controllers.v3 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v1, v3);
					ez.keziUtasitastKuld('p1', 'v3_v1_nyitas', 34);
				} else if (data.general.controllers.v3 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v1, v3);
					ez.keziUtasitastKuld('p1', 'v3_v1_zaras', 35);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 4:
				if (data.general.controllers.v4 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v2, v4);
					ez.keziUtasitastKuld('p1', 'v4_v2_nyitas', 36);
				} else if (data.general.controllers.v4 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v2, v4);
					ez.keziUtasitastKuld('p1', 'v4_v2_zaras', 37);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 5:
				if (data.general.controllers.v5 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v5, v7);
					ez.keziUtasitastKuld('p2', 'v5_v7_nyitas', 38);
				} else if (data.general.controllers.v5 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v5, v7);
					ez.keziUtasitastKuld('p2', 'v5_v7_zaras', 39);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 6:
				if (data.general.controllers.v6 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v6, v8);
					ez.keziUtasitastKuld('p2', 'v6_v8_nyitas', 40);
				} else if (data.general.controllers.v6 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v6, v8);
					ez.keziUtasitastKuld('p2', 'v6_v8_zaras', 41);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 7:
				if (data.general.controllers.v7 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v5, v7);
					ez.keziUtasitastKuld('p2', 'v7_v5_nyitas', 42);
				} else if (data.general.controllers.v7 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v5, v7);
					ez.keziUtasitastKuld('p2', 'v7_v5_zaras', 43);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
			case 8:
				if (data.general.controllers.v8 == BTN_DANGER) {
					ez.koztesAllapotraSzinez(v6, v8);
					ez.keziUtasitastKuld('p2', 'v8_v6_nyitas', 44);
				} else if (data.general.controllers.v8 == BTN_SUCESS) {
					ez.koztesAllapotraSzinez(v6, v8);
					ez.keziUtasitastKuld('p2', 'v8_v6_zaras', 45);
				} else {
					alert('A szelep köztes állapotban van!');
				}
				break;
		}

	};


	this.keziUtasitastKuld = function(receiver, instructionname, instructionid) {

		vezerles = true;
		//console.log('keziUtasitastKuld  receiver: ' + receiver + '   instructionname: ' + instructionname + '   instructionid: ' + instructionid);
		post = { receiver: receiver , instruction: { name: instructionname, id: instructionid} };
		console.log(JSON.stringify(post));
		
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/vezerlo/keziUtasitas.php'
			url: '../vezerlo/keziUtasitas.php'
			, type: 'POST'
			, contentType: 'application/json; charset=utf-8'
			, dataType: 'json'
			, data: JSON.stringify(post)
			, success: function(result) {
				if (! result.ok) {
					alert('A(z) ' + instructionname + ' nem engedélyezett, amíg egy utasítás folyamatban van!');
				} else {
					console.log('keziUtasitas.php: ' + JSON.stringify(result));
				}
				vezerles = false;
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('keziUtasitas xhr hiba!');
				vezerles = false;
			}
		});

	}


	logout.on("click", function() {	
		vezerles = true;
		
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/login/kilepes.php'
			url: '../login/kilepes.php'
			, success: function(result) {
				vezerles = false;
				// window.location.assign('http://'+ez.serverUrl+'/login.php');
				window.location.assign('../login.php');
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				vezerles = false;
				frissites = false;
				console.log('kilepes.php hiba');
			}
		});
	});
	
	pid.on("click", function() {	
		vezerles = true;
		window.location.assign('../pid.php');
	});	


	p1ping.on("click", function() {	
		cmdout.html('A parancs elindult<br>');
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/dummy.php?parancs=P1_ping'
			url: '../dummy.php?parancs=P1_ping'
			, success: function(result) {
				cmdout.html(cmdout.html() + result);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				cmdout.html('hibás parancs');
			}
		});
	});

	p2ping.on("click", function() {	
		cmdout.html('A parancs elindult<br>');
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/dummy.php?parancs=P2_ping'
			url: '../dummy.php?parancs=P2_ping'
			, success: function(result) {
				cmdout.html(cmdout.html() + result);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				cmdout.html('hibás parancs');
			}
		});
	});


	p1restart.on("click", function() {
		cmdout.html('A parancs elindult<br>');
		let hibak = ez.funkcioEllenorzes();
		if (hibak != '') {
			cmdout.html(hibak.split("\n").join('<br>'));
			alert(hibak);
			return;
		}
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/dummy.php?parancs=P1_start'
			url: '../dummy.php?parancs=P1_start'
			, success: function(result) {
				cmdout.html(cmdout.html() + result);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				cmdout.html('hibás parancs');
			}
		});
	});

	p2restart.on("click", function() {	
		cmdout.html('A parancs elindult<br>');
		let hibak = ez.funkcioEllenorzes();
		if (hibak != '') {
			cmdout.html(hibak.split("\n").join('<br>'));
			alert(hibak);
			return;
		}
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/dummy.php?parancs=P2_start'
			url: '../dummy.php?parancs=P2_start'
			, success: function(result) {
				cmdout.html(cmdout.html() + result);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				cmdout.html('hibás parancs');
			}
		});
	});
	
	this.funkcioEllenorzes = function() {
		let clone = JSON.parse(JSON.stringify(lastData));
		console.log(clone);
		let ksz = clone.general.controllers.ksz;
		let kszState = ksz.substring(ksz.length-1, ksz.length);
		let rossz = false;
		let hibak = '';
		let p1m = clone.p1.measurements;
		p1m.forEach(function (item, index) {
			console.log('P1.' + item.port + '=' + item.value);
  			if ((item.port == 'G16' && parseInt(item.value) == 1) || (item.port == 'G12' && parseInt(item.value) == 1)) {
				rossz = true;
			}
		});
		let p2m = clone.p2.measurements;
		p2m.forEach(function (item, index) {
			console.log('P2.' + item.port + '=' + item.value);
  			if ((item.port == 'G16' && parseInt(item.value) == 1) || (item.port == 'G12' && parseInt(item.value) == 1)) {
				rossz = true;
			}
		});
		if (rossz) {
			hibak += 'Nem végezhető el indítás/újraindítás, amíg savazó szelepek nyitva vannak!'+"\n";
		}
		if (kszState != 'r') {
			hibak += 'Nem végezhető el indítás/újraindítás, amíg a sav keringtető szivattyú nyitva van!'+"\n";
		}
		return hibak;
	};


	this.nyofTablaFrissites = function() {
		if (! p4Aktiv) {
			nyfe1.text('---');
			//nyfe2.text('---');
			nyfe3.text('---');
			nyfe4.text('---');
			nyfetm.text('---------');
			return;
		}
		$.ajax({
			url: '../nyofdata.php'
			, success: function(result) {
				nyfe1.text(result.G17);
				//nyfe2.text(result.G16);
				nyfe3.text(result.A5);
				
				if (parseInt(result.inv) == 1) {
					nyfe4.text('H');
					nyfi.css('background-color','#5f875a');
				} else {
					nyfe4.text('L');
					nyfi.css('background-color','#ce342c');
				}
				
				nyfetm.text(result.time);
				
				if (result.alarm == '1') {
					nyfr.css('background-color','#ce342c');
					nyfalarm = true;
				} else if (nyfalarm) {
					nyfr.css('background-color','#5f875a');
					nyfalarm = false;
				}
				
				var log = '';
				if (parseInt(result.sms1) == 1) {
					log += '<br>1.Elküldött sms: A NYOMAS MAGAS > 8 bar...';
				}
				if (parseInt(result.sms2) == 1) {
					log += '<br>2.Elküldött sms: A SZIVATTYU LEALLT MAGAS NYOMAS MIATT...';
				}
				if (parseInt(result.sms3) == 1) {
					log += '<br>3.Elküldött sms: A SZIVATTYU LEALLT, NEM UZEMEL...';
				}
				if (parseInt(result.sms4) == 1) {
					log += '<br>4.Elküldött sms: A NYOMAS ALACSONY! ...';
				}
				nyflog.html(log);
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				nyfe1.text('---');
				//nyfe2.text('---');
				nyfe3.text('---');
				//nyfe4.text('---');
				nyfetm.text('---------');
			}
		});
	};

	p4reset.on("click", function() {	
		cmdout.html('A parancs elindult<br>');
		$.ajax({
			url: '../nyofreset.php'
			, success: function(result) {
				cmdout.html('A P4 újraindul...');
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				cmdout.html('hibás parancs');
			}
		});
	});

	usrmngmt.on("click", function() {	
		window.location.replace('./usrmngmt.php');
	});

	passreplace.on("click", function() {	
		window.location.replace('./passreplace.php');
	});



}
