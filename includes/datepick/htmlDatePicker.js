/**************************************************************************************
	htmlDatePicker v0.5
	
	Copyright (c) 2007, Jason Powell
	All Rights Reserved

	Redistribution and use in source and binary forms, with or without modification, are 
		permitted provided that the following conditions are met:

		* Redistributions of source code must retain the above copyright notice, this list of 
			conditions and the following disclaimer.
		* Redistributions in binary form must reproduce the above copyright notice, this list 
			of conditions and the following disclaimer in the documentation and/or other materials 
			provided with the distribution.
		* Neither the name of the product nor the names of its contributors may be used to 
			endorse or promote products derived from this software without specific prior 
			written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS 
	OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
	MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL 
	THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
	GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
	AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
	OF THE POSSIBILITY OF SUCH DAMAGE.
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	How to integrate htmlDatePicker into an HTML page:
	
	1) In the <head> portion of your HTML page, place the following code:
		<script language="JavaScript" src="datepicker.js" type="text/javascript"></script>
		<link href="datepicker.css" rel="stylesheet" />
		
	2) In your <form> place an <input> element for your date field:
		<input type="text" name="SelectedDate" id="SelectedDate" readonly onClick="GetDate(this);" />
	   NOTE:  "name" and "id" can be whatever you want.  I would suggest leaving "readonly"
	   		  and, of course, the onClick code is the most important and should not be changed.
	
	
***************************************************************************************/
// User Changeable Vars
var HighlightToday	= true;		// use true or false to have the current day highlighted
var DisablePast		= false;		// use true or false to allow past dates to be selectable
// The month names in your native language can be substituted below
var MonthNames = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var DisableNoDateButton = false;	// use true or false to allow the user to select "No Date"
var dateFormat = "n/j/Y";	/*	dateFormat Rules:	(subset of PHP rules)
									Day:
										d = Day of the month, 2 digits with leading zeros (01-31)
										j = Day of the month without leading zeros (1-31)
									Month:
										m = Numeric representation of a month, with leading zeros (01-12)
										n = Numeric representation of a month, without leading zeros (1-12)
										F = full textual representation of a month (January - December)
										M = short textual representation of a month (Jan - Dec)
									Year:
										Y = A full numeric representation of a year, 4 digits (2007)
										y = A two digit representation of a year (07)
							*/
var range_start = null; // Date Ranges are highlighted in a light purple colour
var range_end = null;	// both must be present (not null) in order to work, even for single dates
var fireOnChange = false;	// true || false :: true = the onchange event of the target control will fire when a new date is chosen
var restrictFuture = "";	// can be a single integer (ie 14) to restrict the choosing of a date to X days in the future
							// can also be a future date (in same format of dateFormat) to restrict the choosing of a date from Now to given future date

// Global Vars
var now = new Date();
var dest = null;
var ny = now.getFullYear(); // Today's Date
var nm = now.getMonth();
var nd = now.getDate();
var sy = 0; // currently Selected date
var sm = 0;
var sd = 0;
var y = now.getFullYear(); // Working Date
var m = now.getMonth();
var d = now.getDate();
var MonthLengths = new Array(31,28,31,30,31,30,31,31,30,31,30,31);

/*
	Function: GetDate(control)

	Arguments:
		control = ID of destination control
*/
function GetDate() {
	EnsureCalendarExists();
	DestroyCalendar();
	// One arguments is required, the rest are optional
	// First arguments must be the ID of the destination control
	if(arguments[0] == null || arguments[0] == "") {
		// arguments not defined, so display error and quit
		alert("ERROR: Destination control required in funciton call GetDate()");
		return;
	} else {
		// copy argument
		dest = arguments[0];
	}
	y = now.getFullYear();
	m = now.getMonth();
	d = now.getDate();
	var ddval = ParseFromattedDate(dest.value);
	if(ddval.toDateString() != "NaN" && ddval.toDateString() != "Invalid Date") {
		sm = ddval.getMonth();
		sd = ddval.getDate();
		sy = ddval.getFullYear();
		m=sm;
		d=sd;
		y=sy;
	}
	
	/* Calendar is displayed above the destination element*/
	var position = getPosition(dest);
	l = position.x;
	t = position.y - position.h - 128;	// just using 128 as a "good guess" for size of calendar, it will be repositioned right after to the proper height
	if(t < 0) t = 0;
	
	DrawCalendar(l, t);
	eCal = document.getElementById("dpCalendar");
	t = position.y - eCal.offsetHeight;
	if(t < 0) t = 0;
	RepositionCalendar(l, t);
}

/*
	function DestoryCalendar(l, t)
	
	Purpose: Destory any already drawn calendar so a new one can be drawn
*/
function DestroyCalendar() {
	var cal = document.getElementById("dpCalendar");
	if(cal != null) {
		cal.innerHTML = null;
		cal.style.display = "none";
	}
	return
}

/*
	function DrawCalendar(l, t)
	Where:	l = the left position for the calendar
			t = the top position for the calendar
	
	Purpose: Create the calendar and draw it on the page
*/
function DrawCalendar(l, t) {
	DestroyCalendar();
	var cal = document.getElementById("dpCalendar");
	if(cal.style.left == "" && arguments[0] != null) {	// IE7 work around, should be set ALWAYS though!
		cal.style.left = l + "px";
		cal.style.top = t + "px";
	}
	
	var sCal = "<table><tr><td class=\"cellButton\"><a href=\"javascript: PrevMonth();\" title=\"Previous Month\">&lt;&lt;</a></td>"+
		"<td class=\"cellMonth\" width=\"80%\" colspan=\"5\">"+MonthNames[m]+" "+y+"</td>"+
		"<td class=\"cellButton\"><a href=\"javascript: NextMonth();\" title=\"Next Month\">&gt;&gt;</a></td></tr>"+
		"<tr><td>S</td><td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td></tr>";
	var wDay = 1;
	var wDate = new Date(y,m,wDay);
	if(isLeapYear(wDate)) {
		MonthLengths[1] = 29;
	} else {
		MonthLengths[1] = 28;
	}
	rangeExists = false;
	if(range_start != null && range_end != null) {
		if(typeof(range_start)=="object" && typeof(range_end)=="object") {
			rangeExists = true;
			if(range_start.valueOf() > range_end.valueOf()) {
				// ranges are backwards, so flip
				var range_tmp = range_start;
				range_start = range_end;
				range_end = range_tmp;
			}
		}
	}
	var dayclass = "";
	var isToday = false;
	var linkDay = false;
	for(var r=1; r<7; r++) {
		sCal = sCal + "<tr>";
		for(var c=0; c<7; c++) {
			var wDate = new Date(y,m,wDay);
			if(wDate.getDay() == c && wDay<=MonthLengths[m]) {
				if(wDate.getDate()==sd && wDate.getMonth()==sm && wDate.getFullYear()==sy) {
					dayclass = "cellSelected";
					isToday = true;  // only matters if the selected day IS today, otherwise ignored.
				} else if(wDate.getDate()==nd && wDate.getMonth()==nm && wDate.getFullYear()==ny && HighlightToday) {
					dayclass = "cellToday";
					isToday = true;
				} else {
					dayclass = "cellDay";
					isToday = false;
					// check to see if date lies in range
					if(rangeExists) {
						if(wDate.valueOf() >= range_start.valueOf() && wDate.valueOf() <= range_end.valueOf()) {
							dayclass = "cellRange";
						}
					}
				}
				linkDay = (((now > wDate) && !DisablePast) || (now <= wDate) || isToday);
				if(linkDay) {
					// Day is potentially selectable at this point.  Make sure we aren't restricting future dates as well
					if(restrictFuture != "") {
						// we are restricting future dates
						if(parseInt(restrictFuture) == restrictFuture) {
							// we are restricting by X days
							var dRestriction = new Date(now.getTime() + (parseInt(restrictFuture, 10) * 24 * 60 * 60 * 1000));	// add X days
						} else {
							// we are restricting by a future date
							var dRestriction = ParseFromattedDate(restrictFuture);
						}
						dRestriction.setHours(23,59,59,999);
						linkDay = (wDate.getTime() < dRestriction.getTime());
					}
				}
				if(linkDay) { 
					// date is selectable
					sCal = sCal + "<td class=\""+dayclass+"\"><a href=\"javascript: ReturnDay("+wDay+");\">"+wDay+"</a></td>";
				} else {
					// date is read only
					sCal = sCal + "<td class=\""+dayclass+"\">"+wDay+"</td>";
				}
				wDay++;
			} else {
				sCal = sCal + "<td class=\"unused\"></td>";
			}
		}
		sCal = sCal + "</tr>";
	}
	if(DisableNoDateButton) {
		sCal = sCal + "<tr><td colspan=\"4\" class=\"unused\"></td>";
	} else {
		sCal = sCal + "<tr><td colspan=\"3\" class=\"cellCancel\"><a href=\"javascript: ReturnDay(0);\">No Date</a></td>";
		sCal = sCal + "<td colspan=\"1\" class=\"unused\"></td>";
	}
	sCal = sCal + "<td colspan=\"3\" class=\"cellCancel\"><a href=\"javascript: DestroyCalendar();\">Cancel</a></td></tr></table>"
	cal.innerHTML = sCal; // works in FireFox, Opera, IE7
	cal.style.display = "inline";
}

function ShowHideCalendar(force) {
	// force = true|false;  true = show; false = hide;
	eCal = document.getElementById("dpCalendar");
	if(arguments[0] == null) {
		// no force given, so toggle
		if(eCal.style.display == "" || eCal.style.display == "inline") {
			// calendar is shown, so hide
			eCal.style.display = "none";
		} else {
			// calendar is hidden, so show
			eCal.style.display = "inline";
		}
	} else {
		// force specific state
		if(force) {
			eCal.style.display = "inline";
		} else {
			eCal.style.display = "none";
		}
	}
}

function RepositionCalendar(l, t) {
	eCal.style.left = l + "px";
	eCal.style.top  = t + "px";
}

function PrevMonth() {
	m--;
	if(m==-1) {
		m = 11;
		y--;
	}
	DrawCalendar();
}

function NextMonth() {
	m++;
	if(m==12) {
		m = 0;
		y++;
	}
	DrawCalendar();
}

function ReturnDay(day) {
	sy = y;
	sm = m;
	sd = day;
	if(day == 0) {
		dest.value = "";
	} else {
		m++;	// month numbers start at 0, so add one.
		year = "" + y;
		sOutput = dateFormat;
		sOutput = sOutput.replace(/j/,day);							// day NLZ
		sOutput = sOutput.replace(/d/,(day<10?"0":"")+day);			// day LZ
		sOutput = sOutput.replace(/Y/,year);						// year, 4 digit
		sOutput = sOutput.replace(/y/,year.substring(y.length-2));	// year, 2 digit
		sOutput = sOutput.replace(/n/,m);							// month NLZ
		sOutput = sOutput.replace(/m/,(m<10?"0":"")+m);				// month LZ
		sOutputBefore = sOutput;
		sOutput = sOutput.replace(/F/,MonthNames[m-1]);				// month Name Long
		if(sOutputBefore == sOutput) {
			sOutput = sOutput.replace(/M/,MonthNames[m-1].substring(3,0));	// month Name Short
		}
		dest.value = sOutput;
	}
	DestroyCalendar();
	if(fireOnChange && typeof(dest.onchange) == "function") dest.onchange();
}

function EnsureCalendarExists() {
	if(document.getElementById("dpCalendar") == null) {
		var eCalendar = document.createElement("div");
		eCalendar.setAttribute("id", "dpCalendar");
		document.body.appendChild(eCalendar);
	}
}

function isLeapYear(dTest) {
	var y = dTest.getYear();
	var bReturn = false;
	
	if(y % 4 == 0) {
		if(y % 100 != 0) {
			bReturn = true;
		} else {
			if (y % 400 == 0) {
				bReturn = true;
			}
		}
	}
	
	return bReturn;
}

function getPosition(eDest) {
	var position = new Object();

	position.x = Position_getPageOffsetLeft(eDest);
	position.y = Position_getPageOffsetTop(eDest);
	position.w = eDest.offsetWidth;
	position.h = eDest.offsetHeight;
	
	return position;
}

function Position_getPageOffsetLeft (eItem) {
	var retLeft = eItem.offsetLeft;

	while((eItem = eItem.offsetParent) != null) {
	  retLeft += eItem.offsetLeft;
	}
	
	return retLeft;
}

function Position_getPageOffsetTop (eItem) {
	var retTop = eItem.offsetTop;

	while( (eItem = eItem.offsetParent) != null) {
	  retTop += eItem.offsetTop;
	}

	return retTop;
}

/*
	Function: ParseFromattedDate(d)
	
	Where:		d = Date as string
	
	Returns:	Date Object
	
	Purpose:	Parses given string as a date in the format per the global variable "dateFormat"
*/
function ParseFromattedDate(d) {
	// figure out date from format
	var seperator = dateFormat.charAt(1);
	var aFormat = dateFormat.split(seperator);
	var fm, fd, fy;
	for(var i = 0; i < 3; i++) {
		if(aFormat[i] == "Y" || aFormat[i] == "y") {
			fy = i;
		} else if(aFormat[i] == "d" || aFormat[i] == "j") {
			fd = i;
		} else {
			fm = i;
		}
	}
	var adval = d.split(seperator);
	var dret = new Date(Date.parse(adval[fm] + " " + adval[fd] + " " + adval[fy]));
	
	return dret;
}
