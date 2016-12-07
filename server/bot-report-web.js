var fs = require("fs"),
    Steam = require("steam"),
    SteamID = require("steamid"),
    IntervalIntArray = {},
    readlineSync = require("readline-sync"),
    Protos = require("./protos/protos.js"),
    CountReports = 0,
    Long = require("long"),
    SteamClients = {},
    SteamUsers = {},
    SteamGCs = {},
    SteamFriends = {},
    process = require("process"),
    steamID = process.argv[2],
    ClientHello = 4006,
    ClientWelcome = 4004;

var accounts = [];

var arrayAccountsTxt = fs.readFileSync("accounts.txt").toString().split("\n");
for (i in arrayAccountsTxt) {
    var accInfo = arrayAccountsTxt[i].toString().trim().split(":");
    var username = accInfo[0];
    var password = accInfo[1];
    accounts[i] = [];
    accounts[i].push({
        username: username,
        password: password
    });
}

var size = 0;
size = arrayAccountsTxt.length;

arrayAccountsTxt.forEach(processSteamReport);

function processSteamReport(element, indexElement, array) {
    if (element != "") {
        var account = element.toString().trim().split(":");
        var account_name = account[0];
        var password = account[1];
        SteamClients[indexElement] = new Steam.SteamClient();
        SteamUsers[indexElement] = new Steam.SteamUser(SteamClients[indexElement]);
        SteamGCs[indexElement] = new Steam.SteamGameCoordinator(SteamClients[indexElement], 730);
        SteamFriends[indexElement] = new Steam.SteamFriends(SteamClients[indexElement]);

        SteamClients[indexElement].connect();
		
		var sentryfile;
		if(fs.existsSync(account_name + '.sentry')) {
			sentryfile = fs.readFileSync(account_name + '.sentry');
        }
		
        SteamClients[indexElement].on("connected", function() {
			if(fs.existsSync(account_name + '.sentry')) {
				SteamUsers[indexElement].logOn({
					account_name: account_name,
					password: password,
					sha_sentryfile: sentryfile
				});
			} else {
				SteamUsers[indexElement].logOn({
					account_name: account_name,
					password: password
				});
			}
        });

        SteamClients[indexElement].on("logOnResponse", function(res) {
            if (res.eresult !== Steam.EResult.OK) {
                if (res.eresult == Steam.EResult.ServiceUnavailable) {
                    console.log("\n[STEAM CLIENT - Login failed - STEAM IS DOWN!");
                    SteamClients[indexElement].disconnect();
                    process.exit();
                } else {
					CountReports++;
					if (CountReports == size)
					{
						console.log("\n\n"+ CountReports + " Reports for this faggot.\nThanks for using this Service!\nCredits for the Script to askwrite & TROLOLO");
						process.exit();
					}
                    console.log("\n[STEAM CLIENT (" + account_name.substring(0, 4) + "**) - Login failed!" + res.eresult);
                    SteamClients[indexElement].disconnect();
                    SteamClients.splice(indexElement, 1);
                    SteamFriends.splice(indexElement, 1);
                    SteamGCs.splice(indexElement, 1);
                    SteamUsers.splice(indexElement, 1);
                    IntervalIntArray.splice(indexElement, 1);
                }
            } else {
                SteamFriends[indexElement].setPersonaState(Steam.EPersonaState.Offline);

                SteamUsers[indexElement].gamesPlayed({
                    games_played: [{
                        game_id: 730
                    }]
                });

                if (SteamGCs[indexElement]) {
                    IntervalIntArray[indexElement] = setInterval(function() {
                        SteamGCs[indexElement].send({
                            msg: ClientHello,
                            proto: {}
                        }, new Protos.CMsgClientHello({}).toBuffer());
                    }, 2000);
					console.log("[GC - " + indexElement + "] Client Hello sent!");
                } else {
                    SteamClients[indexElement].disconnect();
                    SteamClients.splice(indexElement, 1);
                    SteamFriends.splice(indexElement, 1);
                    SteamGCs.splice(indexElement, 1);
                    SteamUsers.splice(indexElement, 1);
                    IntervalIntArray.splice(indexElement, 1);
                }
            }
        });

        SteamClients[indexElement].on("error", function(err) {
			console.log("[STEAM CLIENT - " + indexElement + "] Account is probably ingame! Logged out!\n" + err);
			size = size - 1;
			if (CountReports == size)
			{
				console.log("\n\n"+ CountReports + " Reports for this faggot.\nThanks for using this Service!\nCredits for the Script to askwrite & TROLOLO");
				process.exit();
			}
            SteamClients[indexElement].disconnect();
            SteamClients.splice(indexElement, 1);
            SteamFriends.splice(indexElement, 1);
            SteamGCs.splice(indexElement, 1);
            SteamUsers.splice(indexElement, 1);
            IntervalIntArray.splice(indexElement, 1);
        });

        SteamGCs[indexElement].on("message", function(header, buffer, callback) {
            switch (header.msg) {
                case ClientWelcome:
                    clearInterval(IntervalIntArray[indexElement]);
                    console.log("[GC - " + indexElement + "] Client Welcome received!");
					console.log("[GC - " + indexElement + "] Report request sent!");
					IntervalIntArray[indexElement] = setInterval(function() {
                        sendReport(SteamGCs[indexElement], SteamClients[indexElement], account_name, steamID);
                    }, 2000);
                    break;
                case Protos.ECsgoGCMsg.k_EMsgGCCStrike15_v2_MatchmakingGC2ClientHello:
                    console.log("[GC - " + indexElement + "] MM Client Hello sent!");
					break;
                case Protos.ECsgoGCMsg.k_EMsgGCCStrike15_v2_ClientReportResponse:
                    CountReports++;
					console.log("[GC - (" + CountReports + ")] Report with confirmation ID: " + Protos.CMsgGCCStrike15_v2_ClientReportResponse.decode(buffer).confirmationId.toString() + " sent!");
					if (CountReports == size)
					{
					console.log("\n\n"+ CountReports + " Reports for this faggot.\nThanks for using this Service!\nCredits for the Script to askwrite & TROLOLO");
					}
					SteamClients[indexElement].disconnect();
                    SteamClients.splice(indexElement, 1);
                    SteamFriends.splice(indexElement, 1);
                    SteamGCs.splice(indexElement, 1);
                    SteamUsers.splice(indexElement, 1);
                    IntervalIntArray.splice(indexElement, 1);
                    break;
                default:
                    console.log(header);
                    break;
            }
        });
    }
}

function sendReport(GC, Client, account_name) {
    var account_id = new SteamID(steamID).accountid;
    GC.send({
        msg: Protos.ECsgoGCMsg.k_EMsgGCCStrike15_v2_ClientReportPlayer,
        proto: {}
    }, new Protos.CMsgGCCStrike15_v2_ClientReportPlayer({
        accountId: account_id,
        matchId: 8,
        rptAimbot: 2,
        rptWallhack: 3,
        rptSpeedhack: 4,
        rptTeamharm: 5,
        rptTextabuse: 6,
        rptVoiceabuse: 7
    }).toBuffer());
}

process.on("uncaughtException", function(err) {});

console.log("Reporting SteamID: " + steamID + "\nStarting Accounts...");
