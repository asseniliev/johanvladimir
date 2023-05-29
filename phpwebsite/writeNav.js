function writeNavLink( id, menuID )
{
	document.write( '<td> <a class="bareLink" href="index.php?module=pagemaster&PAGE_user_op=view_page&PAGE_id=' );
	document.write( id );
	if ( menuID != 0 )
	{
		document.write( '&MMN_position=' );
		document.write( menuID );
	}
	document.write( '">' );
}


function writeNav( pages, menuID )
{
	document.write( '<table align="center" border="0"><tr>' );
//	document.write( '<p align="center">' );
	var s = document.location.search;
	var i = s.search("PAGE_id=");
	if ( i == -1 )
	{
		document.write( '<!-- writeNav error: PAGE_id not found in URL -->' );
		return;
	}
	i += 8;
	s = s.substr(i);
	var j = s.search("&");
	if ( j != -1 )
	{
		s = s.substr(0,j);
	}
	for ( i=0; i < pages.length; ++i )
	{
		if ( pages[i] == s )
			break;
	}
	if ( i == pages.length )
	{
		document.write( '<!-- writeNav error: PAGE_id not found in pages array -->' );
		return;
	}
	if ( i > 0 )
	{
		writeNavLink( pages[i-1], menuID );
		document.write( '&lt; </a></td>' );
	}
	for ( j=0; j < pages.length; ++j )
	{
		if ( j != i )
		{
			writeNavLink( pages[j], menuID );
			document.write( j+1 );
			document.write( '</a></td>' );
		}
		else
		{
			document.write( '<td>' );
			document.write( i+1 );
			document.write( '</td>' );
		}
	}
	if ( i < pages.length-1 )
	{
		writeNavLink( pages[i+1], menuID );
		document.write( '&gt; </a>' );
	}
//	document.write( '</p>' );
	document.write( '</tr></table>' );
}
