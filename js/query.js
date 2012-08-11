Query={};
Query.Populating=false;
Query.Loading=true;
Query.JustInitiated=true;
Query.LoadedAllResultsForQuery=false;
Query.SearchKeypressTimer=undefined;

Query.RegisteredFields=[];
Query.Headers=[];

Query.CurrentPage=0;

Query.SortAsc=[];
Query.SortDesc=[];

Query.RegisterField=function(slug,id,queryParam,translator,exclusive){
	var field={};
	field.slug=slug;
	field.id=id;
	field.queryParam=queryParam;
	field.translator=translator;//translator lets you make a checkbox macro a certain parameter or whatevs
	field.exclusive=exclusive;
	//Query.RegisteredFields.push(field);
	Query.RegisteredFields[field.slug]=field;
};

Query.RegisterHeader=function(display,queryParam,firstStep){
	var header={};
	header.display=display;
	header.queryParam=queryParam;
	header.firstStep=firstStep;
	Query.Headers.push(header);
};



Query.Init=function(){
	for (var qid in Query.RegisteredFields){
		var q=Query.RegisteredFields[qid];
		q.field=$("#"+q.id).get(0);
		if (q.field===undefined) continue;
		q.field.field=q;//ikr?
		
		//hook change events
		if ($(q.field).attr("type")=="text"){
			$(q.field).keyup(function(){
				var me=this;
				if (this.lastval!=$(this).val()){
					
					if (Query.SearchKeypressTimer!==undefined){
						clearTimeout(Query.SearchKeypressTimer);
						Query.SearchKeypressTimer=undefined;
					}
					Query.SearchKeypressTimer=setTimeout(function(){Query.FieldChanged.call(me);},250);
					this.lastval=$(this).val();
				}
			});
		} else {
			if (q.field.tagName=="RADIOGROUP"){
				DisplayUtils.SetupFauxRadiobutton(q.field);
			}
			$(q.field).change(Query.FieldChanged);

			
		}

		var clearShortcut=$("<div class=\"clearShortcut\">").text("X").attr("title","Clear this field").hide();
		clearShortcut.click(Query.ClearShortcut);
		$(q.field).after(clearShortcut);

		Query.UpdateSelectStyling(q.field);
	}

	//init table headers
	for (var i = 0 ; i < Query.Headers.length; i++) {//this is a wtf template btw, sublimetext2
		var h=Query.Headers[i];
		var display=h.display;
		var sortIcon=$("<div class=\"sortIcon\" />");
		sortIcon.get(0).state=0;
		sortIcon.get(0).header=h;
		sortIcon.click(Query.UISort);

		var header=$("<th>").text(display).append(sortIcon);
		$("#resultsHeaders").append(header);
		header.click(function(){
			$(".sortIcon",this).click();
		});
		header.attr("title","Cycle sorting of this column").css("cursor","pointer");

	}

	//endless scroll
	$(document).scroll(function() {
		
		if ($(document).height() - ($(document).scrollTop()+$(window).height()) < 1000) {
			if (Query.LoadedAllResultsForQuery || Query.Loading) return;
			Query.CurrentPage++;
			Query.Query();
		}
	});
	$.address.change(Query.BuildFromAddress);

	Query.SetupRegionFilter();
	Query.SetupDivisionFilter();


	
};
$(document).ready(Query.Init);

Query.ShowHideClearShortcut=function(element){
	if ($(element).val()!==undefined && $(element).val()!=="" && $(element).attr("type")!="text"){
		$(".clearShortcut",element.parentNode).show().css("display","inline-block");
	} else {
		$(".clearShortcut",element.parentNode).hide();
	}
};

Query.UpdateSelectStyling=function(element){
	if ($(element)[0].tagName=="SELECT"){
		if ($(element).val()===""){
			$(element).addClass("inactive");
		} else {
			$(element).removeClass("inactive");
		}
	}
};

Query.FieldChanged=function(){
	Query.ShowHideClearShortcut(this);
	Query.UpdateSelectStyling(this);
	if (this.field.exclusive){
		//unset all other fields that specify this query value
		for (var qid in Query.RegisteredFields){
			var q=Query.RegisteredFields[qid];
			if (q.field!=this && q.queryParam==this.field.queryParam){
				$(q.field).val("");
			}
		}
	}
	//trigger rebuild & reload
	Query.Build();

};

Query.Build=function(){
	//place the params into the address, then let the event handler actually do the loading so I can not duplicate stuff everywhere
	
	for (var qid in Query.RegisteredFields){
		var q=Query.RegisteredFields[qid];
		var val=$(q.field).val();
		q.value=val;
		$.address.parameter(q.slug,val.replace(/\s/g,"%20"));//spaces=don't play well
	}
};

Query.BuildFromAddress=function(){
	
	var inSearch=false;
	var values={};
	for (var qid in Query.RegisteredFields){
		var q=Query.RegisteredFields[qid];
		var val=$.address.parameter(q.slug);
		if (val!==undefined) val=val.replace(/%20/g," ");
		if ($(q.field).val()!=val) {$(q.field).val(val);}
		Query.ShowHideClearShortcut(q.field);
		if (typeof q.translator!="undefined" && q.translator!==null){
			val=q.translator(val);
		}
		if (val!=="" && val!==undefined) inSearch=true;
		if (typeof values[q.queryParam]=="undefined") {
			values[q.queryParam]=val;
		} else {
			values[q.queryParam]=values[q.queryParam]+"&&"+val;
		}

		
	}

	if (inSearch){
		if (Query.JustInitiated){
			$("#introduction").hide();
		} else {
			$("#introduction").animate({marginTop:-$("#introduction").outerHeight()},500,function(){$("#introduction").hide();});
		 }
	}
	if (Query.JustInitiated) {
		Query.JustInitiated=false;
		Query.FilterRegions();
		Query.FilterDivisions();
	}
	values.mode=Query.Mode;
	values.sortasc=$.address.parameter("sortasc");
	values.sortdesc=$.address.parameter("sortdesc");

	//set UI to match
	$(".sortIcon").each(function(){
		if (values.sortasc!==undefined && values.sortasc.split(',').indexOf(this.header.queryParam)!=-1){
			$(this).removeClass("desc").addClass("asc");
			this.state=1;
		} else if (values.sortdesc!==undefined && values.sortdesc.split(',').indexOf(this.header.queryParam)!=-1) {
			$(this).removeClass("asc").addClass("desc");
			this.state=2;
		} else {
			$(this).removeClass("asc desc");
			this.state=0;
		}
	});

	Query.CurrentValues=values;
	Query.CurrentPage=0;
	Query.LoadedAllResultsForQuery=false;
	Query.Query();
	
	
};

Query.Query=function(page){
	if (Query.LoadedAllResultsForQuery) return;
	Query.CurrentValues.page=Query.CurrentPage;
	$.post("query.php",Query.CurrentValues,Query.QueryCallback,"json");
	Query.Loading=true;
	$("#resultCt").hide();
	$("#footer #loading").show();
};

Query.CreateRefineButton=function(fieldslug,fvalue){
	
	var ddBtn=$("<div class=\"refineButton\" title=\"Refine to this value\">R</div>");
	ddBtn.get(0).fieldSlug=fieldslug;
	ddBtn.get(0).fieldValue=fvalue;
	$(ddBtn).click(Query.RefineButton);
	
	if (Query.RegisteredFields[fieldslug]!==undefined && $(Query.RegisteredFields[fieldslug].field).val()==fvalue) {
		ddBtn.hide();
	}
	return ddBtn;
};

Query.CreateInsightButton=function(endpoint,parameter,diagCallback){
	var ddBtn=$("<div class=\"insightButton\" title=\"More details\">D</div>");
	ddBtn.get(0).endpoint=endpoint;
	ddBtn.get(0).parameter=parameter;
	ddBtn.get(0).diagCallback=diagCallback;
	$(ddBtn).click(Query.InsightButton);
	return ddBtn;
};

Query.InsightButton=function(){
	Insight.Show($(this).get(0).endpoint,$(this).get(0).parameter);
};

Query.QueryCallback=function(data){
	//clear old table contents
	if (Query.CurrentPage===0) $("#resultsTableBody tr,#resultsContainer div").remove();
	if (data.rows.length!=50) {Query.LoadedAllResultsForQuery=true;}//50=page length on server side

	if (data.rows.length!==0) {
		Query.AppendResultsToTable(data.rows);
		$("#resultsTable div.truncContainer").width($("#resultsTable").width());
	}

	$("#resultCt").text(data.totalCount+" results found");//in "+data.queryTime+"ms"
	Query.Loading=false;
	$("#footer #loading").hide();
	$("#resultCt").show();
	if (Query.LoadedAllResultsForQuery && $("#endMark").length===0){
		Query.CreateEndmark();
	}
};

Query.CreateEndmark=function(){
	var endmarkElement=$("<div id=\"endMark\"><img src=\"img/endmark.png\" title=\"No more results\"></div>");
	$("#resultsContainer").append(endmarkElement);
};

Query.ClearShortcut=function(){
	$(this).prev().val(null);
	$(this).prev().triggerHandler("change");
	$(this).prev().triggerHandler("keyup");
};

Query.RefineButton=function(e){
	StopPropagation(e);//for cell expansion
	for (var qid in Query.RegisteredFields){
		var q=Query.RegisteredFields[qid];
		if (q.slug==this.fieldSlug){

			$(q.field).val(this.fieldValue);
			Query.Build();
			break;
		}
	}
	
};

Query.UISort=function(e){
	StopPropagation(e);
	//states: 0=none, 1=asc, 2=desc
	if (this.state==0){
		if (this.header.firstStep=="asc") {
			this.state=1;
		} else {
			this.state=2;
		}
	} else if (this.state==1 && this.header.firstStep=="asc"){
		this.state=2;
	} else if (this.state==1) {
		this.state=0;
	} else if (this.state==2 && this.header.firstStep=="asc"){
		this.state=0;
	} else {
		this.state=1;
	}
	
	if (this.state==0){
		$(this).removeClass("asc desc");
	} else if (this.state==1){
		$(this).removeClass("desc").addClass("asc");
	} else {
		$(this).removeClass("asc").addClass("desc");
	}
	Query.DoSort();
};

Query.DoSort=function(){
	//Query.SortAsc=[];
	//Query.SortDesc=[];
	
	//again, load this into the URL first
	var Acols=[];
	$(".sortIcon.asc").each(function(){
		Acols.push(this.header.queryParam);
	});
	var Dcols=[];
	$(".sortIcon.desc").each(function(){
		Dcols.push(this.header.queryParam);
	});

	$.address.parameter("sortasc",Acols);
	$.address.parameter("sortdesc",Dcols);
	
	Query.BuildFromAddress();
};

//region filtering code
var Regions=[];
Query.SetupRegionFilter=function(){
	$("#provSelect").change(Query.FilterRegions);
	$("#regionSelect option").each(function(){
		var reg={};
		reg.display=$(this).text();
		if (reg.display=="Any") return;
		reg.value=$(this).attr("value");
		reg.province=$(this).attr("province");
		Regions.push(reg);
	});
};

Query.FilterRegions=function(){

	var prevValue=$("#regionSelect").val();
	
	var curProv=$("#provSelect").val();
	$("#regionSelect option:not(:first)").remove();
	var persistValue=false;
	for (var i = 0; i < Regions.length; i++) {
		if (Regions[i].province==curProv || curProv===""){
			$("<option>").text(Regions[i].display).attr("value",Regions[i].value).appendTo("#regionSelect");
			if (Regions[i].value==prevValue){
				persistValue=true;
			}
		}
	}
	if (persistValue){
		$("#regionSelect").val(prevValue);

	} else {
		$("#regionSelect").val("");
		Query.Build();
	}


};

//division filtering code, which I copied outright from the region stuff
var Divisions=[];
Query.SetupDivisionFilter=function(){

	$("#yearSearch").change(Query.FilterDivisions);
	$("#divisionSearch option").each(function(){
		var div={};
		div.display=$(this).text();
		if (div.display=="Any") return;
		div.value=$(this).attr("value");
		div.start=parseInt($(this).attr("startyear"),10);
		div.end=parseInt($(this).attr("endyear"),10);
		Divisions.push(div);
	});
};

Query.FilterDivisions=function(){

	var prevValue=$("#divisionSearch").val();//just realized I changed from XYZselect to XYZsearch somewhere along the way :\
	
	var curProv=parseInt($("#yearSearch").val(),10);
	console.log(curProv);
	$("#divisionSearch option:not(:first)").remove();
	var persistValue=false;
	for (var i = 0; i < Divisions.length; i++) {
		if ((Divisions[i].start<=curProv && Divisions[i].end>=curProv) || isNaN(curProv)){
			$("<option>").text(Divisions[i].display).attr("value",Divisions[i].value).appendTo("#divisionSearch");
			if (Divisions[i].value==prevValue){
				persistValue=true;
			}
		}
	}
	if (persistValue){
		$("#divisionSearch").val(prevValue);

	} else {
		$("#divisionSearch").val("");
		Query.Build();
	}
};