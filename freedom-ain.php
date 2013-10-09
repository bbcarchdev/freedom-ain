<?php

/*
 * Copyright 2012 BBC.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/*
Plugin Name: The Space: Freedom-ain
Description: Modifies URIs output by Wordpress to not include a scheme-and-hostname prefix.
Author: Mo McRoberts
Author URI: http://www.bbc.co.uk/
*/

class Freedomain
{
	public static $instance;
	
	public static function init()
	{
		self::$instance = new Freedomain();
	}
	
	public function __construct()
	{
		$linkFilters = array('home_url', 'content_url', 'includes_url', 'plugins_url', 'stylesheet_url', 'style_loader_src', 'script_loader_src');
		foreach($linkFilters as $filter)
		{
			add_filter($filter, array($this, 'filterLink'), 1000, 1);
		}
		add_filter('upload_dir', array($this, 'onupload_dir'), 1000, 1);
		add_filter('bloginfo_url', array($this, 'onbloginfo_url'), 1000, 2);
		add_filter('site_url', array($this, 'onsite_url'), 1000, 4);
		add_filter('index_rel_link', array($this, 'onindex_rel_link'), 1000, 1);
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		add_action('wp_head', array($this, 'wlwmanifest_link'));
		add_action('wp_head', array($this, 'rsd_link'));
		add_action('wp_default_styles', array($this, 'onwp_default_styles'), 1000, 1);
		add_action('wp_default_scripts', array($this, 'onwp_default_scripts'), 1000, 1);
	}

	public function onwp_default_styles(&$wp_styles)
	{
		$wp_styles->base_url = '';
	}

	public function onwp_default_scripts(&$wp_scripts)
	{
		$wp_scripts->base_url = '';
	}

	public function onbloginfo_url($url, $kind)
	{
		return $this->filterLink($url);
	}

	public function onsite_url($url, $path = null, $orig_scheme = null, $blog_id = null)
	{
		return $this->filterLink($url);
	}

	public function onindex_rel_link($link)
	{
		return str_replace("href=''", "href='/'", $link);
	}

	public function wlwmanifest_link()
	{
		$url = includes_url('wlwmanifest.xml');
        echo '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="'
			. $url . '" /> ' . "\n";
	}

	public function rsd_link()
	{
		$url = site_url('xmlrpc.php?rsd');
        echo '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="'
			. $url . '" /> ' . "\n";
	}
	
	public function onupload_dir($dir)
	{
		$dir['url'] = $this->filterLink($dir['url']);
		$dir['baseurl'] = $this->filterLink($dir['baseurl']);
		return $dir;
	}

	/* Return a site-relative version of a link */
	public function filterLink($link)
	{
		if(!strncmp($link, '//', 2))
		{
			return substr($link, 1);
		}
		$matches = array();
		if(preg_match('!^https?://[^/]+(/.*)?$!', $link, $matches))
		{
			if(isset($matches[1]))
			{
				return $matches[1];
			}
			return '/';
		}
		return $link;
	}
}

Freedomain::init();
