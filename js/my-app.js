// Initialize your app
var myApp = new Framework7();
// Export selectors engine
var $$ = Dom7;
// Add view
var mainView = myApp.addView('.view-main', {
	// Because we use fixed-through navbar we can enable dynamic navbar
	dynamicNavbar: true
});
var user = [];
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
	$$('div#ProfileVolledigenaam').text(user.username + " " + user.voornaam);
	$$('div#Profileusername').text(user.username);
	$$('div#ProfileWachtwoord').text(user.wachtwoord);
	$$('div#ProfileEmail').text(user.email);
});
myApp.onPageInit('STU_vakken', function (page) {
	console.log(user);
	inputdata = {
		function: 'getallfromstudent',
		table: 'vakstudent',
		id: user.studentid
	}
	console.log(inputdata);
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
				console.log(response);
			}
		}
	});
});
myApp.onPageInit('DOC_vakken', function (page) {
	inputdata = {
		function: 'getallfromdocent',
		table: 'vakdocent',
		id: user.docentid
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
					$('#lijstvakkendocent').append("<li> <a href='DOC_Vak.html' id='" + response.data[index].values.VAK_id + "' class='item-link item-content'> <div class='item-media'><i class='icon f7-icons' width='100'>collection</i></div><div class='item-inner'><div class='item-title-row'><div class='item-title'>" + response.data[index].values.VAK_naam + "</div><div class='item-after'>Online</div></div><div class='item-subtitle'></div><div class='item-text'></div></div></a></li>");
				});
				console.log(response);
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
					user.userid = response.data.GEB_id;
					user.username = response.data.username;
					user.type = response.data.type;
					inputdata = {
						function: 'getone',
						table: 'student',
						id: user.userid
					}
					$.post({
						url: "php/users.php",
						data: inputdata,
						success: function (response) {
							response = JSON.parse(response);
							if(response.status === "fail") {
								alert(response.error);
							} else {
								user.studentid = response.data.STU_id;
								user.naam = response.data.GEB_naam;
								user.voornaam = response.data.GEB_voornaam;
								user.wachtwoord = response.data.GEB_wachtwoord;
								user.email = response.data.GEB_email;
								console.log(user);
								mainView.router.load({
									url: "STU_Vakken.html"
								});
								myApp.closeModal();
							}
						}
					});
				} else if(response.data.type === "docent") {
					user.userid = response.data.GEB_id;
					user.username = response.data.username;
					user.type = response.data.type;
					inputdata = {
						function: 'getone',
						table: 'docent',
						id: user.userid
					}
					$.post({
						url: "php/users.php",
						data: inputdata,
						success: function (response) {
							response = JSON.parse(response);
							if(response.status === "fail") {
								alert(response.error);
							} else {
								user.docentid = response.data.DOC_id;
								user.naam = response.data.GEB_naam;
								user.voornaam = response.data.GEB_voornaam;
								user.wachtwoord = response.data.GEB_wachtwoord;
								user.email = response.data.GEB_email;
								console.log(user);
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