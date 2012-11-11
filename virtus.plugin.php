<?php

class Virtus extends Plugin
{
	
	public function action_update_check()
	{
		Update::add( $this->info->name, '7069002d-da25-46c5-8dd7-198e2fd27b34', $this->info->version );
	}
	
	private static function get_virtues() {
		$virtues = array(
			'vigor' => array(
				'name' => 'Vigor',
				'parts' => 1
				),
			'purity' => array(
				'name' => 'Purity',
				'parts' => 2
				),
			'amiability' => array(
				'name' => 'Amiability',
				'parts' => 1
				),
			'courage' => array(
				'name' => 'Courage',
				'parts' => 1
				),
			'moderation' => array(
				'name' => 'Moderation',
				'parts' => 1,
				'default' => 1
				),
			'integrity' => array(
				'name' => 'Integrity',
				'parts' => 1,
				'default' => 1
				),
			'peace' => array(
				'name' => 'Peace',
				'parts' => 1
				),

			);
					
		return $virtues;
	}
	
	public function action_init() {
		// self::install();		
		
		$this->add_template('habit_month', dirname(__FILE__) . '/month.php');
		$this->add_template('habit_day', dirname(__FILE__) . '/day.php');
		$this->add_template('habit_day_inner', dirname(__FILE__) . '/day_inner.php');
		
		// Rewrite rules
		$this->add_rule('"virtus"', 'view_virtus');
		$this->add_rule('"virtus"/"month"/year/month', 'view_month');
		$this->add_rule('"virtus"/"day"/year/month/day', 'view_day');
		$this->add_rule('"virtus"/"inner"/year/month/day', 'view_day_inner');
		
		$this->add_rule('"virtus"/"today"', 'view_today');
		
		$this->add_rule('"homepage"', 'view_homepage');
		
		$this->add_rule('"virtus"/"manifest"/manifest/"default.manifest"', 'manifest');
	}
	
	public function action_plugin_activation( $plugin_file )
	{
		self::install();
	}
	
	public function action_plugin_deactivation( $plugin_file )
	{
		Post::deactivate_post_type( 'day' );
	}
	
	/*
	 * install various stuff we need
	 */
	public static function install() {
		/**
		 * Register content type
		 **/
		Post::add_new_type( 'day' );
		
		// Give anonymous users access
		// $group = UserGroup::get_by_name('anonymous');
		// $group->grant('post_report', 'read');

	}
	
	public function filter_adminhandler_post_loadplugins_main_menu( array $menu )
	{
		unset( $menu['create']['submenu'][ 'create_' . Post::type('day') ] );
		unset( $menu['manage']['submenu'][ 'manage_' . Post::type('day') ] );
		return $menu;
	}

	private static function get_weeks( $month )
	{
		
		$weeks = array();
		$i = 1;
		
		while( $i <= cal_days_in_month(CAL_GREGORIAN, $month->format('n'), $month->format('Y') ) )
		{
			$day = HabariDateTime::date_create( $month->format('F') . ' ' . $i . ', ' . $month->format('Y') );
		
			
			if( $i == 1 || $day->format('N') == 1) // first day or a monday
			{
				$week = array( $day );
				
				$weeks[] = $week;
				
			}
			else
			{
				$weeks[ count( $weeks ) - 1 ][] = $day;
			}
			
			$i++;
		}
		
		return $weeks;
		
	}

	/**
	 * Gets the week a day is in
	 **/
	private static function get_week( $day )
	{
		$weeks = self::get_weeks( $day );
		
		$i = 1;
		
		foreach( $weeks as $week ) {
			foreach( $week as $date ) {
				if( $date->date == $day->date )
				{
					return $i;
				}
			}
			$i++;
		}
		
		return -1;
		
	}
	
	/**
	 * Set up stack 
	 **/
	private static function setup_stack( $type = 'normal' )
	{
		
		Stack::add( 'virtus_header_css', 'http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css', 'reset' );
		Stack::add( 'virtus_header_css', URL::get_from_filesystem( __FILE__ ) . '/humanmsg.css', 'humanmsg', 'reset' );
				
		Stack::add( 'virtus_header_javascript', Site::get_url('scripts') . '/jquery.js', 'jquery' );
		Stack::add( 'virtus_header_javascript', Site::get_url('scripts') . "/jquery-ui.min.js", 'jquery.ui', 'jquery' );
		Stack::add( 'virtus_header_javascript', Site::get_url('scripts') . "/jquery.color.js", 'jquery.color', 'jquery' );
		Stack::add( 'virtus_header_javascript', URL::get_from_filesystem( __FILE__ ) . '/jquery.ajaxmanager.js', 'ajaxmanager', 'jquery' );
		Stack::add( 'virtus_header_javascript', Site::get_url('habari') . "/3rdparty/humanmsg/humanmsg.js", 'humanmsg', 'jquery' );
		Stack::add( 'virtus_header_javascript', URL::get_from_filesystem( __FILE__ ) . '/virtus.js', 'virtus', array( 'jquery', 'ajaxmanager' ) );
		
		switch( $type )
		{
			case 'iphone':
				Stack::add( 'virtus_header_css', URL::get_from_filesystem( __FILE__ ) . '/iphone.css', 'iphone', 'reset' );
				break;
			
			case 'normal':
			default:
				Stack::add( 'virtus_header_css', URL::get_from_filesystem( __FILE__ ) . '/virtus.css', 'virtus', 'reset' );
				break;
		}
		
	}

	private static function view_month( $handler, $month )
	{
		
		$handler->theme->url = URL::get( 'ajax', array( 'context'=>'update_virtue' ) );
		
		self::setup_stack();
		
		// Set month
		$handler->theme->month = $month;
		$handler->theme->month_points = self::calc_month( $month );
		
		// Get records
		$handler->theme->record_month = array(
			'month' => self::get_record_month(),
			'record' => self::calc_month( self::get_record_month() )
			);
			
		$week = self::get_record_week();
		$handler->theme->record_week = array(
			'month' => $week['month'],
			'week' => $week['week'],
			'record' => self::calc_week( $week['week'], $week['month'] )
			);
		
		// Utils::debug( self::get_record_day() );
		
		$handler->theme->record_day = array(
			'day' => self::get_record_day(),
			'record' => self::calc_day( self::get_record_day() )
			);
		
		$weeks = array();
		$i = 1;
		
		while( $i <= cal_days_in_month(CAL_GREGORIAN, $month->format('n'), $month->format('Y') ) )
		{
			
			
			$day = array();			
			$day['date'] = HabariDateTime::date_create( $month->format('F') . ' ' . $i . ', ' . $month->format('Y') );
			
			// Utils::debug( $i, $month->format('F') . ' ' . $i . ', ' . $month->format('Y'), $day['date']->date );
			$day['virtues'] = self::fetch_virtues( $day['date'] );
			
			// $post = false;
			$post = self::get_day( $day['date'] );
			if( !$post ) {
				$day['comment'] = '';
			}
			else
			{
				$day['comment'] = $post->content;	
			}
			
			if( $i == 1 || $day['date']->format('N') == 1) // first day or a monday
			{
				$week = array();
				$week['days'] = array( $day );
				$week['number'] = count( $weeks ) + 1;
				$week['score'] = self::calc_week( $week['number'], $month );
				
				$weeks[] = $week;
				
			}
			else
			{
				$weeks[ count( $weeks ) - 1 ]['days'][] = $day;
			}
			
			$i++;
		}
		
		$handler->theme->weeks = $weeks;
		
		$handler->theme->virtues = self::get_virtues();
				
		$handler->theme->display('habit_month');
	}


	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_virtus($handler)
	{
		$month = HabariDateTime::date_create();
		
		self::view_month( $handler, $month );
	}


	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_month($handler)
	{
		
		$date = HabariDateTime::date_create($handler->handler_vars['month'] . '/01/' . $handler->handler_vars['year'] );
	
		self::view_month( $handler, $date );
	
	}
	
	/**
	 * Handle manifests action
	 **/
	public function action_plugin_act_manifest($handler)
	{
		
		
		header('Content-Type: text/cache-manifest; charset=utf-8');
		// header('Content-Type: text/plain; charset=utf-8');
		
		self::setup_stack( 'iphone' );
		
		// Cached files	
		$cache = array(
			URL::get_from_filesystem( __FILE__ ) . '/icon.png'
		);
		
		$cache = array_merge( $cache, Stack::get_named_stack('virtus_header_css'), Stack::get_named_stack('virtus_header_javascript') );
		
		// Network files (whitelist)
		$network = array (
		);
		
		$date = HabariDateTime::date_create();
		$network[] = URL::get( 'view_day_inner', array( 'year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('j') ) );
		
		$ver = UUID::get();
		$ver = '19';
		
		$manifest = "CACHE MANIFEST\r\n";
		
		$manifest .= "NETWORK:\r\n";
		foreach( $network as $url ) {
			$manifest.= $url . "\r\n";
		}
		
		$manifest .= "CACHE:\r\n";
		foreach( $cache as $url ) {
			$manifest.= $url . "\r\n";
		}
		
		$manifest.= '# ver' . $ver . "\r\n";		
		
		echo utf8_encode( $manifest );
	
	}
	
	
	private static function view_day( $handler, $date )
	{		
		$handler->theme->url = URL::get( 'ajax', array( 'context'=>'update_virtue' ) );
		$handler->theme->refresh_url = URL::get( 'view_day_inner', array( 'year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('j') ) );
		
		$handler->theme->icon = URL::get_from_filesystem( __FILE__ ) . '/icon.png';
		
		$handler->theme->manifest = URL::get( 'manifest', array( 'manifest' => 'day' ) );
		
		// virtus::stack_remove();
	
		self::setup_stack( 'iphone' );
				
		$handler->theme->display('habit_day');
	}
	
	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_today($handler)
	{
		$day = HabariDateTime::date_create();
		
		self::view_day( $handler, $day );
	}
	
	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_homepage($handler)
	{
		$handler->theme->url = URL::get( 'ajax', array( 'context'=>'update_virtue' ) );
		$handler->theme->refresh_url = URL::get( 'view_day_inner', array( 'year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('j') ) );
		
		$handler->theme->icon = URL::get_from_filesystem( __FILE__ ) . '/icon.png';
		
		$handler->theme->manifest = URL::get( 'manifest', array( 'manifest' => 'day' ) );
		
		// virtus::stack_remove();
	
		self::setup_stack( 'iphone' );
				
		$handler->theme->display('homepage');
	}


	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_day($handler)
	{
		
		$date = HabariDateTime::date_create( $handler->handler_vars['month'] . '/' . $handler->handler_vars['day'] . '/' . $handler->handler_vars['year'] );
	
		self::view_day( $handler, $date );
	
	}
	
	/**
	 * Handle register_page action
	 **/
	public function action_plugin_act_view_day_inner($handler)
	{
		
		$date = HabariDateTime::date_create( $handler->handler_vars['month'] . '/' . $handler->handler_vars['day'] . '/' . $handler->handler_vars['year'] );
		
		$day = array();
		
		$day['date'] = $date;
		
		// Utils::debug( $i, $month->format('F') . ' ' . $i . ', ' . $month->format('Y'), $day['date']->date );
		$day['virtues'] = self::fetch_virtues( $day['date'] );
		
		// $post = false;
		$post = self::get_day( $day['date'] );
		if( !$post ) {
			$day['comment'] = '';
		}
		else
		{
			$day['comment'] = $post->content;	
		}
		
		$day['points'] = self::calc_day( $day['date'] );
		
		$handler->theme->day = $day;
		
		$handler->theme->virtues = self::get_virtues();
				
		$handler->theme->display('habit_day_inner');
	
	}
	
	/**
	 * Gets the virtues on a given day 
	 **/
	private static function fetch_virtues( $date )
	{
		$virtues = array();
		
		// Utils::debug( $date->date );
		
		foreach( self::get_virtues() as $key => $virtue )
		{
			if( isset( $virtue['default'] ) )
			{
				$virtues[ $key ] = $virtue['default'];
			}
			else
			{
				$virtues[ $key ] = 0;
			}
		}
				
		$post = self::get_day( $date );
		
		// Utils::debug( $post, $date->format('Y-m-d') );
		
		if( !$post || $post->info->virtues == null )
		{
			return $virtues;
		}
		
		// Utils::debug( $post->pubdate->date, $date->date );
		
		// Utils::debug( $post->info->virtues );
				
		foreach( $virtues as $key => $virtue )
		{
			// Utils::debug( $key, $virtue, $virtues );
			
			if( isset( $post->info->virtues[ $key ] ) )
			{
				$virtues[ $key ] = $post->info->virtues[ $key ];
			}
		}
		
		// Utils::debug( $virtues );
		
		return $virtues;
		
	}
	
	
	/**
	 * Gets the monthly record 
	 **/
	public static function get_record_month()
	{
		$record = Options::get('virtus__monthlyrecord');
		
		if( $record == null )
		{
			return false;
		}
		else
		{
			return HabariDateTime::date_create( $record );
		}
		
	}
	
	/**
	 * Gets the weekly record 
	 **/
	public static function get_record_week()
	{
		$record = Options::get('virtus__weeklyrecord');
				
		if( $record == null )
		{
			return false;
		}
		else
		{
			return array(
				'week' => $record['week'],
				'month' => HabariDateTime::date_create( $record['month'] )
			);
		}
		
	}
	
	/**
	 * Gets the daily record 
	 **/
	public static function get_record_day()
	{
		$record = Options::get('virtus__dailyrecord');
		
		if( $record == null )
		{
			return false;
		}
		else
		{
			return HabariDateTime::date_create( $record );
		}
		
	}
	
	/**
	 * Calculates the score for a month 
	 **/
	private static function calc_month( $month, $force = false )
	{
		$points = 0;
		
		$i = 1;
		
		while( $i <= cal_days_in_month(CAL_GREGORIAN, $month->format('n'), $month->format('Y') ) )
		{
			
			$points += self::calc_day( HabariDateTime::date_create( $month->format('F') . ' ' . $i . ', ' . $month->format('Y') ), $force );
			
			$i++;
		}
		
		if( !$force ) // Don't do record calculations
		{
			return $points;
		}
		
		$record = self::get_record_month();
		
		if( $record == false )
		{
			Options::set( 'virtus__monthlyrecord', $month->sql);
		}
		else
		{			
			if( $points > self::calc_month( $record ) )
			{
				Options::set( 'virtus__monthlyrecord', $month->sql);
			}
		}
		
		return $points;
	}
	
	/**
	 * Calculates the score for a week 
	 **/
	private static function calc_week( $week, $month, $force = false )
	{
		
		$points = 0;
		
		$weeks = self::get_weeks( $month );
		
		foreach( $weeks[ $week - 1 ] as $day )
		{
			$points += self::calc_day( $day, $force );
		}
		
		if( !$force ) // Don't do record calculations
		{
			return $points;
		}
		
		$record = self::get_record_week();
				
		if( $record == false )
		{
			Options::set( 'virtus__weeklyrecord', array( 'week' => $week, 'month' => $month->sql ) );
		}
		else
		{						
			if( $points > self::calc_week( $record['week'], $record['month'] ) )
			{
				Options::set( 'virtus__weeklyrecord', array( 'week' => $week, 'month' => $month->sql ) );
			}
		}
		
		return $points;
	}
	
	/**
	 * Calculates the score for some days
	 **/
	private static function calc_days( $days, $force = false )
	{
		
		$points = 0;
		$virtues = self::get_virtues();
		
		foreach( $days as $day )
		{
			
			foreach( $day['virtues'] as $virtue => $status )
			{
				if( $status >= $virtues[$virtue]['parts'] )
				{
					$points++;
				}
			}
						
		}
		
		return $points;
	}
	
	/**
	 * Calculates the score for a day 
	 **/
	private static function calc_day( $day, $force = false )
	{
		
		$points = 0;
		$virtues = self::get_virtues();
		
		foreach( self::fetch_virtues( $day ) as $virtue => $status )
		{
			// Utils::debug( $virtue );
			
			if( $status >= $virtues[$virtue]['parts'] )
			{
				$points++;
			}
		}
		
		if( !$force ) // Don't do record calculations
		{
			return $points;
		}
		
		$record = self::get_record_day();
				
		if( $record == false )
		{
			Options::set( 'virtus__dailyrecord', $day->sql );
		}
		else
		{									
			if( $points > self::calc_day( $record ) )
			{
				Options::set( 'virtus__dailyrecord', $day->sql );
			}
		}
		
		return $points;
	}
	
	public function action_ajax_update_virtue( $handler ) 
	{
		$date = HabariDateTime::date_create( $handler->handler_vars['date'] );
		
		$post = self::get_day( $date );
		
		if( !$post) {
			$date->modify('+1 hour');
			$post = Post::create( array( 'title' => $date->format('Y-m-d'), 'pubdate' => $date, 'content_type' => Post::type('day') ) );
		}
		
		$comment = $handler->handler_vars['comment'];
		if( isset( $comment ) )
		{
			
			$post->content = $comment;
			
			$post->update();
			
		}
		else
		{
			$virtue = $handler->handler_vars['virtue'];
			$status = (int) $handler->handler_vars['status'];		

			$post = self::get_day( $date );

			if( !$post->info->virtues )
			{
				$virtues = array();
			}
			else
			{
				$virtues = $post->info->virtues;
			}

			$virtues[ $virtue ] = $status;

			$post->info->virtues = $virtues;

			$post->update();
			
			$results = array();
			
			$results['day'] = self::calc_day( $date, true );
			$results['week'] = self::calc_week( self::get_week( $date ), $date, true );
			$results['month'] = self::calc_month( $date, true );
			
			$record_month = self::get_record_month();
			$results['record-month'] = self::calc_month( $record_month ) . ' in ' . $record_month->format('F Y');
			
			$record_week = self::get_record_week();
			$results['record-week'] = self::calc_week( $record_week['week'], $record_week['month'] ) . ' in ' . $record_week['month']->format('F Y');
			
			$record_day = self::get_record_day();
			$results['record-day'] = self::calc_day( $record_day ) . ' on ' . $record_day->format('F j, Y');
			
			echo json_encode( $results );
		}
		
		
					
	}
	
	private static function get_day( $date )
	{
		
		// Utils::debug( $date->date, array( 'content_type' => Post::type('day'), 'day' => $date->format('d'), 'month' => $date->format('m'), 'year' => $date->format('Y'), 'ignore_permissions' => true ) );
		
		$post = Posts::get( array( 'content_type' => Post::type('day'), 'day' => $date->format('d'), 'month' => $date->format('m'), 'year' => $date->format('Y'), 'ignore_permissions' => true ) );
				
		if( count( $post ) >= 1 )
		{
			// Utils::debug( $date->date, $post[0]->pubdate->date );
			return $post[0];
		}
		else
		{
			return false;
		}
		
	}
	
	
}

?>