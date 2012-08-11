Insight={};
Insight.Width=800;//magic # w/ CSS

Insight.Show=function(provider,parameter){
	$.get("insight/"+provider+"/"+parameter,Insight.ShowCallback);
	Insight.CreateDialog();
};

Insight.CreateDialog=function(){
	$("<div class=\"insightCover\">").appendTo($("body")).hide().fadeIn(200).click(Insight.Close);

	$("<div class=\"insightDiag\">").appendTo($("body")).hide().fadeIn(200).append($("<div class=\"insightLoading\">").text("Loading..."));

};

Insight.Close=function(){
	$(".insightCover, .insightDiag").remove();
};

Insight.ShowCallback=function(content){
	//var sizingDiv=$("<div>").css("width",Insight.Width).html(content).appendTo(document.body);
	//var height=$(sizingDiv).height();
	//$(sizingDiv).remove();
	$(".insightDiag").html(content);
	$(".insightDiag").css("top",$(window).scrollTop()+$(window).height()*0.1);
};
