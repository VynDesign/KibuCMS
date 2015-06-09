
			sfHover = function() {
					var sfEls = document.getElementById("dropnav").getElementsByTagName("li");
					for (var i=0; i<sfEls.length; i++) {
					sfEls[i].onmouseover=function() {
						this.className+=" ieFix"
					}
					sfEls[i].onmouseout=function() {
						this.className=this.className.replace(new RegExp(" ieFix\\b"), "");
					}
				}
			}
			if (window.attachEvent) window.attachEvent("onload", sfHover);