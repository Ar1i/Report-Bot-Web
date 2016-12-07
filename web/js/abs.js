function ad_block_test(callback, testad_id) {
	if(typeof document.body == 'undefined') {
		// right now just silently fail if the body element isn't there
		return;
	}
	var version = "0.1.2-dev";
	var testad_id = testad_id ? testad_id : "sponsorText";
	var testad = document.createElement("DIV");
	testad.id = testad_id;
	testad.style.position = "absolute";
	testad.style.left = "-999px";
	testad.appendChild(document.createTextNode("&nbsp;"));
	document.body.appendChild(testad); // add test ad to body

	// wait a bit and then check its height
	setTimeout(function() {
		if (testad) {
			var blocked = (testad.clientHeight == 0);
			try {
				// AdBlock Plus or AdBlock Edge in FFox uses -moz-binding property to hide elements
				//  They also usually collapse the element, depending on settings. Value looks like:
				//  url("about:abp-elemhidehit?668798490716#dummy")
				// blocked = blocked || (getComputedStyle(testad).getPropertyValue('-moz-binding').indexOf('about:') !== -1);
			} catch (err) {
				// log errors
				if(console && console.log) { console.log("ad-block-test error",err); }
			}
			callback(blocked, version);
			document.body.removeChild(testad);
			// Should testad disappearing entirely count as an ad block?
			// Currently it does not fire callback at all in this case
		}
	}, 175);
}