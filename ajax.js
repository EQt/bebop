var xmlHttp;
var toggleBib = {};
var toggleEntryDetail = {};
var toggleKeywordsCloud = 'h';
var loadingMessage = "<div class=\"loading\">Loading...</div>";

function showCategory(strBy, str)
{ 
toggleBib = {};
toggleEntryDetail = {};

xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 } 

//alert ("showCategory called");

if(str == "showkeywords")
 {
// s for show
  toggleKeywordsCloud = 's';
  showKeywordsCloud();
 }
else
 {
// h for hide
  toggleKeywordsCloud = 'h';
  document.getElementById("keywordsCloud").innerHTML="";
  var url="ajax.php"
  url=url+"?action=showcategory&by="+strBy+"&pub="+str
  url=url+"&sid="+Math.random()
  xmlHttp.onreadystatechange=stateChanged 
  xmlHttp.open("GET",url,true)
  xmlHttp.send(null)
 }
}

function stateChanged() 
{ 
 if (xmlHttp.readyState<4)
   document.getElementById("CfPTable").innerHTML=loadingMessage
 else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("CfPTable").innerHTML=xmlHttp.responseText 
 if(toggleKeywordsCloud == 'h')
   document.getElementById("keywordsCloud").innerHTML="" 

 } 
}

function getBib(pub)
{ 
if(toggleBib[pub]!='s')
{
	toggleBib[pub]='s';
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
 	{
	 alert ("Browser does not support HTTP Request")
	 return
	 } 
	// alert ("getBib called")
	var url="ajax.php"
	url=url+"?action=showbib&pub="+pub
	url=url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=function () { 
                                    openBib(pub, xmlHttp);
                                    }
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
else
{
	toggleBib[pub]='h';
	document.getElementById("bib"+pub).innerHTML='';
}
}


function openBib(pub, xmlHttp) 
{ 
 document.getElementById("bib"+pub).style.visibility="visible";
 if (xmlHttp.readyState<4)
 { 
 document.getElementById("bib"+pub).innerHTML=loadingMessage
 }
 else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("bib"+pub).innerHTML=xmlHttp.responseText 
 } 
}

function getEntryDetail(pub)
{ 
if(toggleEntryDetail[pub]!='s')
{
	toggleEntryDetail[pub]='s';
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
 	{
	 alert ("Browser does not support HTTP Request")
	 return
	 } 
	// alert ("getEntryDetail called")
	var url="ajax.php"
	url=url+"?action=showentrydetail&pub="+pub
	url=url+"&sid="+Math.random()
	xmlHttp.onreadystatechange=function () { 
                                    openEntryDetail(pub, xmlHttp);
                                    }
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
else
{
	toggleEntryDetail[pub]='h';
	toggleBib[pub]='h';
	document.getElementById("entrydetail"+pub).innerHTML='';
	document.getElementById("entrydetail"+pub).style.visibility="hidden";
	document.getElementById("bib"+pub).innerHTML='';
	document.getElementById("bib"+pub).style.visibility="hidden";
}
}

function openEntryDetail(pub, xmlHttp) 
{ 
 document.getElementById("entrydetail"+pub).style.visibility="visible";
 if (xmlHttp.readyState<4)
 { 
 document.getElementById("entrydetail"+pub).innerHTML=loadingMessage
 }
 else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("entrydetail"+pub).innerHTML=xmlHttp.responseText 
 } 
}


function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 // Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}



function showKeywordsCloud()
{ 
toggleKeywordsCloud='s';

xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 } 

//alert ("showKeywordsCloud called");

var url="ajax.php"
url=url+"?action=showkeywordscloud"
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChangedKeywords
xmlHttp.open("GET",url,true)
xmlHttp.send(null)
}

function stateChangedKeywords() 
{ 
  if (xmlHttp.readyState<4)
 { 
 document.getElementById("keywordsCloud").innerHTML=loadingMessage; 
 document.getElementById("CfPTable").innerHTML="";
 } 
 else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("keywordsCloud").innerHTML=xmlHttp.responseText; 
 document.getElementById("CfPTable").innerHTML="";
 } 
}
