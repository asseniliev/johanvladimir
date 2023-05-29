function pr( str )
{
	document.write( str )
}

function prt( id )
{
	pr( t[id] )
}

function toGoth( str, fntSize )
{
	var s = new String( str )
	if ( fntSize == null ) fntSize = 0;
	return '<font face="GothicE" size="+' + fntSize + '"> <font size="+' + (fntSize+2) + '" color="red">'
		+ s.charAt(0) + '</font>' + s.substring( 1, s.length ) + '</font>'
}

function prTitle( i, lnk )
{
	if ( lnk ) {
		pr( '&nbsp; <a style="color:black" href="story_' + i + '.shtml" onclick="setCookie(\'story\','
			 + i + ');">' )
	}
//	pr( '' + i + '.' + ' ' )
	pr( toGoth( title[i], 1 ) )
	if ( lnk ) {
		pr( '</a>' )
	}
}

function getTitleImg( i )
{
	return '<img border=0 src="images/content_' + i + '_' + getCookie('lang') + '.png">'
}

function prTitleImg( i )
{
	pr( '<a href="story_' + i + '.shtml" onclick="setCookie(\'story\','
		 + i + ');">' )
	pr( getTitleImg( i ) )
	pr( '</a>' )
}


t = new Array()
title = new Array()


if ( getCookie('lang') == 'eng' ) {

//--------------- English -------------------

t["title"] = "Johan Vladimir<br>Legends About The Guardians Of The Three Gates"
t["menuHome"] = "Home"
t["menuContents"] = "Contents"
t["menuAuthor"] = "The Author"
t["menuIllust"] = "The Illustrators"
t["menuGuestbook"] = "Guestbook"
t["menuThanks"] = "Thanks"
t["contents"] = "Contents"

t["content_kakavida"] = 

t["guestbook"] = "Guestbook"
t["page1of1"] = "Page #1 of 1"
t["noentriesyet"] = "There are no entries yet"
t["signguestbook"] = "Sign my guestbook"
t["guestbookdisabled"] = "Guestbook is locked"
t["addcomment"] = "Please add your comment"
t["PageN"] = "Page # "
t["of"] = "of"
t["Top"] = "Top"
t["Bottom"] = "Bottom"
t["bottom"] = "bottom"
t["Name"] = "Name"
t["Website"] = "Website"
t["Comment"] = "Comment"
t["requiredfield"] = "Required field"
t["vercode"] = "Verification Code"
t["retype"] = "Please retype this code below"
t["submit"] = '<input type="submit" value="Submit">'
t["reset"] = '<input type="reset" value="Reset">'
t["missingfields"] = "A required field is missing"
t["tryagain"] = "Try again"
t["invalidemail"] = "Invalid email address"
t["invalidurl"] = "Invalid URL format"
t["invalidword"] = "Invalid word found on your entry"
t["toomanymessages"] = "Sorry, only 2 messages allowed per session"
t["invalidvercode"] = "Invalid verification code"
t["pleasewait"] = "Please wait..."
t["entryadded"] = "Thank you, your entry has been added"
t["invalidadminpass"] = "Invalid admin password"
t["entrydeleted"] = "Record has been deleted"

title['kakavida'] = "The Cocoon"
title['kamak'] = "She is Stone"
title['lampa'] = "The Lamp"
title['obrok'] = "The Vow"
title['atentat'] = "The assault"
title['robi'] = "Three Caravans of Slaves"
title['zwiar'] = "The Beast"
title['demon'] = "Demonofilia"
title['zanaiat'] = "Family business"
title['ritsar'] = "The Last Knight"

//	} else if ( getCookie('lang') == 'rus' ) {		// no russian yet

} else {

//--------------- Bulgarian -------------------

t["title"] = "���� ��������<br>������� �� �������� �� ����� �����"
t["menuHome"] = "������"
t["menuContents"] = "����������"
t["menuAuthor"] = "�������"
t["menuIllust"] = "�������������"
t["menuGuestbook"] = "����� �� �����"
t["menuThanks"] = "�������������"
t["contents"] = "����������"

t["guestbook"] = "����� �� �����"
t["page1of1"] = "�������� #1 �� 1"
t["noentriesyet"] = "��� ���� ���������"
t["signguestbook"] = "�������� ����� ��������"
t["guestbookdisabled"] = "������� �� ����� � ���������"
t["addcomment"] = "����, �������� ����� ��������"
t["PageN"] = "�������� # "
t["of"] = "��"
t["Top"] = "������"
t["Bottom"] = "����"
t["bottom"] = "����"
t["Name"] = "���"
t["Website"] = "�������"
t["Comment"] = "��������"
t["requiredfield"] = "������������ ����"
t["vercode"] = "��� �� �����������"
t["retype"] = "���� ������������ ���� ��� ����"
t["submit"] = '<input type="submit" value="�������">'
t["reset"] = '<input type="reset" value="�������">'
t["missingfields"] = "������ ������������ ����"
t["tryagain"] = "�������� ������"
t["invalidemail"] = "��������� e-mail �����"
t["invalidurl"] = "��������� URL"
t["invalidword"] = "� ��������� �� ��� ��������� ����"
t["toomanymessages"] = "���������, ��������� �� �� 2 ��������� �� �����"
t["invalidvercode"] = "��������� ��� �� �����������"
t["pleasewait"] = "����, ���������..."
t["entryadded"] = "���������, ���������� �� ���� �������"
t["invalidadminpass"] = "������ ���������������� ������"
t["entrydeleted"] = "���������� ���� ������"

title['kakavida'] = "����������"
title['kamak'] = "�� � �����"
title['lampa'] = "������� �� �������"
title['obrok'] = "�����"
title['atentat'] = "���������"
title['robi'] = "��� �������� ����"
title['zwiar'] = "������"
title['demon'] = "�����������"
title['zanaiat'] = "������ ������"
title['ritsar'] = "���������� �����"

}
