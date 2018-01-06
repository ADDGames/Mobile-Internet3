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
// Callbacks to run specific code for specific pages, for example for About page:
myApp.onPageInit('about', function (page) {
	// run createContentPage func after link was clicked
	$$('.create-page').on('click', function () {
		createContentPage();
	});
});
// Generate dynamic page
var dynamicPageIndex = 0;

function createContentPage() {
	mainView.router.loadContent('<!-- Top Navbar-->' + '<div class="navbar">' + '  <div class="navbar-inner">' + '    <div class="left"><a href="#" class="back link"><i class="icon icon-back"></i><span>Back</span></a></div>' + '    <div class="center sliding">Dynamic Page ' + (++dynamicPageIndex) + '</div>' + '  </div>' + '</div>' + '<div class="pages">' + '  <!-- Page, data-page contains page name-->' + '  <div data-page="dynamic-pages" class="page">' + '    <!-- Scrollable page content-->' + '    <div class="page-content">' + '      <div class="content-block">' + '        <div class="content-block-inner">' + '          <p>Here is a dynamic page created on ' + new Date() + ' !</p>' + '          <p>Go <a href="#" class="back">back</a> or go to <a href="services.html">Services</a>.</p>' + '        </div>' + '      </div>' + '    </div>' + '  </div>' + '</div>');
	return;
}
/*
function vakkenladen() {
        var input = {
					table = 'vak'
				};
				$.post({
					url: "php/vak.php",
					data: inputdata,
					success: function (response) {
						response = JSON.parse(response);
						if(response.status === "fail") {
							alert(response.error);
						} else {
							alert('ok');
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(jqXHR);
						alert(textStatus);
						alert(errorThrown);
					}
				});
}
*/
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
		url: "php/vakken.php",
		data: inputdata,
		success: function (response) {
			response = JSON.parse(response);
			if(response.status === "fail") {
				alert(response.error);
			} else {
				$.each(response.data, function (index) {
					$('#lijstvakkenstudent').append("<li> <a href='STU_Vak.html' id='" + response.data[index].values.VAK_id + "' class='item-link item-content'> <div class='item-media'><i class='icon f7-icons' width='100'>collection</i></div><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.VAK_naam + "</div><div class='item-after'>Online</div></div><div class='item-subtitle'>" + response.data[index].values.Docent_naam + "</div><div class='item-text'></div></div></a></li>");
				});
			}
		}
	});
});
myApp.onPageInit('DOC_vakken', function (page) {
	var inputdata = {
		function: 'getallfromdocent',
		table: 'vakdocent',
		id: gegevens.user.docentid
	}
	$.post({
		url: "php/vakken.php",
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
						url: "php/vakken.php",
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
		url: "php/users.php",
		data: inputdata,
		success: function (response) {
			response = JSON.parse(response);
			if(response.status === "fail") {
				alert(response.error);
			} else {
				if(response.data.type === "student") {
					gegevens.user.userid = response.data.GEB_id;
					gegevens.user.username = response.data.username;
					gegevens.user.type = response.data.type;
					var inputdata = {
						function: 'getone',
						table: 'student',
						id: gegevens.user.userid
					}
					$.post({
						url: "php/users.php",
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
					gegevens.user.userid = response.data.GEB_id;
					gegevens.user.username = response.data.username;
					gegevens.user.type = response.data.type;
					var inputdata = {
						function: 'getone',
						table: 'docent',
						id: gegevens.user.userid
					}
					$.post({
						url: "php/users.php",
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
	if($("#signup-wachtwoord1").val() = $("#signup-wachtwoord2").val()) {
		var inputdata = {
			function: 'add',
			voornaam: $("#signup-naam").val(),
			naam: $("#signup-achternaam").val(),
			username: $("#signup-gebruikersnaam").val(),
			email: $("#signup-email").val(),
			wachtwoord: $("#signup-wachtwoord1").val()
		};
		if($$("#signupLeerkracht").prop('disabled')) {
			inputdata['table'] = 'student';
		} else {
			inputdata['table'] = 'docent';
			inputdata['code'] = $("#LeerkrachtCode").val();
		}
		$.post({
			url: "php/users.php",
			data: inputdata,
			success: function (response) {
				response = JSON.parse(response);
				if(response.status === "fail") {
					alert(response.error);
				} else {
					alert('ok');
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