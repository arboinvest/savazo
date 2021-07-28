function PID() {
	var ez = this;
	/// paraméterek
	var serverUrl = null;
	var nyofURL = null;
	/// html
	var hatter = $("#hatter");
	var sz1 = $("#sz1");
	var v1 = $("#v1");
	var v2 = $("#v2");
	var v3 = $("#v3");
	var v4 = $("#v4");
	var v5 = $("#v5");
	var v6 = $("#v6");
	var v7 = $("#v7");
	var v8 = $("#v8");
	var hRiaszt = $("#hRiaszt");
	var hMax = $("#hMax");
	var hMin = $("#hMin");
	var nysz = $("#nysz");
	var nyszErtek = $("#nyszErtek");
	var ph2Value = $("#ph2Value");
	var ph1Value = $("#ph1Value");
	var t1Value = $("#t1Value");
	var t2Value = $("#t2Value");
	var t3Value = $("#t3Value");
	var t4Value = $("#t4Value");
	var m4Value = $("#m4Value");
	var p1Value = $("#p1Value");
	var p2Value = $("#p2Value");
	var t5Value = $("#t5Value");
	var p3Value = $("#p3Value");
	var keziVez = $("#keziVez");
	var time = $("#time");
	var ind = $("#ind");
	var nyszRiaszt = $("#nyszRiaszt");
	var szintRiaszt = $("#szintRiaszt");
	var p1Div = $("#p1Div");
	var p2Div = $("#p2Div");
	var p3Div = $("#p3Div");
	var p4Div = $("#p4Div");
	var v1ny = $("#v1ny");
	var v2ny = $("#v2ny");
	var v3ny = $("#v3ny");
	var v4ny = $("#v4ny");
	var v5ny = $("#v5ny");
	var v6ny = $("#v6ny");
	var v7ny = $("#v7ny");
	var v8ny = $("#v8ny");
	var v1nyDiv = $("#v1nyDiv");
	var v2nyDiv = $("#v2nyDiv");
	var v3nyDiv = $("#v3nyDiv");
	var v4nyDiv = $("#v4nyDiv");
	var v5nyDiv = $("#v5nyDiv");
	var v6nyDiv = $("#v6nyDiv");
	var v7nyDiv = $("#v7nyDiv");
	var v8nyDiv = $("#v8nyDiv");
	var logout = $("#logout");
		
	//változók
	var xoffset = 0;
	var yoffset = 30;
	
	var refreshSessionInterval = null;
	var refreshInterval = null;
	var refreshInterval2 = null;
	var frissites = false;
	var vezerles = false;
	var data = null;
	var nyofUtemezes = 0;
	var indChars = ["▄","&nbsp;▄","&nbsp;&nbsp;▄", "&nbsp;&nbsp;&nbsp;▄", "&nbsp;&nbsp;&nbsp;&nbsp;▄",   "&nbsp;&nbsp;&nbsp;▄", "&nbsp;&nbsp;▄", "&nbsp;▄"];
	var indCount = 0;
	var refreshCount = 2;
	// szelep állapotok
	var v1NY = 1;
	var v1Z = 1;
	var v2NY = 1;
	var v2Z = 1;
	var v3NY = 1;
	var v3Z = 1;
	var v4NY = 1;
	var v4Z = 1;
	var v5NY = 1;
	var v5Z = 1;
	var v6NY = 1;
	var v6Z = 1;
	var v7NY = 1;
	var v7Z = 1;
	var v8NY = 1;
	var v8Z = 1;
	var keziMode = false;
	var nyszRiasztMode = false;
	var szintRiasztMode = false;
	var villogas = true;
	var keziVezIdo = 0;
	var keziVezSzelep = 0;
	
	// konstansok
	var ELTOLAS_X = +1;
	var ELTOLAS_Y = +3;
	
	var PIROS = "#a00000";
	var ZOLD = "#00a000";
	var SZ_NYITOTT = "./img/sz.png";
	var SZ_ZART = "./img/sz_zart.png";
	var SZ_KOZTES = "./img/sz_koz.png";
	var SZINT_OK = "./img/szint-tr.png"
	var SZINT_RIASZT = "./img/szint-tr_alrm.png"
	var VEGALLAS = ['L', 'H'];
	
	var VA_FORMAT1 = '<span style="color: red">';
	var VA_FORMAT2 = '</span>&nbsp;<span style="color: green">';
	var VA_FORMAT3 = '</span>';

	var helyek = [
		['sz1', 508, 213 ]
		, ['v3', 650, 328 ]
		, ['v4', 559, 364 ]
		, ['v1', 650, 402 ]
		, ['v2', 559, 441 ]
		, ['v7', 650, 516 ]
		, ['v8', 559, 552 ]
		, ['v5', 650, 591 ]
		, ['v6', 559, 629 ]
		, ['hRiaszt', 792, 61 ]
		, ['hMax', 792, 87 ]
		, ['hMin', 792, 155 ]
		, ['nysz', 88, 626 ]
		, ['ph1Value', 672, 22]
		, ['ph2Value', 586, 144]
		, ['t2Value', 755, 322]
		, ['t1Value', 755, 396]
		, ['t4Value', 755, 510]
		, ['t3Value', 755, 586]
		, ['m4Value', 243, 396]
		, ['p2Value', 315, 321]
		, ['p1Value', 324, 396]
		, ['t5Value', 70, 496]
		, ['p3Value', 70, 553]
		, ['keziVez', 124, 59]
		, ['time', 120, 21]
		, ['ind', 823, 4]
		, ['p3', 931, 120]
		, ['p1', 931, 408]
		, ['p2', 931, 595]
		, ['v3ny', 665, 353]
		, ['v4ny', 570, 355]
		, ['v1ny', 665, 424]
		, ['v2ny', 568, 434]
		, ['v7ny', 664, 539]
		, ['v8ny', 567, 544]
		, ['v5ny', 664, 617]
		, ['v6ny', 571, 624]
		, ['p4', 153, 605]
		, ['nyszRiaszt', 138, 656]
		, ['szintRiaszt', 782, 32]
		, ['logout', 873, 1]
		, ['dashb', 873, 667]
		, ['nyszErtek', 97, 614]
	];



	this.init = function(_serverUrl, _nyofURL) {
		this.serverUrl = _serverUrl;
		this.nyofURL = _nyofURL;
		var xoffset = (window.innerWidth / 2) - 485;
		if (xoffset < 0) xoffset = 0;
		hatter.css('left', xoffset);
		hatter.css('top', yoffset);
		for (i = 0; i < helyek.length; ++i) {
			let elem = $("#" + helyek[i][0] + "Div");
			elem.css('left', parseInt(helyek[i][1]) + xoffset + ELTOLAS_X);
			elem.css('top', parseInt(helyek[i][2]) + yoffset + ELTOLAS_Y);
		}

		time.text(new Date().toLocaleString());
		p1Div.css('color', PIROS);
		p2Div.css('color', PIROS);
		p3Div.css('color', ZOLD);
		p4Div.css('color', PIROS);
		
		ez.nyofTablaFrissites();

		refreshInterval = setInterval(function() {

			
			if (indCount > 7) {
				indCount = 0;
			}
			ind.html(indChars[indCount]);
			++indCount;
			
			if (refreshCount > 1) {
				time.text(new Date().toLocaleString());
				ez.refreshJsonData();
				refreshCount = 0;
			}
			++ refreshCount;

			++ nyofUtemezes;
			if (nyofUtemezes > 8) {
				nyofUtemezes = 0;
				ez.nyofTablaFrissites();
			}
			
			if (nyszRiasztMode) {
				if (villogas) {
					nyszRiaszt.text('RIASZTÁS!!');
				} else {
					nyszRiaszt.text('');
				}
				
			}
			if (szintRiasztMode) {
				if (villogas) {
					szintRiaszt.text('RIASZTÁS!!');
				} else {
					szintRiaszt.text('');
				}
			}
			if (keziVezIdo > 0) {
				--keziVezIdo;
				var akt = NaN;
				switch (keziVezSzelep) {
					case 1: akt = v1nyDiv; break;
					case 2: akt = v2nyDiv; break;
					case 3: akt = v3nyDiv; break;
					case 4: akt = v4nyDiv; break;
					case 5: akt = v5nyDiv; break;
					case 6: akt = v6nyDiv; break;
					case 7: akt = v7nyDiv; break;
					case 8: akt = v8nyDiv; break;
				}
				if (keziVezIdo > 1) {
					if (villogas) {
						akt.show();
					} else {
						akt.hide();
					}					
				} else {
					akt.show();
				}
			}
			if (nyszRiasztMode || szintRiasztMode || keziVezIdo > 0) {
				villogas = ! villogas;
			}

		},500);

		ez.refreshStatus(true);
		refreshInterval2 = setInterval(function(){
			ez.refreshStatus(false);
		},1300);


		refreshSessionInterval = setInterval(function() {
//			console.log("refreshSessions()");
			ez.refreshSessions();
		}, 1200000 );

		
	};

	this.refreshSessions = function() {
		$.ajax({
			url: '../pid.php'
			, success: function(result) {
				// drop this!
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('refreshSession xhr hiba!');
			}
		});
	};
	
	this.refreshJsonData = function() {
		if (frissites) return false;
		
		$.ajax({
			url: '../data.php'
			, success: function(result){
				if (! frissites) {
//					console.log(JSON.stringify(result));
					frissites = true;

					data = result;
					
					switch (data.general.controllers.v1.charAt(4)) {
						case 'd' : 
							v1.attr('src', SZ_ZART);
							break;
						case 's' :
							v1.attr('src', SZ_NYITOTT);
							break;
						default:
							v1.attr('src', SZ_KOZTES);
					}
					
					switch (data.general.controllers.v2.charAt(4)) {
						case 'd' : 
							v2.attr('src', SZ_ZART);
							break;
						case 's' :
							v2.attr('src', SZ_NYITOTT);
							break;
						default:
							v2.attr('src', SZ_KOZTES);
					}
					
					switch (data.general.controllers.v3.charAt(4)) {
						case 'd' : 
							v3.attr('src', SZ_ZART);
							break;
						case 's' :
							v3.attr('src', SZ_NYITOTT);
							break;
						default:
							v3.attr('src', SZ_KOZTES);
					}

					switch (data.general.controllers.v4.charAt(4)) {
						case 'd' : 
							v4.attr('src', SZ_ZART);
							break;
						case 's' :
							v4.attr('src', SZ_NYITOTT);
							break;
						default:
							v4.attr('src', SZ_KOZTES);
					}

					switch (data.general.controllers.v5.charAt(4)) {
						case 'd' : 
							v5.attr('src', SZ_ZART);
							break;
						case 's' :
							v5.attr('src', SZ_NYITOTT);
							break;
						default:
							v5.attr('src', SZ_KOZTES);
					}

					switch (data.general.controllers.v6.charAt(4)) {
						case 'd' : 
							v6.attr('src', SZ_ZART);
							break;
						case 's' :
							v6.attr('src', SZ_NYITOTT);
							break;
						default:
							v6.attr('src', SZ_KOZTES);
					}

					switch (data.general.controllers.v7.charAt(4)) {
						case 'd' : 
							v7.attr('src', SZ_ZART);
							break;
						case 's' :
							v7.attr('src', SZ_NYITOTT);
							break;
						default:
							v7.attr('src', SZ_KOZTES);
					}
					
					switch (data.general.controllers.v8.charAt(4)) {
						case 'd' : 
							v8.attr('src', SZ_ZART);
							break;
						case 's' :
							v8.attr('src', SZ_NYITOTT);
							break;
						default:
							v8.attr('src', SZ_KOZTES);
					}
					
					switch (data.general.controllers.ksz.charAt(3)) {
						case 's' :
							sz1.attr('src', "./img/sziv");
							break;
						case 'd' : 
							sz1.attr('src', "./img/sziv_all.png");
							break;
					}
					
					switch (data.p1.class.charAt(3)) {
						case 's' :
							p1Div.css('color', ZOLD);
							break;
						case 'd' : 
							p1Div.css('color', PIROS);
							break;
					}
					
					switch (data.p2.class.charAt(3)) {
						case 's' :
							p2Div.css('color', ZOLD);
							break;
						case 'd' : 
							p2Div.css('color', PIROS);
							break;
					}
					
					switch (data.p3.class.charAt(3)) {
						case 's' :
							p3Div.css('color', ZOLD);
							break;
						case 'd' : 
							p3Div.css('color', PIROS);
							break;
					}
					
					for (i=0; i < data.p3.measurements.length; ++i) {
						switch (data.p3.measurements[i].port) {
							case 'A1' :
								ph1Value.text( parseFloat(data.p3.measurements[i].value).toFixed(1) + ' pH');
								break;
							case 'A2' :
								ph2Value.text(parseFloat(data.p3.measurements[i].value).toFixed(1) + ' pH');
								break;
							case 'A3' :
								m4Value.text(parseFloat(data.p3.measurements[i].value).toFixed(1) + ' m3/h');
								break;
							case 'A4' :
								p1Value.text(parseFloat(data.p3.measurements[i].value).toFixed(1) /*+ ' bar'*/);
								break;
							case 'A5' :
								p2Value.text(parseFloat(data.p3.measurements[i].value).toFixed(1) /*+ ' bar'*/);
								break;
							case 'G5' :
								if (data.p3.measurements[i].value == 1) {
									hRiaszt.attr('src', SZINT_OK);
									szintRiasztMode = false;
								} else {
									hRiaszt.attr('src', SZINT_RIASZT);
									szintRiasztMode = true;
								}
								break;
							case 'G12' :
								if (data.p3.measurements[i].value == 1) {
									hMax.attr('src', SZINT_OK);
								} else {
									hMax.attr('src', SZINT_RIASZT);
								}
								break;
							case 'G6' :
								if (data.p3.measurements[i].value == 1) {
									hMin.attr('src', SZINT_OK);
								} else {
									hMin.attr('src', SZINT_RIASZT);
								}
								break;
						}
					}
					
					for (i=0; i < data.p1.measurements.length; ++i) {
						switch (data.p1.measurements[i].port) {
							case 'G25' :
								t2Value.text( parseFloat(data.p1.measurements[i].value).toFixed(1) + ' °C');
								t2Value.removeClass('szamok');
								t2Value.removeClass('inaktiv');
								if (parseFloat(data.p1.measurements[i].value) != 0.0) {
									t2Value.addClass('szamok');
								} else {
									t2Value.addClass('inaktiv');
								}
								break;
							case 'G27' :
								t1Value.text( parseFloat(data.p1.measurements[i].value).toFixed(1) + ' °C');
								t1Value.removeClass('szamok');
								t1Value.removeClass('inaktiv');
								if (parseFloat(data.p1.measurements[i].value) != 0.0) {
									t1Value.addClass('szamok');
								} else {
									t1Value.addClass('inaktiv');
								}
								break;

							case 'G13' :
								v1NY = parseInt(data.p1.measurements[i].value);
								break;
							case 'G16' :
								v1Z = parseInt(data.p1.measurements[i].value);
								break;

							case 'G21' :
								v2NY = parseInt(data.p1.measurements[i].value);
								break;
							case 'G22' :
								v2Z = parseInt(data.p1.measurements[i].value);
								break;

							case 'G6' :
								v3NY = parseInt(data.p1.measurements[i].value);
								break;
							case 'G12' :
								v3Z = parseInt(data.p1.measurements[i].value);
								break;

							case 'G19' :
								v4NY = parseInt(data.p1.measurements[i].value);
								break;
							case 'G20' :
								v4Z = parseInt(data.p1.measurements[i].value);
								break;

						}
					}
					
					for (i=0; i < data.p2.measurements.length; ++i) {
						switch (data.p2.measurements[i].port) {
							case 'G25' :
								t4Value.text( parseFloat(data.p2.measurements[i].value).toFixed(1) + ' °C');
								break;
							case 'G27' :
								t3Value.text( parseFloat(data.p2.measurements[i].value).toFixed(1) + ' °C');
								break;
							case 'G13' :
								v5NY = parseInt(data.p2.measurements[i].value);
								break;
							case 'G16' :
								v5Z = parseInt(data.p2.measurements[i].value);
								break;
							case 'G21' :
								v6NY = parseInt(data.p2.measurements[i].value);
								break;
							case 'G22' :
								v6Z = parseInt(data.p2.measurements[i].value);
								break;
							case 'G6' :
								v7NY = parseInt(data.p2.measurements[i].value);
								break;
							case 'G12' :
								v7Z = parseInt(data.p2.measurements[i].value);
								break;
							case 'G19' :
								v8NY = parseInt(data.p2.measurements[i].value);
								break;
							case 'G20' :
								v8Z = parseInt(data.p2.measurements[i].value);
								break;
						}
					}

					v1ny.html(VA_FORMAT1 + VEGALLAS[v1Z] + VA_FORMAT2 + VEGALLAS[v1NY] + VA_FORMAT3);
					v2ny.html(VA_FORMAT1 + VEGALLAS[v2Z] + VA_FORMAT2 + VEGALLAS[v2NY] + VA_FORMAT3);
					v3ny.html(VA_FORMAT1 + VEGALLAS[v3Z] + VA_FORMAT2 + VEGALLAS[v3NY] + VA_FORMAT3);
					v4ny.html(VA_FORMAT1 + VEGALLAS[v4Z] + VA_FORMAT2 + VEGALLAS[v4NY] + VA_FORMAT3);

					v5ny.html(VA_FORMAT1 + VEGALLAS[v5Z] + VA_FORMAT2 + VEGALLAS[v5NY] + VA_FORMAT3);
					v6ny.html(VA_FORMAT1 + VEGALLAS[v6Z] + VA_FORMAT2 + VEGALLAS[v6NY] + VA_FORMAT3);
					v7ny.html(VA_FORMAT1 + VEGALLAS[v7Z] + VA_FORMAT2 + VEGALLAS[v7NY] + VA_FORMAT3);
					v8ny.html(VA_FORMAT1 + VEGALLAS[v8Z] + VA_FORMAT2 + VEGALLAS[v8NY] + VA_FORMAT3);

					frissites = false;
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				frissites = false;
				console.log('data.php hiba');
			}
		});
		
		return true;
	};
	

	this.nyofTablaFrissites = function() {
		
		$.ajax({
			url: '../nyofdata.php'
			, success: function(result) {
				
				t5Value.removeClass('szamok');
				t5Value.removeClass('inaktiv');
				if (result.G17 != '0.0') {
					t5Value.addClass('szamok');
					t5Value.text(result.G17 + ' °C');
				} else {
					t5Value.addClass('inaktiv');
					t5Value.text(result.G17 + ' °C');
				}

				p3Value.removeClass('szamok');
				p3Value.removeClass('inaktiv');
				if (result.A5 != '0.0') {
					p3Value.addClass('szamok');
					p3Value.text(result.A5 + ' bar');
				} else {
					p3Value.addClass('inaktiv');
					p3Value.text(result.A5 + ' bar');
				}
				
				if (parseInt(result.inv) == 1) {
					nysz.attr('src', "./img/csziv-tr.png");
					nyszErtek.text('H');
					nyszErtek.css('color', ZOLD);
				} else {
					nysz.attr('src', "./img/csziv-tr_all.png");
					nyszErtek.text('L');
					nyszErtek.css('color', PIROS);
				}
				p4Div.css('color', ZOLD);
				
				if (parseInt(result.alarm) == 1) {
					nyszRiasztMode = true;
				} else {
					nyszRiasztMode = false;
				}
				
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				t5Value.removeClass('szamok');
				t5Value.removeClass('inaktiv');
				t5Value.addClass('inaktiv');
				t5Value.text('---');
				p3Value.removeClass('szamok');
				p3Value.removeClass('inaktiv');
				p3Value.addClass('inaktiv');
				p3Value.text('---');
				p4Div.css('color', PIROS);
				nyszRiasztMode = false;
			}
		});		
		
		
	};

	this.refreshStatus = function(forced) {
		if (! forced && statusFrissites) return;
		if (! forced && vezerles) return;
		statusFrissites = true;
		$.ajax({
			url: '../vezerlo/vezerlesStatus.php'
			, success: function(result) {
				//console.log('refreshStatusDraw');
				ez.refreshStatusDraw(result);
				statusFrissites = false;
				if (forced && vezerles) {
					setTimeout(function() {
						vezerles = false;
					}, 100);
				}
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				alert('refreshStatus xhr hiba!');
				statusFrissites = false;
			}
		});
	};

	this.refreshStatusDraw = function(data) {
		//console.log('refreshStatusDraw.. elindult');
		//if (! isNaN(keziVez)) {
			//console.log('refreshStatusDraw.. data.KeziVezerles: ' + data.KeziVezerles);
			switch (parseInt(data.KeziVezerles)) {
				case 1 :
					keziMode = false;
					keziVez.prop("value", "Kézi vezérlés BE");
					keziVez.prop("disabled", false);
					break;
				case 2 :
					keziMode = true;
					keziVez.prop("value", "Kézi vezérlés KI");
					keziVez.prop("disabled", false);
					break;
				default :
					keziMode = false;
					keziVez.prop("disabled", true);
			}
		//}
	};

	
	this.sz1Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		
	};

	this.v1Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v1Z==0 && (v2Z==1 || v4Z == 1) ) {
			alert('Nem nyitható amíg a V2,V4 nincs zárva!');
			return;
		}
		if ((v1Z==1 && v1NY==1) || (v3Z==1 && v3NY==1)) {
			alert('A v1,v3 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 1;
		if (v1Z==1) {
			ez.keziUtasitastKuld('p1', 'v1_v3_nyitas', 30);			
		} else {
			ez.keziUtasitastKuld('p1', 'v1_v3_zaras', 31);
		}
		
	};
	
	this.v2Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v2Z==0 && (v3Z==1 || v1Z==1)) {
			alert('Nem nyitható amíg a V3,V1 nincs zárva!');
			return;
		}
		if ((v2Z==1 && v2NY==1) || (v4Z==1 && v4NY==1)) {
			alert('A v2,v4 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 2;
		if (v2Z==1) {
			ez.keziUtasitastKuld('p1', 'v2_v4_nyitas', 32);
		} else {
			ez.keziUtasitastKuld('p1', 'v2_v4_zaras', 33);
		}

		
	};
	
	this.v3Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v3Z==0 && (v2Z==1 || v4Z==1)) {
			alert('Nem nyitható amíg a V2,V4 nincs zárva!');
			return;
		}
		if ((v1Z==1 && v1NY==1) || (v3Z==1 && v3NY==1)) {
			alert('A v1,v3 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 3;
		if (v3Z==1) {
			ez.keziUtasitastKuld('p1', 'v1_v3_nyitas', 30);			
		} else {
			ez.keziUtasitastKuld('p1', 'v1_v3_zaras', 31);
		}


	};
	
	this.v4Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v4Z==0 && (v3Z==1 || v1Z==1)) {
			alert('Nem nyitható amíg a V3,V1 nincs zárva!');
			return;
		}
		if ((v2Z==1 && v2NY==1) || (v4Z==1 && v4NY==1)) {
			alert('A v2,v4 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 4;
		if (v4Z==1) {
			ez.keziUtasitastKuld('p1', 'v2_v4_nyitas', 32);
		} else {
			ez.keziUtasitastKuld('p1', 'v2_v4_zaras', 33);
		}
		
		
	};
	
	this.v5Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v5Z==0 && (v6Z==1 || v8Z==1)) {
			alert('Nem nyitható amíg a V6,V8 nincs zárva!');
			return;
		}
		if ((v5Z==1 && v5NY==1) || (v7Z==1 && v7NY==1)) {
			alert('A v5,v7 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 5;
		if (v5Z==1) {
			ez.keziUtasitastKuld('p2', 'v5_v7_nyitas', 38);
		} else {
			ez.keziUtasitastKuld('p2', 'v5_v7_zaras', 39);
		}

	};

	this.v6Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v6Z==0 && (v5Z==1 || v7Z==1)) {
			alert('Nem nyitható amíg a V5,V7 nincs zárva!');
			return;
		}
		if ((v6Z==1 && v6NY==1) || (v8Z==1 && v8NY==1)) {
			alert('A v6,v8 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 6;
		if (v6Z==1) {
			ez.keziUtasitastKuld('p2', 'v6_v8_nyitas', 40);
		} else {
			ez.keziUtasitastKuld('p2', 'v6_v8_zaras', 41);
		}

	};

	this.v7Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v7Z==0 && (v6Z==1 || v8Z==1)) {
			alert('Nem nyitható amíg a V6,V8 nincs zárva!');
			return;
		}
		if ((v5Z==1 && v5NY==1) || (v7Z==1 && v7NY==1)) {
			alert('A v5,v7 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 7;
		if (v7Z==1) {
			ez.keziUtasitastKuld('p2', 'v5_v7_nyitas', 38);
		} else {
			ez.keziUtasitastKuld('p2', 'v5_v7_zaras', 39);
		}


	};

	this.v8Click = function() {
		if (! keziMode) {
			alert('A kézi vezérlés nincs bekapcsolva, vagy nem engedélyezett!');
			return;
		}
		if (v8Z==0 && (v5Z==1 || v7Z==1)) {
			alert('Nem nyitható amíg a V5,V7 nincs zárva!');
			return;
		}
		if ((v6Z==1 && v6NY==1) || (v8Z==1 && v8NY==1)) {
			alert('A v6,v8 szelepek nem mozgathatóak, mert épp köztes állapotban vannak!');
			return;
		}
		keziVezSzelep = 8;
		if (v8Z==1) {
			ez.keziUtasitastKuld('p2', 'v6_v8_nyitas', 40);
		} else {
			ez.keziUtasitastKuld('p2', 'v6_v8_zaras', 41);
		}


	};
	
	this.keziVezClick = function() {
		let keziVezPostfix = keziMode ? 'Ki.php' : 'Be.php';
		$.ajax({
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

	this.keziUtasitastKuld = function(receiver, instructionname, instructionid) {
		vezerles = true;
		keziVezIdo = 16;

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

		// console.log('nincs szelepmozgatás..ez csak próba');
	};

	this.logoutClick = function() {
		vezerles = true;
		
		$.ajax({
			// url: 'http://'+ez.serverUrl+'/login/kilepes.php'
			url: '../login/kilepes.php'
			, success: function(result) {
				vezerles = false;
				// window.location.assign('http://'+ez.serverUrl+'/login.php');
				window.location.assign('../login.php?d=2');
			}
			, error: function (xhr, ajaxOptions, thrownError) {
				vezerles = false;
				frissites = false;
				console.log('kilepes.php hiba');
			}
		});		
	};

	this.dashbClick = function() {
		vezerles = true;
		window.location.assign('../vezerlo.php');
	};
	
	

}