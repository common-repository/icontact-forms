<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://icontact.com
 * @since      1.0.0
 *
 * @package    Icontact_Forms
 * @subpackage Icontact_Forms/admin/partials
 */

namespace Icontact\Icontact_Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="icontact-forms-about">
	<h2 class="title">About iContact Forms</h2>
	<p>This plugin makes it easy to use your iContact forms on your WordPress site and grow your lists!</p>
	<div class="row">
		<div class="inline">
			<div class="icform-video">
				<iframe width="100%" height="170px" src="https://www.youtube.com/embed/m-GXMLpNgGk" title="iContact video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>    
			</div>
		</div>
		<div class="inline">
			<div class="message">
				<p>What can the iContact plugin do? </p>
				<ul class="pagenave">
					<li class="cat-item">Quickly create and add your forms to your WordPress site</li>
					<li class="cat-item">Customize your forms using iContact</li>
					<li class="cat-item">Collect contact information for people who visit your site</li>
					<li class="cat-item">Grow your lists and collect additional data in your forms</li>
				</ul>                
				<p>Learn more about the iContact WordPress Plugin <a href="https://help.icontact.com/customers/s/article/Wordpress-Plugin" target="_blank">here</a>.</p>
			</div>            
		</div>
	</div>
</div>
