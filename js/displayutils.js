RegExp.escape = function(str)
{
  var specials = /[.*+?|()\[\]{}\\$^]/g; // .*+?|()[]{}\$^
  return str.replace(specials, "\\$&");
};
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

DisplayUtils={};
DisplayUtils.AgeCatDisplay=function(cat){

	if (cat==1) return "Junior";
	if (cat==2) return "Intermediate";
	if (cat==3) return "Senior";
	/*
	if (cat==1) return "Jun.";
	if (cat==2) return "Int.";
	if (cat==3) return "Sen.";*/
};
DisplayUtils.DivisionDisplay=function(div){
	
	if (div=="Physical & Mathematical Sciences") return "Phy. & Math Sci.";
	if (div=="Earth & Environmental Sciences") return "Enviro. Sci.";
	if (div=="Health Sciences") return "Health Sci.";
	if (div=="Life Sciences") return "Life Sci.";
	if (div=="Biotechnology") return "Biotech.";
	if (div=="Engineering & Computing Sciences") return "Eng & Comp. Sci.";
	return div;

};

DisplayUtils.FormatCurrency=function(val){
	if (val===null) return "&ndash;";
	return "$"+addCommas(val);
};

DisplayUtils.AwardsSummary=function(awardsDat){
	if (typeof awardsDat=="string") awardsDat=$.parseJSON(awardsDat);
	var awardTitles=[];
	for (var i = 0; i < awardsDat.length; i++) {
		awardTitles.push("<div class=\"awardIcon\"/>"+DisplayUtils.HighlightTerms(awardsDat[i].Name,"AwardsData"));
	}
	return awardTitles.join("");
};

function StopPropagation(event){
	if (event.stopPropagation) {
		event.stopPropagation();
	} else {
		event.cancelBubble = true;
	}
}


DisplayUtils.HighlightTerms=function(string, queryparam){
	if (Query.CurrentValues[queryparam]===undefined) return string;
	var val=Query.CurrentValues[queryparam].split('&&');
		
	for (var k in val){
		val[k]=RegExp.escape(val[k]);
	}
	string=htmlEscape(string);//since elsewhere I use jQuery's .text method to avoid this issue
	string=string.replace(new RegExp("("+val.join("|")+")","ig"),"<span class=\"searchHighlight\">$1</span>");
	return string;
};

DisplayUtils.PreloadImages=function(imgs){
	DisplayUtils.PreloadImageBlock(imgs, 5, 0);
};
DisplayUtils.PreloadImageBlock=function(allImgs, blockSz, blockIndex){
	$.imgpreload(allImgs.slice(blockIndex, blockSz + blockIndex),{
		each: DisplayUtils.ImagePreloadCompleteSingle,
		all: function(){
			var newBlockSz=Math.min(blockSz, allImgs.length-blockIndex);
			DisplayUtils.PreloadImageBlock(allImgs, newBlockSz, blockIndex+blockSz);
		}
	});
};

DisplayUtils.ImagePreloadCompleteSingle=function(){
	if (!$(this).data('loaded')) return;
	$("img[actualsrc=\""+$(this).attr("src")+"\"]").attr("src",$(this).attr("src"));
};

//jquery haxx
var originalVal = this.originalVal = $.fn.val;
$.fn.val = function(value) {
    if (typeof value == 'undefined') {

        if (this.get(0).tagName=="RADIOGROUP"){
			var val=$("input.active",this.get(0)).attr("radiovalue");
			if (val===undefined) return "";
			return val;
        }
        return originalVal.call(this);
    }
    else {
        if (this.get(0).tagName=="RADIOGROUP"){
			$("input.active",this.get(0)).removeClass("active");
			if (value===undefined || value==="") return;
			$("input[radiovalue="+value+"]").addClass("active");
			return;
        }
        return originalVal.call(this, value);
    }
};

DisplayUtils.SetupFauxRadiobutton=function(group){
	group=$(group);
	$("input",group).click(DisplayUtils.FRBSelect);
};
DisplayUtils.FRBSelect=function(){
	if ($(this.parentNode).val()!=$(this).attr("radiovalue")){


		$("input",this.parentNode).removeClass("active");
		$(this).addClass("active");
		
	} else {
		$("input",this.parentNode).removeClass("active");
	}
	$(this.parentNode).triggerHandler("change");
};