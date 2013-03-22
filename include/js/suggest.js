// Global XMLHttpObject variable

function request() {
var req = null;
if (typeof XMLHttpRequest != 'undefined')
    req = new XMLHttpRequest();
if (!req && typeof ActiveXObject != 'undefined')
{
    try
    {
        req=new ActiveXObject('Microsoft.XMLHTTP');
    }
    catch (e)
    {
        try
        {
            req=new ActiveXObject('Msxml2.XMLHTTP');
        }
        catch (e2)
        {
            try
            {
                req=new ActiveXObject('Msxml2.XMLHTTP.4.0');
            }
            catch (e3)
            {
                req=null;
            }
        }
    }
}
return req;
}

function suggest(e)
{
	
	// first check to make sure the search is NOT empty
	if(document.getElementById("q").value != '' || document.getElementById("q").value != null){

		// let's check to see if they pressed shift, ctrl, etc.. things we don't care about
		var evt = e || window.event;
		if(evt.keyCode == 16 || evt.keyCode == 17 || evt.keyCode == 27 || evt.keyCode == 18 || evt.keyCode == 33 || evt.keyCode == 34 || evt.keyCode == 35 || evt.keyCode == 36 || evt.keyCode == 37 || evt.keyCode == 38 || evt.keyCode == 39 || evt.keyCode == 40 || evt.keyCode == 113 || evt.keyCode == 114 || evt.keyCode == 115 || evt.keyCode == 116 || evt.keyCode == 117 || evt.keyCode == 118 || evt.keyCode == 119 || evt.keyCode == 120 || evt.keyCode == 121 || evt.keyCode == 122 || evt.keyCode == 123){return false;}
		reqsuggest = request();
	
		if (reqsuggest==null){window.alert('Your browser does not support AJAX!');return;} 
		reqsuggest.onreadystatechange=stateChanged;

		var url = 'http://www.teckfusion.com/ampush/search_suggest.php';
		// We are posting, not getting


		currtime = new Date().getTime();

		var params = 'time='+currtime+'&q='+document.getElementById("q").value;
		reqsuggest.open('POST',url,true);

		// Send the proper header information along with the POST submission
		reqsuggest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		reqsuggest.setRequestHeader('Content-length', params.length);
		reqsuggest.setRequestHeader('Connection', 'close');

		// This is really the part that does the posting
		reqsuggest.send(params);
		
	}
}

function stateChanged()
{
	var response = '';
	var returnedArray = [];
	returnedArray[0] = true;	// true means error, false means it worked fine
	if(reqsuggest.readyState == 4){
		// It ran so we can now determine if it resulted in an error or was a success
		response = reqsuggest.responseText;
		if(response == 1){returnedArray[1] = "<b>Error:</b> The search was blank.";}	// empty search
		else if(response == 2){returnedArray[1] = "<b>Error:</b> The search type was invalid.";}// invalid searchtype
		else if(response == 3){returnedArray[1] = "<b>Notice:</b> No results found.";}// no results found
		else if(response == 4){returnedArray[1] = "<b>Error:</b> Database error, please try again.";}// there was an error with database
		else {eval(response);} // it worked
		handleOutput(returnedArray);
	}
}

// this will allow us to read the array, create the div, and fill in its contents
function handleOutput(message)
{
	var results = '';
	var div_content = "";
	var re= /<\S[^><]*>/g;
	var suggest_div = document.getElementById('suggest_div');
	var placer = document.getElementById('q');
	
	if(message[0] == true){
		// this means we have an error, let's display the error and be done
		div_content += "<ul><li><a href='javascript:void(0);' onclick='update_search(\"\");'>"+message[1]+"</a></li></ul>";
	} else {
		// this means we do not have an error, message[1] contains an array of the returned results
		var results = message;
		div_content += '<ul>';
		for(i=0;i<results.length;i++){
			div_content += "<li><a href='javascript:void(0)' onclick='update_search(\""+results[i].replace(re,"")+"\");'>"+results[i]+"</a></li>";
		}
		div_content += '</ul>';

	}
	suggest_div.innerHTML = div_content;	
	suggest_div.style.display = "inline";//show the div after it has been positioned
	suggest_div.style.left = GetElementLeft(placer)+parseInt(placer.style.width)+"px";
	//find the textbox's offset, then place div on the right side


}

function update_search(val)
{
	var q = document.getElementById('q');
	if(val != 'close_div'){	q.value = val;}
	q.focus();
	document.getElementById('suggest_div').style.display = 'none';
	return false;
}

//the following functions down here allow us to determine the position of any element on the page, eElement refers to the element object
//credits to http://www.webreference.com/dhtml/diner/realpos1/2.html for explanations of cross-browser issues with element positioning
function GetElementLeft(eElement)
{
    var nLeftPos = eElement.offsetLeft;          // initialize var to store calculations
    var eParElement = eElement.offsetParent;     // identify first offset parent element  
    while (eParElement != null)
    {                                            // move up through element hierarchy
        nLeftPos += eParElement.offsetLeft;      // appending left offset of each parent
        eParElement = eParElement.offsetParent;  // until no more offset parents exist
    }
    return nLeftPos;                             // return the number calculated
}

function GetElementTop(eElement)
{
    var nTopPos = eElement.offsetTop;            // initialize var to store calculations
    var eParElement = eElement.offsetParent;     // identify first offset parent element  
    while (eParElement != null)
    {                                            // move up through element hierarchy
        nTopPos += eParElement.offsetTop;        // appending top offset of each parent
        eParElement = eParElement.offsetParent;  // until no more offset parents exist
    }
    return nTopPos;                              // return the number calculated
}


window.onload = function() {
	// add event listeners
	document.getElementById('q').onkeyup = suggest;// run the calls for the suggest
	document.getElementById('suggest_div').onmouseout = function(e) {// hide the div
		if (!e) var e = window.event;
		var tg = (window.event) ? e.srcElement : e.target;
		if (tg.nodeName != 'DIV') return;// if the element the mouse moved out of is not the DIV, we know it hasn't left
		var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;// check the element the mouse moved to, it's either a child or a parent
		if(reltg.nodeName != 'HTML'){// if it is HTML, we will close it since it moved out
			while (reltg != tg && reltg.nodeName != 'BODY')
				reltg= reltg.parentNode
			if (reltg== tg) return;// it was a child, as we encounter the DIV (parent of child element)
		}
		// Mouseout took place when mouse actually left layer
		this.style.display = 'none';
	}
}