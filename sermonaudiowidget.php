<?php
/*
Plugin Name: SermonAudio Widgets
Plugin URI: http://sermonaudio.com
Description: Customizable plugin to show sermons from your SermonAudio account on your Wordpress page.
Version: 1.9.3
Author: Dan Alexander
Author URI: http://sermonaudio.com
License: FreeBSD

*/

/* SETUP DATABASE AND INSTALL WIDGET*/

	global $wpdb;
	global $sa_plugin_table;
	global $sa_plugin_db_version;	
	$sa_plugin_table = $wpdb->prefix . 'sa_plugin';
	$sa_plugin_db_version = '1.9.3';

	//INSTALL
	register_activation_hook( __FILE__,  'sa_widget_install' );

	//UNINSTALL
	register_deactivation_hook( __FILE__, 'sa_widget_uninstall');

function sa_widget_install() {
	
	global $wpdb;
	global $sa_plugin_table;
	global $sa_plugin_db_version;

	if ( $wpdb->get_var( "show tables like '$sa_plugin_table'" ) != $sa_plugin_table ) {
			

		$wpdb->show_errors();

		$sql = "CREATE TABLE $sa_plugin_table (".
			"Id INT NOT NULL AUTO_INCREMENT, ".
			"settings TEXT NOT NULL, ".
			"PRIMARY KEY Id (Id) ".
			")";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );


		// CREATE EXAMPLE

		$examp = array('name' => 'Example Widget',
			'slug' => 'example widget',
			'description' => 'This is an example widget.',
			'memberID' => 'FAITH',
			'type' => 'sermon_browser',
			'style' => 1);

		$exampJSON = json_encode($examp);

		$res = $wpdb->insert( $sa_plugin_table, array('settings' => $exampJSON) );
		
		add_option( "sa_plugin_db_version", $sa_plugin_db_version );
	}
	
}

function sa_widget_update_check(){

	global $wpdb;
	global $sa_plugin_table;

	
	if (get_option("sa_plugin_db_version") && get_option("sa_plugin_db_version") == "1.9.3"){
		return;
	}
	else if (get_option("sa_plugin_db_version") && (get_option("sa_plugin_db_version") == "1.6" || get_option("sa_plugin_db_version") == "1.7"
		|| get_option("sa_plugin_db_version") == "1.8" || strpos(get_option("sa_plugin_db_version"), "1.9") == 0)){
		// updating since version 1.6 is easy and only requires updating the version field
		update_option("sa_plugin_db_version", "1.9.3");
		return;
	}

	// versions older than 1.6 need more work

	update_option("sa_plugin_db_version", "1.9.3");

	$wpdb->query("DROP TABLE IF EXISTS $sa_plugin_table");

	if ( $wpdb->get_var( "show tables like '$sa_plugin_table'" ) != $sa_plugin_table ) {
			

		$wpdb->show_errors();

		$sql = "CREATE TABLE $sa_plugin_table (".
			"Id INT NOT NULL AUTO_INCREMENT, ".
			"settings TEXT NOT NULL, ".
			"PRIMARY KEY Id (Id) ".
			")";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );


		// CREATE EXAMPLE

		$examp = array('name' => 'Example Widget',
			'slug' => 'example widget',
			'description' => 'This is an example widget.',
			'memberID' => 'FAITH',
			'type' => 'sermon_browser',
			'style' => 2);

		$exampJSON = json_encode($examp);

		$res = $wpdb->insert( $sa_plugin_table, array('settings' => $exampJSON) );
		
	}
}

add_action( 'plugins_loaded', 'sa_widget_update_check' );

function sa_widget_uninstall(){

	//global $wpdb;
	//global $sa_plugin_table;
	//global $sa_plugin_db_version;

	/*
	if (get_option("sa_plugin_db_version")){

		remove_option("sa_plugin_db_version");
	}
	*/

	// we no longer want to remove the table. the user can now do that if he desires.
	//$wpdb->query("DROP TABLE IF EXISTS $sa_plugin_table");
}

/* MAIN HOOK SHORTCODE, RUN WHEN A PAGE/POST IS LOADED */
function SermonAudioPlugin_Handler($atts) {
	  return sa_plugin_hook($atts['id']);
}
add_shortcode('SermonAudio', 'SermonAudioPlugin_Handler');
function sa_plugin_hook($widgetID){

	global $wpdb;
	global $sa_plugin_table;

	$results = $wpdb->get_results( "SELECT * FROM $sa_plugin_table WHERE Id=$widgetID" );
	if (count($results) == 0){
		return "SermonAudio Plugin Error: Bad ID Given."; //bad widget
	}

	$selectedWidget = $results[0];

	$widgetJSON = json_decode($selectedWidget->settings);

	// memberID is used in almost all widgets, so get it here
	$memberID = $widgetJSON->memberID;

	if ($memberID == "" || $memberID == null){
		$memberID = "MEMBERID";
	}

	// get the type here
	$type = $widgetJSON->type;

	$types = array(
		"none"=>'None',	
		"audio_book"=>"Audio+Book",
		"bible_study"=>"Bible+Study",
		"camp_meeting"=>"Camp+Meeting",
		"chapel_service"=>"Chapel+Service",
		"children"=>"Children",
		"conference"=>"Conference",
		"current_events"=>"Current+Events",
		"debate"=>"Debate",
		"devotional"=>"Devotional",
		"funeral_service"=>"Funeral+Service",
		"midweek_service"=>"Midweek+Service",
		"prayer_meeting"=>"Prayer+Meeting",
		"question_answer"=>"Question+%26+Answer",
		"radio_broadcast"=>"Radio+Broadcast",
		"special_meeting"=>"Special+Meeting",
		"sunday_am"=>"Sunday+%2D+AM",
		"sunday_pm"=>"Sunday+%2D+PM",
		"sunday_afternoon"=>"Sunday+Afternoon",
		"sunday_school"=>"Sunday+School",
		"sunday_service"=>"Sunday+Service",
		"teaching"=>"Teaching",
		"testimony"=>"Testimony",
		"tv_broadcast"=>"TV+Broadcast",
		"video_dvd"=>"Video+DVD",
		"wedding"=>"Wedding",
		"youth"=>"Youth"
	);

	$imagePath = plugin_dir_url('sermonaudiowidget.js') . "sermonaudio-widgets/images/";

	$script = "";

	$src = "http";

	if (is_ssl()) {
    	//action to take for page using SSL
		$src = "https";
  	}
	
	if ($type == "sermon_browser"){

		$maxrows = $widgetJSON->maxrows;

		if ($maxrows == null || $maxrows == 0){
			$maxrows = 30;
		}

		$hideheader = "false";
		if ($widgetJSON->header == 0){
			$hideheader = "true";
		}

		$hidelogo = "false";
		if ($widgetJSON->logo == 0){
			$hidelogo = "true";
		}

		$alwaysbible = "false";
		if ($widgetJSON->reference == 1){
			$alwaysbible = "true";
		}

		$hidesort = "false";
		if ($widgetJSON->hidesort == 1){
			$hidesort = "true";
		}

		$speakerName = "";
		if ($widgetJSON->view == 1){
			if ($widgetJSON->speakername != null && $widgetJSON->speakername != ""){
				//replace spaces with + symbol
				$speakerName = "&speaker=" . str_replace(" ","+",$widgetJSON->speakername);
				$speakerName = str_replace("'","&#39;",$speakerName);
			}
		}

		$seriesName = "";
		if ($widgetJSON->view == 2){
			if ($widgetJSON->seriesname != null && $widgetJSON->seriesname != ""){
				//replace spaces with + symbol
				$seriesName = "&series=" . str_replace(" ","+",$widgetJSON->seriesname);
				$seriesName = str_replace("'","&#39;",$seriesName);
			}
		}

		$bible = "";
		if ($widgetJSON->view == 3){
			$bible = "&bible=true";
		}

		$style = 1;
		if ($widgetJSON->style == 2){
			$style = 2;
		}


		$script = "<div class='sa_plugin_sermon_browser'>
					<!--Begin SermonAudio Link Button-->
					<SCRIPT type=\"text/javascript\">
					document.write(\"<\" + \"script  src='" . $src . "://www.sermonaudio.com/code_sermonlist.asp?sourceid=" . $memberID . "&hideheader=" . $hideheader . "&hidelogo=" . $hidelogo . "&alwaysbible=" . $alwaysbible . "&rows=" . $maxrows . "&hidesort=" . $hidesort . $seriesName . $speakerName . $bible . "&style=" . $style . "&sourcehref=\" + escape(location.href) + \"'><\",\"/script>\");
					</SCRIPT>
					<!--End SermonAudio Link Button-->
					</div>";
	}
	else if ($type == "featured_sermon"){

		$flash = ($widgetJSON->flash == 0 ? "FALSE" : "TRUE");
		$tiny = ($widgetJSON->tiny == 0 ? "FALSE" : "TRUE");
		$minimal = ($widgetJSON->minimal == 0 ? "FALSE" : "TRUE");

		$eventType = $widgetJSON->eventtype;

		if ($eventType == null || $eventType == "" || $eventType == "none"){
			$eventType = "EVENTID";
		}else{
			$eventType = $types[$eventType];
		}

		$sermonID = "";
		if ($widgetJSON->newest == 1){
			$sermonID = "";
		}else if ($widgetJSON->sermonid != null && $widgetJSON->sermonid != ""){
			$sermonID = $widgetJSON->sermonid;
		}
		
		$script = "<!--Begin SermonAudio Embed-->
					<IFRAME width=\"450\" height=\"100\" 
					src=\"" . $src . "://www.sermonaudio.com/code_sourcefeatured.asp?iframe=TRUE&reversecolor=FALSE&showoverview=FALSE&flashplayer=" . $flash . "&tiny=" . $tiny . "&minimal=" . $minimal . "&eventtype=" . $eventType . "&sermonid=". $sermonID . "&sourceid=" . $memberID . "\"
					 frameborder=\"0\" scrolling=\"no\" allowtransparency=\"true\">
					 </IFRAME>
					 <!--End SermonAudio Embed-->";
	}
	else if ($type == "flash"){

		$tiny = ($widgetJSON->tiny == 0 ? "FALSE" : "TRUE");
		$minimal = ($widgetJSON->minimal == 0 ? "FALSE" : "TRUE");
	
		$script = "<!--Begin SermonAudio Link Button-->
					<SCRIPT type=\"text/javascript\" 
					src=\"" . $src . "://www.sermonaudio.com/code_sourceplaylist.asp?sourceid=" . $memberID . "&mode=VIDEO&tiny=" . $tiny . "&minimal=" . $minimal . "&reversecolor=FALSE\">
					</SCRIPT>
					<!--End SermonAudio Link Button-->
					<!--End SermonAudio Link Button-->";
	}
	else if ($type == "newest_sermons"){
	
		$sortOrder = "";
		if ($widgetJSON->sortorder == 1){
			$sortOrder = "ASC";
		}
		else if ($widgetJSON->sortorder == 2){
			$sortOrder = "DESC";
		}else if ($widgetJSON->sortorder == 3){
			$sortOrder = "LASTPLAYED";
		}
		else{
			$sortOrder = "ASC";
		}


		if ($widgetJSON->eventtype == null || $widgetJSON->eventtype == "" || $widgetJSON->eventtype == "none"){
			$eventType = "EVENTID";
		}else{
			$eventType = $types[$widgetJSON->eventtype];
		}


		if ($widgetJSON->seriesname == null || $widgetJSON->seriesname == ""){
			$seriesName = "SUBTITLE";
		}else{
			//replace spaces with + symbol
			$seriesName = str_replace(" ","+",$widgetJSON->seriesname);
			$seriesName = str_replace("'","&#39;",$seriesName);
		}


		if ($widgetJSON->speakername == null || $widgetJSON->speakername == ""){
			$speakerName = "SPEAKERNAME";
		}else{
			//replace spaces with + symbol
			$speakerName = str_replace(" ","+",$widgetJSON->speakername);
			$speakerName = str_replace("'","&#39;",$speakerName);
		}

		$maxrows = $widgetJSON->maxrows;
		if ($maxrows == null || $maxrows == 0){
			$maxrows = 6;
		}

		$style = 1;
		if ($widgetJSON->style != null){
			$style = $widgetJSON->style;
		}

		$alwaysbible = "";
		if ($style == 4){
			if ($widgetJSON->reference != null && $widgetJSON->reference == 1){
				$alwaysbible = "&alwaysbible=true";
			}
		}
	
		$script ="<!--Begin SermonAudio Link Button-->
  					<SCRIPT type=\"text/javascript\"
  					src=\"" . $src . "://www.sermonaudio.com/code_sermonsub.asp?sourceid=" . $memberID . "&speaker=" . $speakerName . "&eventtype=" . $eventType . "&series=" . $seriesName . "&showallseries=FALSE&style=" . $style . "&hideicon=FALSE&maxrows=" . $maxrows . "&orderby=" . $sortOrder . $alwaysbible . "\">
  					</SCRIPT>
					<!--End SermonAudio Link Button-->";
	}
	else if ($type == "recommended_picks"){
		$sortOrder = "";
		$hideIcon = "FALSE";

		if ($widgetJSON->sortorder == 1){
			$sortOrder = "DATEADDED";
		}
		else if ($widgetJSON->sortorder == 2){
			$sortOrder = "DATE";
		}else if ($widgetJSON->sortorder == 3){
			$sortOrder = "LASTPLAYED";
		}
		else{
			$sortOrder = "DATEADDED";
		}

		$style = $widgetJSON->style;

		if ($style> 1){
			$hideIcon = "TRUE";
		}

		$maxrows = $widgetJSON->maxrows;
		if ($maxrows == null || $maxrows == 0){
			$maxrows = 6;
		}
		
		$script = "<!--Begin SermonAudio Link Button-->
  					<SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_recommendedpicks.asp?sourceid=" . $memberID . "&hideicon=" . $hideIcon . "&style=" . $style . "&maxrows=" . $maxrows . "&orderby=" . $sortOrder . "\"></SCRIPT>
					<!--End SermonAudio Link Button-->";
	}
	else if ($type == "live_webcast"){
		$style = $widgetJSON->style;

		if ($style == 3){
			$script = "<div class='sa_plugin_live_webcast'>
						<!--Begin SermonAudio Link Button-->
  						<SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_webcast.asp?mode=full&sourceid=" . $memberID . "&style=1\"></SCRIPT>
						<!--End SermonAudio Link Button-->
						</div>";
		}
		else if ($style == 4){
			$width = $widgetJSON->width;
			if ($width == 0 || $width == null){
				$width = 640;
			}
			$height = $widgetJSON->height;
			if ($height == 0 || $height == null){
				$height = 0;
			}
		
			$script = "<div class='sa_plugin_live_webcast'>
						<!--Begin SermonAudio Link Button-->
 						 <SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_playwebcast.asp?sourceid=" . $memberID . "&width=" . $width . "&height=" . $height . "\"></SCRIPT>
						<!--End SermonAudio Link Button-->
						</div>";
		}
		else{
			if ($style != 1 && $style != 2){
				$style = 1;
			}
			$script = "<div class='sa_plugin_live_webcast'>
						<!--Begin SermonAudio Link Button-->
  						<SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_webcast.asp?sourceid=" . $memberID . "&style=" . $style . "\"></SCRIPT>
						<!--End SermonAudio Link Button-->
						</div>";
		}
		
	}
	else if ($type == "photos"){
		$albumID = "ALBUMID";
		if ($widgetJSON->albumid != null && $widgetJSON->albumid != ""){
			$albumID = $widgetJSON->albumid;
		}
		$script = "<!--Begin SermonAudio Link Button-->
  					<SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_album2.asp?sourceid=" . $memberID . "&albumid=" . $albumID . "&max=6\"></SCRIPT>
					<!--End SermonAudio Link Button-->";
	}
	else if ($type == "daily_devotional"){
		$script = "<!--Begin SermonAudio Link Button-->
 					 <SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_spurgeon.asp?titlecolor=123154\"></SCRIPT>
					<!--End SermonAudio Link Button-->";
	}
	else if ($type == "sermonaudio_buttons"){
		$style = $widgetJSON->style;
		//normal link buttons
		if ($style >= 1 && $style <= 5){
			$image = $imagePath . "sermonaudio_button" . $style . ($style == 3 ? ".jpg" : ".gif");
			$script = "<!--Begin SermonAudio Link Button-->
  						<A href=\"" . $src . "://www.sermonaudio.com/\" target=\"_blank\"><IMG src=\"" . $image . "\" alt=\"SermonAudio.com MP3 Sermons\" border=\"0\"></A>
						<!--End SermonAudio Link Button-->";
		}
		else if ($style == 6){
			//podcast buttons
			
			$image = $imagePath . "sermonaudio_podcast1.gif";
			$source = "";
			if ($memberID != "MEMBERID"){
				$source = "?sourceid=" . $memberID;
			}
			
			$script = "<!--Begin SermonAudio Link Button-->
 						 <A href=\"" . $src . "://www.sermonaudio.com/rss_source.rss" . $source . "\"><IMG src=\"" . $image . "\" alt=\"Subscribe\" border=\"0\"></A>
						<!--End SermonAudio Link Button-->";
			
		}else{
			//big podcast thing
			
			$image = $imagePath . "sermonaudio_podcast2.jpg";
			$script = "<!--Begin SermonAudio Link Button-->
  						<IMG USEMAP=\"#samypodcast\" src=\"" . $src . "://images.sa-media.com/images/mypodcast.jpg\" width=\"150\" height=\"172\" alt=\"Subscribe to my podcast\" border=\"0\">
  						<MAP NAME=\"samypodcast\">
  						<AREA SHAPE=RECT COORDS=\"0,0,150,156\" HREF=\"itpc://www.sermonaudio.com/rss_source.rss?sourceid=" . $memberID . "\"></AREA>
  						<AREA SHAPE=RECT COORDS=\"0,155,53,172\" TITLE=iTunes HREF=\"itpc://www.sermonaudio.com/rss_source.rss?sourceid=" . $memberID . "\"></AREA>
  						<AREA SHAPE=RECT COORDS=\"53,155,87,172\" TITLE=Zune HREF=\"zune://subscribe/?Our SermonAudio Podcast=www.sermonaudio.com/rss_source.rss?sourceid=" . $memberID . "\"></AREA>
  						<AREA target=_new SHAPE=RECT COORDS=\"87,155,150,172\" TITLE=RSS HREF=\"http://www.sermonaudio.com/rss_source.rss?sourceid=" . $memberID . "\"></AREA>
  						</MAP>
						<!--End SermonAudio Link Button-->";
		}
	}
	else if ($type == "calendar"){
		$script = "<!--Begin SermonAudio Link Button-->
  				  <SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_calendar.asp?sourceid=" . $memberID . "\"></SCRIPT>
				 <!--End SermonAudio Link Button-->";
	}
	else if ($type == "search"){

		$width = "";
		if ($widgetJSON->width != null){
			$width = $widgetJSON->width;
		}

		$script = "<!--Begin SermonAudio Link Button-->
  				  <SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_searchbox.asp?sourceid=" . $memberID . "&width=" . $width . "\"></SCRIPT>
				  <!--End SermonAudio Link Button-->";

	}
	else{
		$script = "<!--Begin SermonAudio Link Button-->
		  <SCRIPT type=\"text/javascript\" src=\"" . $src . "://www.sermonaudio.com/code_sermonsub.asp?sourceid=" . $memberID . "&speaker=SPEAKERNAME&eventtype=EVENTID&series=SUBTITLE&showallseries=FALSE&hideicon=FALSE&orderby=\"></SCRIPT>
		<!--End SermonAudio Link Button-->";
	}

	$script = str_replace("&#39;","42_TEMP_HOLDER",$script);
	$script = str_replace("&amp;","&",$script);
	$script = str_replace("&#038;","&",$script);
	$script = str_replace("&#38;","&",$script);
	$script = str_replace("&","&amp;",$script);
	$script = str_replace("42_TEMP_HOLDER", "&#39;", $script);

	return $script;
}

//adds the CSS file to the header
add_action('wp_head','sa_plugin_header_code');
function sa_plugin_header_code(){
	echo '<link type="text/css" rel="stylesheet" href="' . plugins_url("sermonaudiowidget.css",__FILE__) . '" />';
}

//adds the options page to the administration->plugins menu.
function sa_plugin_menu_setup(){
    //page name, menu text, permission required,permalink (unique),method that will generate the content
	add_plugins_page("SermonAudio Plugin","SermonAudio Plugin","manage_options","SermonAudioPluginOptions","sa_plugin_main_page");
}

//setup the settings for the options page
function sa_options_init(){
	//register the settings: Group, option name, validation function to call.
	register_setting('sa_options','sa_options','sa_options_validate');
}

//this is called when the administration menus are built.
add_action('admin_menu','sa_plugin_menu_setup');
//add_action('admin_init','sa_options_init');

//HTML for the SermonAudio options.
function sa_options_page(){ ?>
	<script src="<?php echo plugins_url('sermonaudiowidget.js',__FILE__);?>"></script>
	<script type="text/javascript">initOptions("<?php echo plugin_dir_url('sermonaudiowidget.js');?>");</script>
	<div>
	<h2>SermonAudio Plugin Options</h2>
	You can customize the SermonAudio plugin to your liking here.
	<form action="options.php" method="post">
	<?php settings_fields('sa_options'); ?>
	<?php do_settings_sections('sa_plugin'); ?>
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	
	</form>
	</div>
<?php }

function sa_plugin_setting_memberid_string(){
	$options = get_option('sa_options');
	echo "<input id='plugin_text_string' name='sa_options[member_id]' size='40' type='text' value='{$options['member_id']}' />";
}

function sa_plugin_setting_widget_string(){
	$options = get_option('sa_options');

	$dict = array(
		"sermon_browser" => "Sermon Browser",
		"featured_sermon" => "Featured Sermon",
		"flash" => "Flash",
		"newest_sermons" => "Newest Sermons",
		"recommended_picks" => "Recommended Picks",
		"live_webcast" => "Live Webcast",
		"photos" => "Photos",
		"daily_devotional" => "Daily Devotional",
		"sermonaudio_buttons" => "SermonAudio Buttons",
		"calendar" => "Calendar",
		"search" => "Search Box",
	);

	$selected = $options['widget'];
	echo "<select name='sa_options[widget]' onchange='widgetSelect(this)'>";
	
	foreach($dict as $key => $value){
		echo "<option value='" . $key . "' ";
		if ($key == $selected){
			echo "selected='true'";
		}
		echo ">" . $value . "</option>";
	}
	
	echo "</select>";
}

function sa_plugin_main_page(){

	//important scripts
	?>
		<script src="<?php echo plugins_url('sermonaudiowidget.js',__FILE__);?>"></script>
		<script type="text/javascript">initOptions("<?php echo plugin_dir_url('sermonaudiowidget.js');?>");</script>
	<?php

	if (isset($_POST["widget_overview_action"])){
		$action = $_POST["widget_overview_action"];
		$widgetID = $_POST["widgetID"];
		if ($action == "Edit"){
			sa_plugin_editWidget($widgetID);
		}else if ($action == "Delete"){
			sa_plugin_deleteWidget($widgetID);
		}else if ($action == "Add New Widget"){
			sa_plugin_addWidget();
		}
	}else if (isset($_POST["widget_edit_action"]) && $_POST["widget_edit_action"] == "Save"){
		sa_plugin_saveWidget();
	}
	else{
		sa_plugin_overview();
	}
}

function sa_plugin_editWidget($widgetID){
	global $wpdb;
	global $sa_plugin_table;

	$bloginfo = get_bloginfo( 'wpurl' );
	
	$results = $wpdb->get_results( "SELECT * FROM $sa_plugin_table WHERE Id=$widgetID" );
	
	$selectedWidget = $results[0];

	$jsonString = $selectedWidget->settings;

	$settingsDecoded = json_decode($jsonString);
	
	//widget type selection
	$dict = array(
		"sermon_browser" => "Sermon Browser",
		"featured_sermon" => "Featured Sermon",
		"flash" => "Flash",
		"newest_sermons" => "Newest Sermons",
		"recommended_picks" => "Recommended Picks",
		"live_webcast" => "Live Webcast",
		"photos" => "Photos",
		"daily_devotional" => "Daily Devotional",
		"sermonaudio_buttons" => "SermonAudio Buttons",
		"calendar" => "Calendar",
		"search" => "Search Box",
	);

	?>
	
	<h2>SermonAudio Widget Settings - Edit</h2>
	<P>Edit the widget's settings.</p>
	
	<form name="sa_edit_widget" method="post" action="<?php echo $bloginfo; ?>/wp-admin/plugins.php?page=SermonAudioPluginOptions">
		<input type="hidden" name="widgetID" value="<?php echo $selectedWidget->Id; ?>">
		<P>Widget Name: <input id="sa_plugin_name_string" name="name" size="40" type="text" value="<?php echo str_replace("\'","'",$settingsDecoded->name);?>" />
		<P>Description: <input id="sa_plugin_description_string" name="description" size="40" type="text" value="<?php echo str_replace("\'","'",$settingsDecoded->description);?>" />
		<P/>
		<P>Member ID: <input id='sa_plugin_memberID_string' name='memberID' size='20' type='text' value='<?php echo $settingsDecoded->memberID;?>' /><div id="memberid_required"></div>
		<P>Widget Type: <select name="type" onchange="widgetSelect(this)">
		<?php
			foreach($dict as $key => $value){
				echo "<option value='" . $key . "' ";
				if ($key == $settingsDecoded->type){
					echo "selected='true'";
				}
				echo ">" . $value . "</option>";
			}
		?>
		</select>
		<P>
		<div id="sa_plugin_widget_type_description" style="width:600px;">Widget description goes here...</div>
		<hr style="border: 0; color: #DFDFDF; background-color: #DFDFDF; height: 1px; margin-top: 18px; margin-bottom: 18px;" />
		<P>
		
		<div id="sa_plugin_customize"></div>
		<div style="clear:both;"></div>
		<input type="submit" name="widget_edit_action" class="button-primary" value="Cancel"/>
		<input type="submit" name="widget_edit_action" class="button-primary" value="Save">
	</form>
		<script type="text/javascript">
			/*
			var style = {"style":<?php echo $settingsDecoded->style;?>,
							"albumid":'<?php echo $settingsDecoded->albumid;?>',
							"sermonid":'<?php echo $settingsDecoded->sermonid;?>',
							"minimal":<?php echo $settingsDecoded->minimal;?>,
							"tiny":<?php echo $settingsDecoded->tiny;?>,
							"flash":<?php echo $settingsDecoded->flash;?>,
							"newest":<?php echo $settingsDecoded->newest;?>,
							"eventtype":'<?php echo $settingsDecoded->eventtype;?>',
							"seriesname":'<?php echo $settingsDecoded->seriesname;?>',
							"speakername":'<?php echo $settingsDecoded->speakername;?>',
							"width":<?php echo $settingsDecoded->width;?>,
							"height":<?php echo $settingsDecoded->height;?>,
							"maxrows":<?php echo $settingsDecoded->maxrows;?>
						};
			*/
			var style = <?php echo $jsonString; ?>;

			widgetDetails("<?php echo $settingsDecoded->type; ?>",style);
			
		</script>
<?php
}

function sa_plugin_deleteWidget($widgetID){
	
	global $wpdb;
	global $sa_plugin_table;
	
	$wpdb->query( "DELETE FROM $sa_plugin_table WHERE Id = '".$widgetID."'" );
	
	sa_plugin_overview();
}

function sa_plugin_addWidget(){
	global $wpdb;
	global $sa_plugin_table;

	$examp = array('name' => 'New Default Widget',
		'slug' => 'new default widget',
		'description' => 'Put a description here.',
		'memberID' => '',
		'type' => 'sermon_browser',
		'style' => 2);

	$exampJSON = json_encode($examp);

	/*
	$sql = "INSERT INTO $sa_plugin_table(settings) VALUES(".
		"\"$exampJSON\"".
		")";
	*/

	$res = $wpdb->insert( $sa_plugin_table, array('settings' => $exampJSON) );
	
	sa_plugin_overview();
}

function sa_plugin_saveWidget(){
	global $wpdb;
	global $sa_plugin_table;

	$ID = $_POST["widgetID"];
	$name = $_POST["name"];
	$slug = $_POST["name"];
	$description = $_POST["description"];

	if (isset($_POST["memberID"])){
		$memberID = $_POST["memberID"];
	}else{
		$memberID = "";
	}
	$type = $_POST["type"];
	if (isset($_POST["style"])){
		$style = $_POST["style"];
	}else{
		$style = 1;
	}
	if ($_POST["minimal"] == "on"){
		$minimal = 1;
	}else{
		$minimal = 0;
	}
	if ($_POST["tiny"] == "on"){
		$tiny = 1;
	}else{
		$tiny = 0;
	}
	if ($_POST["flash"] == "on"){
		$flash = 1;
	}else{
		$flash = 0;
	}
	if (isset($_POST["albumid"])){
		$albumid = $_POST["albumid"];
	}else{
		$albumid = "";
	}
	if (isset($_POST["sermonid"])){
		$sermonid = $_POST["sermonid"];
	}else{
		$sermonid = "";
	}
	if (isset($_POST["sortorder"])){
		$sortorder = $_POST["sortorder"];
	}else{
		$sortorder = 1;
	}
	if ($_POST["newest"] == "on"){
		$newest = 1;
	}else{
		$newest = 0;
	}
	if (isset($_POST["eventtype"])){
		$eventtype = $_POST["eventtype"];
	}else{
		$eventtype = '';
	}
	if (isset($_POST["seriesname"])){
		$seriesname = $_POST["seriesname"];
	}else{
		$seriesname = '';
	}
	if (isset($_POST["speakername"])){
		$speakername = $_POST["speakername"];
	}else{
		$speakername = '';
	}
	if (isset($_POST["width"])){
		$width = $_POST["width"];
	}else{
		$width = 0;
	}
	if (isset($_POST["height"])){
		$height = $_POST["height"];
	}else{
		$height = 0;
	}

	if (isset($_POST["maxrows"])){
		$maxrows = $_POST["maxrows"];
	}
	else{
		$maxrows = 0;
	}

	if ($_POST["header"] == "on"){
		$header = 1;
	}
	else{
		$header = 0;
	}

	if ($_POST["reference"] == "on"){
		$reference = 1;
	}
	else{
		$reference = 0;
	}

	if ($_POST["logo"] == "on"){
		$logo = 1;
	}
	else{
		$logo = 0;
	}

	if ($_POST["hidesort"] == "on"){
		$hidesort = 1;
	}
	else{
		$hidesort = 0;
	}

	if (isset($_POST["view"])){
		$view = $_POST["view"];
	}
	else{
		$view = 0;
	}
	
	$settingsArray = array(
				"name" => $name,
				"slug" => $slug,
				"description" => $description,
				"memberID" => $memberID,
				"type" => $type,
				"style" => $style,
				"minimal" => $minimal,
				"tiny" => $tiny,
				"flash" => $flash,
				"albumid" => $albumid,
				"sermonid" => $sermonid,
				"sortorder" => $sortorder,
				"newest" => $newest,
				"eventtype" => $eventtype,
				"speakername" => $speakername,
				"seriesname" => $seriesname,
				"width" => $width,
				"height" => $height,
				"maxrows" => $maxrows,
				"header" => $header,
				"logo" => $logo,
				"reference" => $reference,
				"hidesort" => $hidesort,
				"view" => $view,
				);

	$settingsJSON = json_encode($settingsArray);
	
	$result = $wpdb->update($sa_plugin_table,array("settings" => $settingsJSON),array("Id" => $ID));
	sa_plugin_overview();
}

function sa_plugin_overview(){
	
	global $wpdb;
	global $sa_plugin_table;
	
	$dict = array(
		"sermon_browser" => "Sermon Browser",
		"featured_sermon" => "Featured Sermon",
		"flash" => "Flash",
		"newest_sermons" => "Newest Sermons",
		"recommended_picks" => "Recommended Picks",
		"live_webcast" => "Live Webcast",
		"photos" => "Photos",
		"daily_devotional" => "Daily Devotional",
		"sermonaudio_buttons" => "SermonAudio Buttons",
		"calendar" => "Calendar",
		"search" => "Search Box",
	);
	
	$widgets = $wpdb->get_results( "SELECT * FROM $sa_plugin_table" );

	?>
	<h2>SermonAudio Widget Settings</h2>
	<P>Here you can set up multiple SermonAudio widgets to display across your site.
	Once you have a widget set up the way you want it, use the short code in a post or page to have that widget show up. If you would like to learn more about this widget, <a href="http://sermonaudio.com/wordpress" target="_blank">click here</a> to go to SermonAudio.com.</p>
	
	<table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th>Name</th>
            <th>Short Code</th>
            <th>Type</th>
            <th>Description</th>
            <th class="eg-cell-spacer-136"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Name</th>
            <th>Short Code</th>
            <th>Type</th>
            <th>Description</th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<?php 
        	
        		for ($index = 0; $index < count($widgets); $index++){
        			$widget = $widgets[$index];
        			$widgetSettingsString = $widget->settings;
        			$widgetSettings = json_decode($widgetSettingsString);
        	?>			
				<tr>
					<td><?php echo str_replace("\'","'",$widgetSettings->name); ?></td>
					<td><input type="text" size="40" value="[SermonAudio id='<?php echo $widget->Id; ?>']"></td>
					<td><?php echo $dict[$widgetSettings->type]; ?></td>
					<td><?php echo str_replace("\'","'",$widgetSettings->description); ?></td>
					<td class="major-publishing-actions">
					<form name="sa_delete_widget" method="post" action="<?php echo get_bloginfo( 'wpurl' );?>/wp-admin/plugins.php?page=SermonAudioPluginOptions">
						<input type="hidden" name="widgetID" value="<?php echo $widget->Id; ?>">
						<input type="submit" name="widget_overview_action" class="button-primary" value="Edit"/>
						<input type="submit" name="widget_overview_action" class="button-primary" value="Delete">
					</form>
					</td>
				</tr>
            <?php } ?>
		</tbody>
     </table>
     <P>
     <form name="sa_add_widget" method="post" action="<?php echo get_bloginfo( 'wpurl' );?>/wp-admin/plugins.php?page=SermonAudioPluginOptions">
     	<input type="submit" name="widget_overview_action" class="button-primary" value="Add New Widget" />
     </form>
	<?php
}

?>