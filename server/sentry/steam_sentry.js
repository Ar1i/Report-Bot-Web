// Credits to @sstokic-tgm for Sentry fix
var username;
var password;
var steamCode;

var Steam = require("steam");
var fs = require("fs");
var readline = require("readline");

var steam = new Steam.SteamClient();

var rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

rl.question("Username: ", function(answer) {
    username = answer;
    rl.question("Password: ", function(answer2) {
        password = answer2;
        rl.pause();
        steam.logOn({
            accountName: username,
            password: password
        });
    });
});

steam.on("loggedOn", function(result) {
    console.log("Logged in");
    steam.setPersonaState(Steam.EPersonaState.Online);
    setTimeout (function() {
        process.exit();
    }, 10000);
});

steam.on("error", function(error) {
    if (error.cause == "logonFail") {
		if (error.eresult == 63) {
            rl.resume();
            rl.question("Steam guard code: ", function(answer) {
                steamCode = answer;
                rl.close();
                steam.logOn({
                    accountName: username,
                    password: password,
                    authCode: steamCode
                });
            });
        } else {
            console.log("Logon fail: " + error.eresult);
        };
    };
});

steam.on('sentry', function(data) {
    var format = username + ".sentry";
    fs.writeFileSync(format, data);
    console.log("Sentry file successfully saved!");
});
