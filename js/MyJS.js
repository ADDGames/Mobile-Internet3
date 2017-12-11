/*$(document).ready(function () {
	$("#signInbutton").click(function () {
			var inputdata = {
				function: 'login',
				table: 'gebruiker',
				username: $("#login-gebruikersnaam").val(),
				wachtwoord: $("#login-wachtwoord").val(),
			};
			alert(inputdata);
			$.post({
					url: "users.php",
					data: inputdata,
					success: function (response) {
						alert(response);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(jqXHR);
						alert(textStatus);
						alert(errorThrown));
				}
			});
	});
});*/