//Query.Headers=["Year",["Title","Name","asc"],"Finalists","Region","Prov/Terr","Division","Age","$ Cash","$ Scholarships","$ Other","$ Total"];

Query.RegisterHeader("Year","Year","desc");
Query.RegisterHeader("Title","projects.Name","asc");
Query.RegisterHeader("Finalists","FinalistAName","asc");
Query.RegisterHeader("Region","RegionName","asc");
Query.RegisterHeader("Prov/Terr","projects.ProvTerr","asc");
//Query.RegisterHeader("Division","Division","asc");
Query.RegisterHeader("Age","AgeCat","asc");
Query.RegisterHeader("$ Cash","CashAwardsValue","desc");
Query.RegisterHeader("$ Scholarships","ScholarshipAwardsValue","desc");
Query.RegisterHeader("$ Other","OtherAwardsValue","desc");
Query.RegisterHeader("$ Total","TotalAwardsValue","desc");

Query.RegisterField("region","regionSelect","Region");
Query.RegisterField("provterr","provSelect","projects.ProvTerr");
Query.RegisterField("finalists","nameSearch","Finalists");

Query.RegisterField("year","yearSearch","Year");
Query.RegisterField("title","titleSearch","projects.Name");
Query.RegisterField("abstract","synopsisSearch","Synopsis");
//Query.RegisterField("projectnumber","pnSearch","ProjectNumber",function(v){if (v===undefined) return v;return v.substring(0,4);});
Query.RegisterField("age","ageSearch","AgeCat");
Query.RegisterField("division","divisionSearch","Divisions");

Query.RegisterField("awards","awardsSearch","Awards");

function FormatMultiple(one, two,safe){
	if (safe===true){//safe=will always indicate 2 seperate values instead of dropping one
		if (one===null) {one="?";}
		if (two===null) {two="?";}
	} else {
		if (one===null && two===null){
			return "?";
		} else if (one===null || two===null){
			if (one===null) {return two;} else {return one;}
		} else {
			//...
		}
	}
	return one+" & "+two;
}

Query.AppendResultsToTable=function(results){
	var images=[];
	var alt=false;
	var imgbase=Query.Platform.APIBase();
	var resbase=Query.Platform.ResourceBase();
	for (var i = 0; i < results.length; i++) {
		var res=results[i];
		/*var row=$("<tr>").appendTo($("#resultsTableBody"));
		Query.AppendBasicCell(row,res.Year);
		Query.AppendBasicCell(row,DisplayUtils.HighlightTerms(res.Name,"Name"),true).css("font-weight","bold");
		Query.AppendBasicCell(row,FormatMultiple(res.FinalistAName,res.FinalistBName));
		
		
		Query.AppendRefineCell(row,res.RegionName,"region",res.Region).addClass("nowrap");
		//Query.AppendRefineCell(row,"<img class=\"provImg\" src=\"img/provterr/"+res.ProvTerr+".gif\" alt=\""+res.ProvTerr+"\"/>","provterr",res.ProvTerr,true).addClass("center");
		Query.AppendRefineCell(row,res.ProvTerr,"provterr",res.ProvTerr,true).addClass("center nowrap");
		Query.AppendRefineCell(row,DisplayUtils.DivisionDisplay(res.Division),"division",res.Division).addClass("nowrap");
		Query.AppendRefineCell(row,DisplayUtils.AgeCatDisplay(res.AgeCat),"agecat",res.AgeCat).addClass("center nowrap");

		Query.AppendBasicCell(row,DisplayUtils.FormatCurrency(res.CashAwardsValue),true).addClass("center");
		Query.AppendBasicCell(row,DisplayUtils.FormatCurrency(res.ScholarshipAwardsValue),true).addClass("center");
		Query.AppendBasicCell(row,DisplayUtils.FormatCurrency(res.OtherAwardsValue),true).addClass("center");

	

		Query.AppendBasicCell(row,DisplayUtils.FormatCurrency(res.TotalAwardsValue),true).addClass("center");

		

		var awards=$.parseJSON(res.AwardsData);
		var awardsRow;
		if (awards.length!==0){
			awardsRow=Query.AppendTruncatedRow(DisplayUtils.AwardsSummary(res.AwardsData),true);
		}

		var synrow=Query.AppendTruncatedRow(res.Synopsis);
		$("td",synrow).addClass("endGroup synopsisText");

		if (alt) {$(row).addClass("alt");$(synrow).addClass("alt");if (awardsRow!==undefined){awardsRow.addClass("alt");}}
		alt=!alt;*/

		var resultHolder=$("<div class=\"result\">").appendTo($("#resultsContainer"));

		var year=$("<div class=\"year\">").css("background-image","url('"+resbase+"img/years/"+res.Year+".png')").attr("title",res.Year).appendTo(resultHolder);

		$("<img src=\""+resbase+"img/blank"+(res.FinalistNames[1]!==null?"_dual":"_single")+".png\" actualsrc=\""+imgbase+"imgcache/"+res.RegID+"_project.jpg\" alt=\"Project Image\">").appendTo(resultHolder);
		images.push(imgbase+"imgcache/"+res.RegID+"_project.jpg");
		var actionButtons=$("<div class=\"actions\">").appendTo(resultHolder);
		$("<a class=\"pdfAction action\" target=\"_new\">").attr("href","https://secure.youthscience.ca/virtualcwsf/projectdetailspdf.php?id="+res.RegID).text("Printable PDF").appendTo(actionButtons);
		$("<a class=\"vcwsfAction action\" target=\"_new\">").attr("href","https://secure.youthscience.ca/virtualcwsf/projectdetails.php?id="+res.RegID).text("View on YSC").appendTo(actionButtons);

		var awards=$.parseJSON(res.AwardsData);
		if (awards.length!==0){
			//1=cash, 2=schol, 3=other
			var cashAwards=[];
			var scholAwards=[];
			var otherAwards=[];

			for (var ai = 0; ai < awards.length; ai++) {
				if (awards[ai].Type==1){
					cashAwards.push(awards[ai]);
				} else if (awards[ai].Type==2){
					scholAwards.push(awards[ai]);
				} else {
					otherAwards.push(awards[ai]);
				}
			}

			var awardsBox=$("<div class=\"awards\">").appendTo(resultHolder).html("<h1>Awards</h1>");
			
			if (cashAwards.length>0){
				$("<div class=\"awardGroup\"></div>").appendTo(awardsBox).html("Cash ("+DisplayUtils.FormatCurrency(res.CashAwardsValue)+")");
				BuildAwardsRows(cashAwards,awardsBox);
			}
			if (scholAwards.length>0){
				$("<div class=\"awardGroup\"></div>").appendTo(awardsBox).html("Scholarships ("+DisplayUtils.FormatCurrency(res.ScholarshipAwardsValue)+")");
				BuildAwardsRows(scholAwards,awardsBox);
			}
			if (otherAwards.length>0){
				$("<div class=\"awardGroup\"></div>").appendTo(awardsBox).html("Other ("+DisplayUtils.FormatCurrency(res.OtherAwardsValue)+")");
				BuildAwardsRows(otherAwards,awardsBox);
			}
			//BuildAwardsRow(awards[0]).appendTo(awardsBox);
			$("<div class=\"awardsTotal\">Total Awards:<span class=\"awardValue\">"+DisplayUtils.FormatCurrency(res.TotalAwardsValue)+"</span></div>").appendTo(awardsBox);

		}



		var header=$("<h1>").html(DisplayUtils.HighlightTerms(res.Name,"projects.Name")).appendTo(resultHolder);

		var participNames=$("<span class=\"finalistNames\">").appendTo(resultHolder);

		for(var pk in res.FinalistNames){
			if (res.FinalistNames[pk]===null) continue;
			$("<span class=\"finalistName\">").text(res.FinalistNames[pk]).appendTo(participNames).append(Query.CreateInsightButton("participant",res.Participants[pk],Query.ParticipantInsightDiag)).append(Query.CreateRefineButton("finalists",res.FinalistNames[pk]));

		}
		if (res.FinalistNames[0]===null){
			$("<span class=\"finalistName\">").text("\u2588\u2588\u2588\u2588\u2588\u2588\u2588\u2588\u2588").attr("title","This finalist's name has been removed").appendTo(participNames);
		}

		if (res.RegionName!==null){
			$("<span class=\"region\">").text(res.RegionName).appendTo(resultHolder).append(Query.CreateRefineButton("region",res.Region));
		}
		$("<br/>").appendTo(resultHolder);
		/*
		for (var di = 0; di < res.Divisions.length; di++) {
			if (res.Divisions[di]===null) continue;
			
			$("<span class=\"division iconed\">").text(res.DivisionNames[di]).appendTo(resultHolder).append(Query.CreateRefineButton("division",res.Divisions[di]));
		}
		$("<span> &mdash; </span>").appendTo(resultHolder);
		*/
		
		$("<span class=\"ageCat\">").text(DisplayUtils.AgeCatDisplay(res.AgeCat)).appendTo(resultHolder).append(Query.CreateRefineButton("age",res.AgeCat));

		$("<p class=\"synopsis\">").html(DisplayUtils.HighlightTerms(res.Synopsis,"Synopsis")).appendTo(resultHolder);
		
		


		$("<div>").css("clear","both").appendTo(resultHolder);
	}
	DisplayUtils.PreloadImages(images);
};

function BuildAwardsRows(awardsDat,container){
	for (var i = 0; i < awardsDat.length; i++) {
		var val=DisplayUtils.FormatCurrency(awardsDat[i].Value);
		
		$("<div class=\"award\">").html(DisplayUtils.HighlightTerms(awardsDat[i].Name,"Awards")).append($("<span class=\"awardValue\">").html(val)).appendTo(container);
	}
	
}
