Insight={};
Insight.Width=800;//magic # w/ CSS

Insight.Show=function(provider,parameter){
	Insight.CreateDialog();
	if (provider=="local"){
		Insight.ShowCallback(parameter);
	} else {
		$.get(Query.Platform.APIBase()+"insight/"+provider+"/"+parameter,Insight.ShowCallback);	
	}
};

Insight.CreateDialog=function(){
	$("<div class=\"insightCover\">").appendTo($("body")).hide().fadeIn(200).click(Insight.Close);

	$("<div class=\"insightDiag\">").appendTo($("body")).hide().fadeIn(200).append($("<div class=\"insightLoading\">").text("Loading..."));
	$(".insightDiag").css("top",$(window).scrollTop()+$(window).height()*0.1);

};

Insight.Close=function(){
	$(".insightCover, .insightDiag").remove();
};

Insight.ShowCallback=function(content){
	$(".insightDiag").html(content);
};
