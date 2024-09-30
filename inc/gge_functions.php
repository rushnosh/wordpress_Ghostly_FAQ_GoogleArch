<?php 

//GGE util function calls

function gge_version_id() {
	if ( WP_DEBUG )
		return time();
	return GGE_FAQ_VERSION;
}


