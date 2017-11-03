
AO = function(url,query)
{
	this.url 			= url;
	this.query 			= query;
	this.uri			= url + "?" + query;
	this.init 			= false;
	this.http 			= false;
	this.setup();
}
AO.prototype.debug = false;
AO.prototype.setup = function()
{
	if (typeof XMLHttpRequest!='undefined')
	{
		this.http = new XMLHttpRequest();
	}
	if (!this.http && window.ActiveXObject)
	{
		try
		{
			this.http = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try
			{
				this.http = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) { this.http = false; }
		}
	}
	if (this.http) this.init = true;
	if (this.debug) alert(this.http);
}

AO.handler = function(o)
{
	var obj = o;
	if (obj.debug) alert("readyState: " + obj.http.readyState);
	
	if (obj.http.readyState == 4)
	{
		// properties
		obj.txt				= obj.http.responseText;
		obj.xml 			= obj.http.responseXML;
		obj.readyState 		= obj.http.readyState;
		obj.status	 		= obj.http.status;
		obj.statusText		= obj.http.statusText;
		obj.headers 		= obj.http.getAllResponseHeaders();
		
		// methods
		obj.getHeader		= function(aHeader) { return obj.http.getResponseHeader(aHeader); }
		
		//if (obj.status != 200) obj.init = false;
		
		if (obj.debug) alert("status: " + obj.status + "\ncalling onload()");
		
		obj.onload();
	}
}

// early utility method ... need to come up with better ones
AO.prototype.putHere = function(itemID, what)
{
	if (what != null) document.getElementById(itemID).innerHTML = what;
	else document.getElementById(itemID).innerHTML = this.txt;
}

AO.prototype.go = function(v)
{
	if (!this.init) return false;
	var me = this;
	try
	{
		this.http.onreadystatechange = function() { AO.handler(me); }
		
		if (v == "GET")
		{
			if (this.debug) alert("getting: " + this.uri);
			this.http.open("GET", this.uri, true);
			this.http.send(null);
		}
		else if (v == "POST")
		{
			if (this.debug) alert("posting: " + this.url + "\npost data: " + this.query);
			this.http.open("POST", this.url, true);
			// todo: set this as an option so we can actually do a web-service type thing
			this.http.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			this.http.send(this.query);
		}
	}
	catch(e)
	{
		if (this.debug) alert("failed to connect");
		this.init = false;
		this.onload();
	}
}
AO.prototype.get = function() { this.go("GET"); }
AO.prototype.post = function() { this.go("POST"); }
AO.prototype.onload = function() { };
