// Initialize your app
var myApp = new Framework7();
// Export selectors engine
var $$ = Dom7;
// Add view
var mainView = myApp.addView('.view-main', {
	// Because we use fixed-through navbar we can enable dynamic navbar
	dynamicNavbar: true
});
var gegevens = [];
gegevens.user = [];
gegevens.vak = [];
gegevens.studenten = [];
myApp.onPageInit('Profile', function (page) {
	$$('div#ProfileVolledigenaam').text(gegevens.user.username + " " + gegevens.user.voornaam);
	$$('div#Profileusername').text(gegevens.user.username);
	$$('div#ProfileWachtwoord').text(gegevens.user.wachtwoord);
	$$('div#ProfileEmail').text(gegevens.user.email);
});
myApp.onPageInit('STU_vakken', function (page) {
	var inputdata = {
		function: 'getallfromstudent',
		table: 'vakstudent',
		id: gegevens.user.studentid
	}
	$.post({
		url: "http://mobile3.atwebpages.com/php/vakken.php",
		data: inputdata,
		success: function (response) {
			response = JSON.parse(response);
			if(response.status === "fail") {
				alert(response.error);
			} else {
				$.each(response.data, function (index) {
					$('#lijstvakkenstudent').append("<li class='doorklikkenSTU' id='" + response.data[index].values.VAK_id + "'> <a href='STU_Vak.html' class='item-link item-content'> <div class='item-media'><i class='icon f7-icons' width='100'>collection</i></div><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.VAK_naam + "</div><div class='item-after'>Online</div></div><div class='item-subtitle'>" + response.data[index].values.Docent_naam + "</div><div class='item-text'></div></div></a></li>");
				});
				$$(".doorklikkenSTU").on('click', function () {
					gegevens.vak.id = $(this).attr('id');
					var inputdata = {
						function: 'getallallfromVak',
						table: 'vakstudent',
						id: gegevens.vak.id
					}
					$.post({
						url: "http://mobile3.atwebpages.com/php/vakken.php",
						data: inputdata,
						success: function (response) {
							response = JSON.parse(response);
							if(response.status === "fail") {
								alert(response.error);
							} else {
								$.each(response.data, function (index) {
									$('#TitelVakStudent').append("<p id='" + response.data[index].values.VAK_id + "'>VAK" + " " + response.data[index].values.VAK_naam + " - " + "Leerkracht" + " " + response.data[index].values.Docent_naam + "</p>");
								});
							}
						}
					});
				});
			}
		}
	});
});
myApp.onPageInit('DOC_vakken', function (page) {
	function init() {
		var inputdata = {
			function: 'getallfromdocent',
			table: 'vakdocent',
			id: gegevens.user.docentid
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/vakken.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$.each(response.data, function (index) {
						$('#lijstvakkendocent').append("<li class='doorklikken' id='" + response.data[index].values.VAK_id + "'> <a href='DOC_Vak.html' class='item-link item-content'> <div class='item-media'><i class='icon f7-icons' width='100'>collection</i></div><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.VAK_naam + "</div><div class='item-after'>Online</div></div><div class='item-subtitle'></div><div class='item-text'></div></div></a></li>");
					});
					$$(".doorklikken").on('click', function () {
						gegevens.vak.id = $(this).attr('id');
						var inputdata = {
							function: 'getallstudentenfromvak',
							table: 'vakstudent',
							id: gegevens.vak.id
						}
						$.post({
							url: "http://mobile3.atwebpages.com/php/vakken.php",
							data: inputdata,
							success: function (response) {
								response = JSON.parse(response);
								if(response.status === "fail") {
									alert(response.error);
								} else {
									$.each(response.data, function (index) {
										$('#lijstleerlingen').append("<li><div class='item-content'><div class='item-inner'><div class='item-title'>" + response.data[index].values.Naam + " " + response.data[index].values.Voornaam + "</div></div></div></li>");
									});
								}
							}
						});
					});
				}
			}
		});
	}
	init();
	$$("#addklasbtn").on('click', function () {
		var inputdata = {
			function: 'addvak',
			table: 'vak',
			DOC_id: gegevens.user.docentid,
			VAK_naam: $("#nieuweklastxt").val()
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/addvak.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$('#lijstvakkendocent').empty();
					init();
					myApp.closeModal(".demo-popover");
				}
			}
		});
	});
});
myApp.onPageInit('STU_vak', function (page) {
	function init() {
		var inputdata = {
			function: 'getallforvak',
			table: 'vakstudent',
			id: gegevens.user.studentid,
			vakid: gegevens.vak.id
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/vak.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$.each(response.data, function (index) {
						$('#lijstsessiestudent').append("<li id='" + response.data[index].values.SES_id + "'><a href='STU_Sessie.html' class='item-link item-content'><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.SES_naam + "</div><div class='item-after'>" + response.data[index].values.SES_actief + "</div></div><div class='item-subtitle'>Kort info?</div><div class='item-text'>Gesloten op: " + response.data[index].values.SES_eindtijd + "</div></div></a></li>");
					});
				}
			}
		});
	}
	init();
	$("#codesessiebtn").on("click", function () {
		var inputdata = {
			function: 'add',
			table: 'sessiestudent',
			studentid: gegevens.user.studentid,
			vakid: gegevens.vak.id,
			code: $("#codesessie").val()
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/addsessiestudent.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$('#lijstsessiestudent').empty();
					init();
				}
			}
		});
	})
});
myApp.onPageInit('DOC_vak', function (page) {
	function init() {
		var inputdata = {
			function: 'getallforvak',
			table: 'vakdocent',
			id: gegevens.vak.id
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/vak.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$.each(response.data, function (index) {
						if(response.data[index].values.SES_actief === 1) {
							response.data[index].values.SES_actief === Online;
						}
						$('#lijstsessiedocent').append("<li id='" + response.data[index].values.SES_id + "' class='doorklikkenDOC'><a id='" + response.data[index].values.SES_naam + "' href='DOC_Sessie.html' class='item-link item-content'><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.SES_naam + "</div><div class='item-after'>" + response.data[index].values.SES_actief + "</div></div><div class='item-subtitle'>CODE: " + response.data[index].values.SES_code + "</div><div class='item-text'>Gesloten op: " + response.data[index].values.SES_eindtijd + "</div></div></a></li>");
					});
					$$(".doorklikkenDOC").on('click', function () {
						gegevens.vak.sessieid = $(this).attr('id');
						gegevens.vak.sessienaam = $(this).children("a").attr('id');
						var inputdata = {
							function: 'getallstudentenfromvak',
							table: 'vakstudent',
							id: gegevens.vak.sessieid
						}
						$.post({
							url: "http://mobile3.atwebpages.com/php/vakken.php",
							data: inputdata,
							success: function (response) {
								response = JSON.parse(response);
								if(response.status === "fail") {
									alert(response.error);
								} else {
									$.each(response.data, function (index) {
										$('#lijstleerlingen').append("<li><div class='item-content'><div class='item-inner'><div class='item-title'>" + response.data[index].values.Naam + " " + response.data[index].values.Voornaam + "</div></div></div></li>");
									});
								}
							}
						});
					});
				}
			}
		});
	}
	init();
	$$('#addleerlingbtn').on('click', function () {
		var inputdata = {
			function: 'getall',
			table: 'student'
		};
		$.post({
			url: "http://mobile3.atwebpages.com/php/users.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					gegevens.studenten = response.data;
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR);
				alert(textStatus);
				alert(errorThrown);
			}
		});
	});
	$$('#voegsessietoebtn').on('click', function () {
		var inputdata = {
			function: 'add',
			table: 'sessie',
			SES_naam: $("#sessienaam").val(),
			VAK_id: gegevens.vak.id
		};
		$.post({
			url: "http://mobile3.atwebpages.com/php/addsessie.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					$("#lijstsessiedocent").empty();
					init();
					myApp.closeModal("#sessiepop");
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR);
				alert(textStatus);
				alert(errorThrown);
			}
		});
	});
});
$$('.form-to-data').on('click', function () {
	var formData = myApp.formToData('#vraag_form');
	alert(JSON.stringify(formData));
});
var boolLeerkracht = false;
$$("#signupLeerkracht").on("change", function () {
	if($$("#LeerkrachtCode").prop('disabled')) {
		$$("#LeerkrachtCode").prop('disabled', false);
		boolLeerkracht = true;
	} else {
		$$("#LeerkrachtCode").prop('disabled', true);
		$$("#LeerkrachtCode").val('');
		boolLeerkracht = false;
	}
});
$('#signInbutton').on('click', function () {
	var inputdata = {
		function: 'login',
		table: 'gebruiker',
		username: $("#login-gebruikersnaam").val(),
		wachtwoord: $("#login-wachtwoord").val()
	};
	$.post({
		url: "http://mobile3.atwebpages.com/php/users.php",
		data: inputdata,
		success: function (response) {
			response = JSON.parse(response);
			if(response.status === "fail") {
				alert(response.error);
			} else {
				gegevens.user.userid = response.data.GEB_id;
				gegevens.user.username = response.data.username;
				gegevens.user.type = response.data.type;
				if(response.data.type === "student") {
					var inputdata = {
						function: 'getone',
						table: 'student',
						id: gegevens.user.userid
					}
					$.post({
						url: "http://mobile3.atwebpages.com/php/users.php",
						data: inputdata,
						success: function (response) {
							response = JSON.parse(response);
							if(response.status === "fail") {
								alert(response.error);
							} else {
								gegevens.user.studentid = response.data.STU_id;
								gegevens.user.naam = response.data.GEB_naam;
								gegevens.user.voornaam = response.data.GEB_voornaam;
								gegevens.user.wachtwoord = response.data.GEB_wachtwoord;
								gegevens.user.email = response.data.GEB_email;
								mainView.router.load({
									url: "STU_Vakken.html"
								});
								myApp.closeModal();
							}
						}
					});
				} else if(response.data.type === "docent") {
					var inputdata = {
						function: 'getone',
						table: 'docent',
						id: gegevens.user.userid
					}
					$.post({
						url: "http://mobile3.atwebpages.com/php/users.php",
						data: inputdata,
						success: function (response) {
							response = JSON.parse(response);
							if(response.status === "fail") {
								alert(response.error);
							} else {
								gegevens.user.docentid = response.data.DOC_id;
								gegevens.user.naam = response.data.GEB_naam;
								gegevens.user.voornaam = response.data.GEB_voornaam;
								gegevens.user.wachtwoord = response.data.GEB_wachtwoord;
								gegevens.user.email = response.data.GEB_email;
								mainView.router.load({
									url: "DOC_Vakken.html"
								});
								myApp.closeModal();
							}
						}
					});
				}
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(jqXHR);
			alert(textStatus);
			alert(errorThrown);
		}
	});
});
$$('#registreerbutton').on('click', function () {
	if($("#signup-wachtwoord1").val() == $("#signup-wachtwoord2").val()) {
		var inputdata = {
			function: 'add',
			voornaam: $("#signup-naam").val(),
			naam: $("#signup-achternaam").val(),
			username: $("#signup-gebruikersnaam").val(),
			email: $("#signup-email").val(),
			wachtwoord: $("#signup-wachtwoord1").val()
		};
		if($("#LeerkrachtCode").prop('disabled')) {
			inputdata['table'] = 'student';
		} else {
			inputdata['table'] = 'docent';
			inputdata['code'] = $("#LeerkrachtCode").val();
		}
		$.post({
			url: "http://mobile3.atwebpages.com/php/users.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					myApp.closeModal("#signup");
					myApp.popup("#login");
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert(jqXHR);
				alert(textStatus);
				alert(errorThrown);
			}
		});
	} else {
		alert('wachtwoord is niet gelijk');
	}
});
$$('#voegleerlingtoe').on('click', function () {
	var inputdata = {
		function: 'nieuweleerling',
		table: 'gebruiker',
		voornaam: $("#addleerlingvoornaam").val(),
		naam: $("#addleerlingnaam").val(),
		vakid: $("").id(),
	};
	$.post({
		url: "http://mobile3.atwebpages.com/php/Addleerling.php",
		data: inputdata,
		success: function (response) {
			response = JSON.parse(response);
			if(response.status === "fail") {
				alert(response.error);
			} else {
				// add rij in vak_student met de id van deze leerling en het id van het vak
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(jqXHR);
			alert(textStatus);
			alert(errorThrown);
		}
	});
});