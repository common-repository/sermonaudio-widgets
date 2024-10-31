//This handles when the widget type selection box is changed.
//When the box is changed, we want to update contents on the options page
//to show widget-specific data.

var pluginPath = "";
var imagePath = "";

function widgetSelect(list){
	var value = list.options[list.selectedIndex].value;
	//alert(value);	
	if (value == "newest_sermons" || value == "recommended_picks"){
		widgetDetails(value,{"style":4});
	}
	else if (value == "sermon_browser"){
		widgetDetails(value,{"style":2});
	}
	else{
		widgetDetails(value,{"style":1});
	}
}

function widgetDetails(value,style_json){

	var style = style_json; //JSON.parse(style_json);
	
	var div = document.getElementById("sa_plugin_customize");
	
	var contents = ""; //JSON.stringify(style)
	
	if (value == "sermon_browser"){
		contents += "<P>Rows: <input type='text' name='maxrows' placeholder='30' ";
		if (style["maxrows"] != null && style["maxrows"] != ""){
			contents += "value='" + style["maxrows"] + "'";
		}
		contents += " style='margin-left: 20px;' >";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>"

		contents += "<P/>";
		contents += "<input type='checkbox' name='header' ";
		if (style["header"] == 1 || style["header"] == null){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Header";

		contents += "<br/>";
		contents += "<input type='checkbox' name='logo' ";
		if (style["logo"] == 1 || style["logo"] == null){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Logo";

		contents += "<br/>";
		contents += "<input type='checkbox' name='reference' ";
		if (style["reference"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Bible Reference";

		contents += "<P>";
		contents += "<input type='checkbox' name='hidesort' ";
		if (style["hidesort"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Hide Sorting";

		contents += "<p/><strong>Default Starting View</strong>";
		contents += "<p/>"
		contents += "<input type='radio' name='view' value='0' style='margin-left: 20px;' ";
		if (style == null || style["view"] == null || style["view"] == 0){
			contents += "checked";
		}
		contents += "> All Sermons ";

		contents += "<p/>"
		contents += "<input type='radio' name='view' value='1' style='margin-left: 20px;' ";
		if (style["view"] == 1){
			contents += "checked";
		}
		contents += "> Speaker ";
		contents += "<input type='text' name='speakername'";
		if (style["speakername"] != null){
			contents += " value='" + style["speakername"] + "'";
		}
		contents += "/> <small><em>*Must be exact match</em></small>"

		contents += "<p><input type='radio' name='view' value='2' style='margin-left: 20px;' ";
		if (style["view"] == 2){
			contents += "checked";
		}
		contents += "> Series ";
		contents += "<input type='text' name='seriesname'";
		if (style["seriesname"] != null){
			contents += " value='" + style["seriesname"] + "'";
		}
		contents += "/> <small><em>*Must be exact match</em></small>"

		contents += "<p><input type='radio' name='view' value='3' style='margin-left: 20px;' ";
		if (style["view"] == 3){
			contents += "checked";
		}
		contents += "> Bible ";

		// SERMON BROWSER WIDGET STYLE
		contents += "<p/>";
		contents += "<strong>Widget Style</strong>";
		contents += "<p/>";
		contents += "<input type='radio' name='style' value='1' style='margin-left: 20px;' ";
		if (style["style"] == 1){
			contents += "checked"
		}
		contents += "> style 1";

		contents += "<p/>";
		contents += "<input type='radio' name='style' value='2' style='margin-left: 20px;' ";
		if (style["style"] == 2 || style["style"] == null) //default style
		{
			contents += "checked"
		}
		contents += "> style 2 (modern CSS)";

		contents += "<p/>";
		contents += "<img src='" + imagePath + "sermon_browser.gif' />";
	}
	else if (value == "featured_sermon"){
	
		var dict = {
		
			"none":'None',	
			"audio_book":"Audio Book",
			"bible_study":"Bible Study",
			"camp_meeting":"Camp Meeting",
			"chapel_service":"Chapel Service",
			"children":"Children",
			"conference":"Conference",
			"current_events":"Current Events",
			"debate":"Debate",
			"devotional":"Devotional",
			"funeral_service":"Funeral Service",
			"midweek_service":"Midweek Service",
			"prayer_meeting":"Prayer Meeting",
			"question_answer":"Question & Answer",
			"radio_broadcast":"Radio Broadcast",
			"special_meeting":"Special Meeting",
			"sunday_am":"Sunday - AM",
			"sunday_pm":"Sunday - PM",
			"sunday_afternoon":"Sunday Afternoon",
			"sunday_school":"Sunday School",
			"sunday_service":"Sunday Service",
			"teaching":"Teaching",
			"testimony":"Testimony",
			"tv_broadcast":"TV Broadcast",
			"video_dvd":"Video DVD",
			"wedding":"Wedding",
			"youth":"Youth"
		};
	
		//sermon id
		contents += "Sermon ID: <input type='text' name='sermonid' ";
		if (style["sermonid"] != null && style["sermonid"] != ""){
			contents += "value='" + style["sermonid"] + "'";
		}
		contents += ">";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>"
	
		//flash
		contents += "<p>";
		contents += "<input type='checkbox' name='flash' ";
		if (style["flash"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Integrated Flash Player";
		
		//tiny
		contents = contents + "<br>";
		contents = contents + "<input type='checkbox' name='tiny' ";
		if (style["tiny"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Tiny Mode <i>(Make the embed footprint tiny)</i>";
		
		//minimal
		contents = contents + "<br>";
		contents = contents + "<input type='checkbox' name='minimal' ";
		if (style["minimal"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Minimal <i>(Take up less real estate)</i>";
		
		//newest sermons
		contents = contents + "<br>";
		contents = contents + "<input type='checkbox' name='newest' ";
		if (style["newest"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Newest Sermons <i>(Automatically show newest sermons)</i>";
		
		//Event Type
		contents = contents + "<br>";
		contents += "<select name='eventtype'>";
		for (var key in dict){
			contents += "<option value='" + key + "' ";
			if (style["eventtype"] == key || (key == "none" && style["eventtype"] == null)){
				contents += " selected='true'"
			}
			contents += " >" + dict[key] + "</option>";
		}
		
		
		contents += "</select>";
		contents = contents + " Newest By Event Type <i>(Show newest sermons of this event type)</i>";
	}
	else if (value == "flash"){
		contents += "<img src='" + imagePath + "flash.gif' style='float:left;' />";
		
		contents = contents + "<div style='position: relative; top:120px;'>";
		
		contents = contents + "<input type='checkbox' name='tiny' ";
		if (style["tiny"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Tiny";
		
		contents = contents + "<br>";
		contents = contents + "<input type='checkbox' name='minimal' ";
		if (style["minimal"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Minimal";
		
		contents = contents + "</div>";
	}
	else if (value == "newest_sermons"){
		//TEXT FIELDS
		contents += "Speaker Name: <input type='text' name='speakername' ";
		if (style["speakername"] != null && style["speakername"] != ""){
			contents += "value='" + style["speakername"] + "'";
		}
		contents += " style='margin-left: 10px;'>";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>";
		
		contents += "<P>Series Name: <input type='text' name='seriesname' ";
		if (style["seriesname"] != null && style["seriesname"] != ""){
			contents += "value='" + style["seriesname"] + "'";
		}
		contents += " style='margin-left: 20px;' >";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>"
		
		contents += "<P>Rows: <input type='text' name='maxrows' ";
		if (style["maxrows"] != null && style["maxrows"] != ""){
			contents += "value='" + style["maxrows"] + "'";
		}
		contents += " style='margin-left: 20px;' >";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>"

		//EVENT TYPE
		var dict = {
		
			"none":'None',	
			"audio_book":"Audio Book",
			"bible_study":"Bible Study",
			"camp_meeting":"Camp Meeting",
			"chapel_service":"Chapel Service",
			"children":"Children",
			"conference":"Conference",
			"current_events":"Current Events",
			"debate":"Debate",
			"devotional":"Devotional",
			"funeral_service":"Funeral Service",
			"midweek_service":"Midweek Service",
			"prayer_meeting":"Prayer Meeting",
			"question_answer":"Question & Answer",
			"radio_broadcast":"Radio Broadcast",
			"special_meeting":"Special Meeting",
			"sunday_am":"Sunday - AM",
			"sunday_pm":"Sunday - PM",
			"sunday_afternoon":"Sunday Afternoon",
			"sunday_school":"Sunday School",
			"sunday_service":"Sunday Service",
			"teaching":"Teaching",
			"testimony":"Testimony",
			"tv_broadcast":"TV Broadcast",
			"video_dvd":"Video DVD",
			"wedding":"Wedding",
			"youth":"Youth"
		};
		contents += "<P>";
		contents += "<select name='eventtype'>";
		for (var key in dict){
			contents += "<option value='" + key + "' ";
			if (style["eventtype"] == key || (key == "none" && style["eventtype"] == null)){
				contents += " selected='true'"
			}
			contents += " >" + dict[key] + "</option>";
		}
		
		
		contents += "</select>";
		contents = contents + " Newest By Event Type <i>(Show newest sermons of this event type)</i>";

		// Bible Reference for style 4:
		contents += "<P>";
		contents = contents + "<input type='checkbox' name='reference' ";
		if (style["reference"] == 1){
			contents = contents + "checked='checked'";
		}
		contents = contents + "> Show Bible References <small><em>(for Style #4 only)</em></small>";
	
		//SORTING ORDER RADIO BUTTONS
		contents += "<p><b>Sorting Order:</b><p>";
		contents += "<input type='radio' name='sortorder' value='1' style='margin-left: 20px;' ";
		if (style == null || style["sortorder"] == null || style["sortorder"] == 1){
			contents += "checked";
		}
		contents += "> Ascending";
		
		contents += "<br><input type='radio' name='sortorder' value='2' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 2){
			contents += "checked";
		}
		contents += "> Descending";
		
		contents += "<br><input type='radio' name='sortorder' value='3' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 3){
			contents += "checked";
		}
		contents += "> Last Played";
		
		contents += "<br><input type='radio' name='sortorder' value='4' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 4){
			contents += "checked";
		}
		contents += "> Random";
	
		//RADIO STYLE BUTTONS
		contents += "<P><b>Styles</b><p>";
		contents += "<input type='radio' name='style' value='1' style='position: relative; top:-53px;' ";
		if (style["style"] == 1){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "newest_sermons1.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		// style 2
		contents += "<input type='radio' name='style' value='2' style='position: relative; top:-59px;' ";
		if (style["style"] == 2){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "newest_sermons2.gif' style='margin-left:10px;' />";
		contents += "<P>";
		
		// style 3
		contents += "<input type='radio' name='style' value='3' style='position: relative; top:-55px;' ";
		if (style["style"] == 3){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "newest_sermons3.gif' style='margin-left:10px;' />";

		// style 4
		contents += "<P>";
		contents += "<input type='radio' name='style' value='4' style='position: relative; top:-55px;' ";
		if (style == null || style["style"] == null || style["style"] == 4){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "newest_sermons4.gif' style='margin-left:10px;' />";
	}
	else if (value == "recommended_picks"){
		contents += "<P>Rows: <input type='text' name='maxrows' ";
		if (style["maxrows"] != null && style["maxrows"] != ""){
			contents += "value='" + style["maxrows"] + "'";
		}
		contents += " style='margin-left: 20px;' >";
		contents += " &nbsp;<small><em>*Can be left blank.</em></small>"

		//SORTING ORDER RADIO BUTTONS
		contents += "<p/><b>Sorting Order:</b><p>";
		contents += "<input type='radio' name='sortorder' value='1' style='margin-left: 20px;' ";
		if (style == null || style["sortorder"] == null || style["sortorder"] == 1){
			contents += "checked";
		}
		contents += "> Date Added";
		
		contents += "<br><input type='radio' name='sortorder' value='2' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 2){
			contents += "checked";
		}
		contents += "> Date Preached";
		
		contents += "<br><input type='radio' name='sortorder' value='3' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 3){
			contents += "checked";
		}
		contents += "> Last Played";
		
		contents += "<br><input type='radio' name='sortorder' value='4' style='margin-left: 20px;'  ";
		if (style["sortorder"] == 4){
			contents += "checked";
		}
		contents += "> Random";
	
		//RADIO STYLE BUTTONS
		contents += "<P><b>Styles</b><p>";
		contents += "<input type='radio' name='style' value='1' style='position: relative; top:-53px;' ";
		if (style["style"] == 1){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "recommended_picks1.gif' style='margin-left:10px;' />";
		
		// style 2
		contents += "<p>";
		contents += "<input type='radio' name='style' value='2' style='position: relative; top:-59px;' ";
		if (style["style"] == 2){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "recommended_picks2.gif' style='margin-left:10px;' />";
		
		// style 3
		contents += "<P>";
		contents += "<input type='radio' name='style' value='3' style='position: relative; top:-55px;' ";
		if (style["style"] == 3){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "recommended_picks3.gif' style='margin-left:10px;' />";

		// style 4
		contents += "<P>";
		contents += "<input type='radio' name='style' value='4' style='position: relative; top:-55px;' ";
		if (style == null || style["style"] == null || style["style"] == 4){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "recommended_picks4.gif' style='margin-left:10px;' />";
	}
	else if (value == "live_webcast"){
		//RADIO STYLE BUTTONS
		contents += "<P><b>Button Only</b><p>";
		contents += "<input type='radio' name='style' value='1' style='position: relative; top:-23px;' ";
		if (style == null || style["style"] == null || style["style"] == 1){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "live_webcast1.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='2' style='position: relative; top:-23px;' ";
		if (style["style"] == 2){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "live_webcast2.gif' style='margin-left:10px;' />";
		contents += "<P>";
		
		contents += "<b>Full Webcast Info</b><p>";
		
		contents += "<input type='radio' name='style' value='3' style='position: relative; top:-78px;' ";
		if (style["style"] == 3){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "live_webcast3.jpg' style='margin-left:10px;' />";
		
		contents += "<P><b>Embed Flash-based Webcast</b><p>";
		
		contents += "<input type='radio' name='style' value='4' style='position: relative; top:68px; float:left;' ";
		if (style["style"] == 4){
			contents += "checked";
		}
		contents += ">";
		contents += "<img src='" + imagePath + "live_webcast4.jpg' style='margin-left:10px; float:left;' />";
		contents += "<div style='position: relative; float: left; top: 48px;'>";
		contents += "Width: <input type='text' name='width' ";
		if (style["width"] != null){
			contents += "value='" + style["width"] + "' ";
		}else{
			contents += "value='640' ";
		}
		contents += "/>";
		contents += "<br>Height: <input type='text' name='height' ";
		if (style["height"] != null){
			contents += "value='" + style["height"] + "' ";
		}else{
			contents += "value='390' ";
		}
		contents += "/>";
		contents += "</div>";
	}
	else if (value == "photos"){
		//sermon id
		contents += "Album ID: <input type='text' name='albumid' ";
		if (style["albumid"] != null && style["albumid"] != ""){
			contents += "value='" + style["albumid"] + "'";
		}
		contents += ">";
		contents += "<br><i>Fill in the album ID to display a single album.</i>";
		
		contents += "<P> <img src='" + imagePath + "photos.gif' />";
	}	
	else if (value == "daily_devotional"){
		contents += "<img src='" + imagePath + "daily_devotional.gif' />";
	}
	else if (value == "sermonaudio_buttons"){
		//Links to SermonAudio
		contents += "<b>Links to SermonAudio.com</b><p>";
		contents += "<input type='radio' name='style' value='1' style='position: relative; top:-14px;' ";
		if (style == null || style["style"] == null || style["style"] == 1){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_button1.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='2' style='position: relative; top:-35px;' ";
		if (style["style"] == 2){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_button2.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='3' style='position: relative; top:-14px;' ";
		if (style["style"] == 3){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_button3.jpg' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='4' style='position: relative; top:-6px;' ";
		if (style["style"] == 4){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_button4.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='5' style='position: relative; top:-60px;' ";
		if (style["style"] == 5){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_button5.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		//PODCAST XML LINKS
		contents += "<p><b>Podcast Links</b><p>";
		contents += "<input type='radio' name='style' value='6' style='position: relative; top:-6px;' ";
		if (style["style"] == 6){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_podcast1.gif' style='margin-left:10px;' />";
		contents += "<p>";
		
		contents += "<input type='radio' name='style' value='7' style='position: relative; top:-86px;' ";
		if (style["style"] == 7){
			contents = contents + "checked";
		}
		contents = contents + ">";
		
		contents = contents + "<img src='" + imagePath + "sermonaudio_podcast2.jpg' style='margin-left:10px;' />";
	}
	else if (value == "calendar"){

		contents += "<img src='" + imagePath + "calendarbox.gif' />";

	}
	else if (value == "search"){

		contents += "Width: <input type='text' name='width' ";
		if (style["width"] != null){
			contents += "value='" + style["width"] + "' ";
		}else{
			contents += "value='' ";
		}
		contents += "/> <small><em>*Can be left blank</em></small>";

		contents += "<p/>";

		contents += "<img src='" + imagePath + "searchbox.gif' />";
	}
	
	
	div.innerHTML = contents;
	var descript = document.getElementById("sa_plugin_widget_type_description");
	descript.innerHTML = "<B>Description:</b> <I>" + widgetTypeDescriptions(value) + "</I>";
}

//returns a description for a widget type
function widgetTypeDescriptions(type){
	var description = "";
	switch(type){
		case "sermon_browser":
			description = "Embed a fully-functional sermon browser from your SermonAudio account to your own website! Page-through, filter, and search through all sermons in your library! To learn more about this widget, please <a target='_blank' href='http://www.sermonaudio.com/browser'><B>click here</b></a> for more information on our website.";
			break;
		case "featured_sermon":
			description = "Embed the currently featured sermon or latest sermon preached from your SermonAudio homepage! Optionally, specify the Sermon ID or if left blank it dynamically display the appropriate sermon based on options. To learn more, please <a target='_blank' href='http://www.sermonaudio.com/embed'><B>click here</b></a> for more information on our website.";
			break;
		case "flash":
			description = "Embed a flash widget of your sermons, videos, or picks on your own website! Use this flash widget to display the most recent sermons or videos on your site's front page and then use the sermon browser (above) to display your entire library of sermons. To learn more, please <a target='_blank' href='http://www.sermonaudio.com/new_details.asp?ID=26186'><B>click here</b></a> for more information on our website.";
			break;
		case "newest_sermons":
			description = "Embed the newest sermons on SermonAudio or from your church or from a specific speaker, or display sermons from a specific sermon series! To learn more, please <a target='_blank' href='http://www.sermonaudio.com/new_details.asp?ID=21094'><B>click here</b></a> for more information on our website.";
			break;
		case "recommended_picks":
			description = "Embed a random sampling of the my recommended picks sermon list on your own website! To display the recommended sermon picks by a specific broadcaster, replace the Member ID with the broadcaster's Member ID. To learn more, please <a target='_blank' href='http://www.sermonaudio.com/new_details.asp?ID=17803'><B>click here</b></a> for more information on our website.";
			break;
		case "live_webcast":
			description = "If you are a live webcaster, you can use this embed widgdet to display the live webcast button on your own site! When a LIVE webcast is in progress, the button turns RED! To learn more, please <a target='_blank' href='http://www.sermonaudio.com/webcast'><B>click here</b></a> for more information about live webcasting.";
			break;
		case "photos":
			description = "Embed your SermonAudio photo albums on to your own website. Be sure to replace the Member ID with your own Member ID. And optionally, replace the Album ID with an individual Album ID to display only a single album.";
			break;
		case "daily_devotional":
			description = "Use this widget to display the current Spurgeon's Daily Devotional (Morning & Evening) on your own website. It's quick. It's easy. And it provides fresh content twice daily to your site! Please <a target='_blank' href='http://www.sermonaudio.com/daily'><B>click here</b></a> to go to the Daily Devotional section on the website.";
			break;
		case "sermonaudio_buttons":
			description = "Help promote SermonAudio.com with these free buttons! Simply click on the button graphic of your choice, then insert it into your own web page. Nothing could be easier! Please <a target='_blank' href='http://www.sermonaudio.com/goodies.asp'><B>click here</b></a> to see more buttons and graphics on the website.";
			break;
		case "calendar":
			description = "Display the upcoming events & blogs from your SermonAudio homepage! Don't forget to enter your Member ID."
			break;
		case "search":
			description = "Display a sermon search box. To search only a specific broadcaster, enter the broadcaster's Member ID above."
			break;
		default:
			break;
	}
	return description;
}

function initOptions(path){
	pluginPath = path + "sermonaudio-widgets/";
	imagePath = pluginPath + "images/";
	//alert(pluginPath);
}