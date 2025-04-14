var div = document.createElement("div");
div.id = "busyIndicator";
document.body.insertBefore(div, document.body.firstChild);
document.getElementById('busyIndicator').innerHTML ="Indic Input Web loading....";
document.getElementById("busyIndicator").setAttribute(
   "style", "margin:10px auto;font-size: 16px;background-color:#1CDCE3!important; color:#000; padding-left: 28px; font-family: arial; top: 200 px; fmargin: auto; z-index: 1000;  width: 200px; height: 24px; background: url(https://www.bhashaindia.com/IIW_Tool/image/loading.GIF) no-repeat; cursor: wait;");
var head = document.getElementsByTagName('head')[0];

var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = "http://muexam.in/convocation/js/resource_HI.js";
	head.appendChild(script);
   document.getElementById("busyIndicator").style.display = "block" 


var txBox;
var HindiChar;
var EngWord;
var bufferOldData;
var CountSpaceChar=0;
var strFirstPart;
var strSecondPart;
var textword;
var finalMousePosition=0;
var spaceCount=0;
var CaratPosition=[];
var index=0;
var afterSpaceposition=0;
var tempText1;
var activeObj;
var flagFirstValue=0;
var enterKeyFlag=0;
var strFirstTextarea;
var previousLength = 0;
var CounterTwoWord = 0;

function getCurruntPosition (oField) {
var input= document.getElementById(oField);
        if (!input) return; // No (input) element found
        if (document.selection) {
            // IE
           input.focus();
        }
        return 'selectionStart' in input ? input.selectionStart:'' || Math.abs(document.selection.createRange().moveStart('character', -input.value.length));
}

function setCaretPositionTextarea (obj, position) {
		if (obj.setSelectionRange) {  
			obj.focus();  
			obj.setSelectionRange(position, position);
		} else if (obj.createTextRange) {  
			var range = obj.createTextRange();
			range.move("character", position);  
			range.select(); 
		} else if(window.getSelection){
			
			s = window.getSelection();
			var r1 = document.createRange();
			
			
			var walker=document.createTreeWalker(obj, NodeFilter.SHOW_ELEMENT, null, false);
			var p = position;
			var n = obj;
			
			while(walker.nextNode()) {
				n = walker.currentNode;
				if(p > n.value.length) {
					p -= n.value.length;
				}
				else break;
			}
			n = n.firstChild;
			r1.setStart(n, p);
			r1.setEnd(n, p);
			
			s.removeAllRanges();
			s.addRange(r1);
			
		} else if (document.selection) {
			var r1 = document.body.createTextRange();
			r1.moveToElementText(obj);
			r1.setEndPoint("EndToEnd", r1);
			r1.moveStart('character', position);
			r1.moveEnd('character', position-obj.innerText.length);
			r1.select();
		} 
	}
	
function setCaretPosition(elemId, caretPos) {
    var elem = document.getElementById(elemId);

    if(elem != null) {
        if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else
                elem.focus();
        }
    }
}

function setVal(data,e,originalId)
{
OriginalIdElement.value=data;
}
function getBetweenFinalTextValue(cursorPosition,strFirstPart,strSecondPart,HindiChar)
{

var word;

if(HindiChar=="")
{HindiChar=" ";}
var chkCursor=strFirstPart + HindiChar;
finalMousePosition=chkCursor.length;

word= strFirstPart + HindiChar + strSecondPart;
return word;
}
function getWord(EngWord,c)
{
if (EngWord !== null && EngWord !== undefined && EngWord!=" " ) {
		EngWord=EngWord + c;
		}
	else if(c==" ")
	{
	EngWord="";
	return EngWord;
	}
	else
		{
		EngWord=c;
		}
return EngWord;
}
function rtrim(s)
{
	var r=s.length -1;
	while(r > 0 && s[r] == ' ')
	{	r-=1;	}

	return s.substring(0, r+1);
}

function getFinalTextValue(cursorPosition,textValue,HindiChar,event,olddata)
{
var NewData;
if(olddata=="0")
olddata="";
if(cursorPosition<(textValue.length))
{
    if (olddata !== null && olddata !== undefined && spaceCount!=0)
    return  NewData=olddata + HindiChar;
	return  HindiChar;
	}
 if(cursorPosition=(textValue.length))
 {
 if (olddata !== null && olddata !== undefined && spaceCount==0)
    return  NewData=olddata + HindiChar;
	 else if(olddata !== null && olddata !== undefined && spaceCount!=0)
    return  NewData=olddata + HindiChar;
 }
else
 return  HindiChar;
}
function formatTextArea(textArea) {
        textArea.value = textArea.value.replace(/(^|\r\n|\n)([^*]|$)/g, "$1*$2");
    }
	function Onclick()
	{
activeObj = document.activeElement;
  var originalId=  activeObj.id;
EngWord="";
 HindiChar="";
 bufferOldData="";
 var temptext=document.getElementById(originalId);
 bufferOldData=temptext.value;
}

 function keyup(e)
{
if(e.keyCode ==8 || e.keyCode==37 || e.keyCode==46 || e.keyCode==38 || e.keyCode==39 || e.keyCode==40  || e.keyCode==36  || e.keyCode==33 || e.keyCode==34 || e.keyCode==35 )
 {
 activeObj = document.activeElement;
  var originalId=  activeObj.id;
  if(originalId==' ' || originalId=="")
  {
  activeObj.id="langText";
  originalId="langText";
  }
  
 EngWord="";
 HindiChar="";
 bufferOldData="";
 var temptext=document.getElementById(originalId);
 bufferOldData=temptext.value;
}
if(e.keyCode==9)
{
EngWord="";
 HindiChar="";
 bufferOldData="";
}

}
 
 function FocusOut(event)
 {
 EngWord="";
 HindiChar="";
 bufferOldData="";

}
 
function isIE () {
  var myNav = navigator.userAgent.toLowerCase();
  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
}
var ie= isIE ();
var getHiddenId = document.getElementById("MicrosoftILITWebEmbedInfo").getAttribute("attachMode");

var allowEvent;
if (getHiddenId == "optout") {
    allowEvent = "false";
}
if (getHiddenId == "optin") {
    allowEvent = "true";
}

var input = document.getElementsByTagName('input');
var inputElementlength = input.length;

if (ie == 9) {
    for (var i = 0; i < inputElementlength; i++) {
        var elementHtml = input[i];
        var inputId = "langinput";
        inputId = "langinput" + i;
        var inIds = (elementHtml.id);
        if (!elementHtml.id)
            elementHtml.setAttribute("id", inputId);
        else
            inputId = inIds;
        var WebElement = document.getElementById(inputId).getAttribute("MicrosoftILITWebAttach");
        if (allowEvent == "false") {
            if (WebElement == "false") {
                WebElement = 0;
            }
            else
                WebElement = 1;
        }
        else if (allowEvent == "true") {

            if (WebElement == "true") {
                WebElement = 1;
            }
            else
                WebElement = 0;
        }

        if (WebElement) {
            elementHtml.attachEvent('onkeypress', keypress);
            elementHtml.attachEvent('onkeyup', keyup);
            elementHtml.attachEvent('onblur', FocusOut);
            elementHtml.setAttribute('onclick', "Onclick()");
        }
    }
}
else {
    for (var i = 0; i < inputElementlength; i++) {
        var elementHtml = input[i];
        var inputId = "langinput";
        inputId = "langinput" + i;
        var inIds = (elementHtml.id);
        if (!elementHtml.id)
            elementHtml.setAttribute("id", inputId);
        else
            inputId = inIds;
        var WebElement = document.getElementById(inputId).getAttribute("MicrosoftILITWebAttach");
        if (allowEvent == "false") {
            if (WebElement == "false") {
                WebElement = 0;
            }
            else
                WebElement = 1;
        }
        else if (allowEvent == "true") {

            if (WebElement == "true") {
                WebElement = 1;
            }
            else
                WebElement = 0;
        }


        if (WebElement) {

            elementHtml.setAttribute("onkeypress", "keypress(event)");
            elementHtml.setAttribute("onkeyup", "keyup(event)");
            elementHtml.setAttribute("onblur", "FocusOut(event)");
            elementHtml.setAttribute('onclick', "Onclick()");
        }
    }
}

var textarea = document.getElementsByTagName('textarea');
var textareaLength = textarea.length;
if (ie == 9) {
    for (var i = 0; i < textareaLength; i++) {
        var elementHtml = textarea[i];
        var inputId = "langtextarea";
        inputId = "langtextarea" + i;
        var inIds = (elementHtml.id);
        if (!elementHtml.id)
            elementHtml.setAttribute("id", inputId);
        else
            inputId = inIds;

        var WebElement = document.getElementById(inputId).getAttribute("MicrosoftILITWebAttach");
        if (allowEvent == "false") {
            if (WebElement == "false") {
                WebElement = 0;
            }
            else
                WebElement = 1;
        }
        else if (allowEvent == "true") {

            if (WebElement == "true") {
                WebElement = 1;
            }
            else
                WebElement = 0;
        }

        if (WebElement) {
            elementHtml.attachEvent('onkeypress', keypress);
            elementHtml.attachEvent('onkeyup', keyup);
            elementHtml.attachEvent('onblur', FocusOut);
            elementHtml.setAttribute('onclick', "Onclick()");
        }
    }
}
else {
    for (var i = 0; i < textareaLength; i++) {

        var elementHtml = textarea[i];
        var inputId = "langtextarea";
        inputId = "langtextarea" + i;
        var inIds = (elementHtml.id);
        if (!elementHtml.id)
            elementHtml.setAttribute("id", inputId);
        else
            inputId = inIds;
        var WebElement = document.getElementById(inputId).getAttribute("MicrosoftILITWebAttach");
        if (allowEvent == "false") {
            if (WebElement == "false") {
                WebElement = 0;
            }
            else
                WebElement = 1;
        }
        else if (allowEvent == "true") {

            if (WebElement == "true") {
                WebElement = 1;
            }
            else
                WebElement = 0;
        }

        if (WebElement) {
            elementHtml.setAttribute("onkeypress", "keypress(event)");
            elementHtml.setAttribute("onkeyup", "keyup(event)");
            elementHtml.setAttribute("onblur", "FocusOut(event)");
            elementHtml.setAttribute('onclick', "Onclick()");
        }
    }
}
function keypress(e) {
    
if(e.keyCode==13)
{
enterKeyFlag++;
EngWord="";
 HindiChar="";
 bufferOldData="";
return true;
}
 activeObj = document.activeElement;
 var originalId=activeObj.id;
 if(originalId==' ' || originalId=="")
 {
  originalId="langText";
 activeObj.id="langText";
 }
 OriginalIdElement=document.getElementById(originalId);

 var numberOfLineBreaks = (document.getElementById(originalId).value.match(/\n/g)||[]).length;
 var text = document.getElementById(originalId);
 try
 {
  var t = text.value.substr(text.selectionStart,text.selectionEnd-text.selectionStart);
  }catch(err)
  {
  t ="";}
  if(!ie)
 var selectedText = window.getSelection();
if(t!= "")
{
var originalText = document.getElementById(originalId).value;
if(ie)
{
if (document.selection) { 
               EngWord = "";
                HindiChar = "";
                var originalText = document.getElementById(originalId).value;
                var selectedText = document.selection.createRange().text;
                var startIndex = document.getElementById(originalId).selectionStart;
                var endIndex = startIndex + selectedText.length;
                bufferOldData = originalText.substring(0, startIndex);
                document.getElementById(originalId).value = bufferOldData + originalText.substring(endIndex, originalText.length);
                setCaretPositionTextarea(document.getElementById(originalId), startIndex);
                }
        }
        else {
            var originalText = document.getElementById(originalId).value;
            var startIndex = document.getElementById(originalId).selectionStart;
            var endIndex = startIndex + t.length;
            bufferOldData = originalText.substring(0, startIndex);
            EngWord = "";
            HindiChar = "";
            document.getElementById(originalId).value = bufferOldData + originalText.substring(endIndex);
            setCaretPositionTextarea(document.getElementById(originalId), startIndex);
        }

}
var cursorPosition=getCurruntPosition(originalId); 
var  Length_Before= document.getElementById(originalId).value.length;
if(!(cursorPosition<Length_Before))
{
var objControl=document.getElementById(originalId); 
 objControl.scrollTop = objControl.scrollHeight;
}  

var a=0;
if(HindiChar !== null &&  HindiChar !== undefined) 
var a=HindiChar.length;

var c = String.fromCharCode(e.keyCode);
if(e.keyCode==32)
{EngWord="";
a="";}
if(e.keyCode==13)
{
EngWord="";
HindiChar="";
previousLength=0;
}
textword=OriginalIdElement.value;
if((cursorPosition<(textword.length)) || numberOfLineBreaks > 0 )
{
var temp=textword.substring((cursorPosition-1),cursorPosition);
if(EngWord!=undefined)
{
if(a<EngWord.length)
{
tempText1=textword.substring(0,(cursorPosition - (a)));
}
else
tempText1=textword.substring(0,(cursorPosition - (a)));
}
strFirstTextarea=textword.substring(0,cursorPosition);
strSecondPart=textword.substring(cursorPosition,(textword.length));
EngWord=getWord(EngWord,c);
txBox = EngWord;
}
else
{
EngWord=getWord(EngWord,c);
txBox = EngWord;
txBox=rtrim(txBox);
}


convertEnglishToRegional = function(){
var r;
var langid="HI";
	var _this = this;
	_this.resource = (_this.langid!='EN') ? eval('new Rsrc_'+'HI'+'()') : null;

HindiChar=this.resource.toUn_HI(txBox);
_this.wordlist = (_this.langid != 'EN') ? eval("WIOCWordList_"  +'HI') : null;
	var wrdl = _this.wordlist ;
 if(wrdl!=null){
			HindiChar = (wrdl[EngWord]!=undefined && wrdl[EngWord]!="") ? wrdl[EngWord] : HindiChar;
		}
}

convertEnglishToRegional();

if(e.keyCode==32)
{
HindiChar=HindiChar + ' ';
afterSpaceposition=cursorPosition+1;
CaratPosition[index]=getCurruntPosition(originalId);
spaceCount++;
CountSpaceChar++;
bufferOldData=OriginalIdElement.value;
bufferOldData=bufferOldData+ ' ';

HindiChar='';
}

if(cursorPosition==0)
{
strFirstPart='';
bufferOldData='';
}
if((cursorPosition<(textword.length)) || numberOfLineBreaks > 0 )
{
strFirstPart1=tempText1;
if(strFirstPart1!=undefined)
strFirstPart=strFirstPart1;
else
strFirstPart=strFirstTextarea;
data= getBetweenFinalTextValue(cursorPosition,strFirstPart,strSecondPart,HindiChar);
setVal(data,e,originalId);
if(numberOfLineBreaks==0)
{
setCaretPosition(activeObj.id,finalMousePosition);
}
else
{
var chkCursor=strFirstPart + HindiChar;
if(c==" ")
{chkCursor= chkCursor + ' '};
finalMousePosition=chkCursor.length;
if(finalMousePosition==cursorPosition)
pos=(cursorPosition);
else
{
if((strFirstTextarea.length)<cursorPosition)
var pos=strFirstTextarea.length;
else if(e.keyCode>64 && e.keyCode<90)
pos=finalMousePosition;
else
pos=cursorPosition+1;
}
var diff=2;
if(diff==(finalMousePosition-cursorPosition))
pos= cursorPosition+2;

if(ie)
{
if(e.keyCode==120)
pos= finalMousePosition;
}
setCaretPositionTextarea(document.getElementById(originalId),pos);
}
CounterTwoWord++; 
if (bufferOldData == "")
    CounterTwoWord = 0;
(e.preventDefault) ? e.preventDefault() : e.returnValue = false;
 
return false;
}
else {
    var textValue = OriginalIdElement.value;

    if (CounterTwoWord == 0 && HindiChar.length >= 2) {   
        bufferOldData = "0";
    } 
    else if ((bufferOldData == undefined || bufferOldData == "") && textValue.length > 1)
        bufferOldData = textValue;
    else if (bufferOldData.charAt(1) == " " && textValue.length == 1)
        bufferOldData = bufferOldData;
    else if (textValue.length == 1)
        bufferOldData = "0";


    flagFirstValue++;
    data = getFinalTextValue(cursorPosition, textValue, HindiChar, e.keyCode, bufferOldData);
    setVal(data, e, originalId);
    CounterTwoWord++; 
    if (bufferOldData == "")
        CounterTwoWord = 0; 

    (e.preventDefault) ? e.preventDefault() : e.returnValue = false;

    return false;
}


}

setTimeout(function(){var element = document.getElementById('busyIndicator');  element.parentNode.removeChild(element);LoadedIndicator();},1000);
function LoadedIndicator()
{
var divNext = document.createElement("div");
divNext.id = "busyIndicatorNext";
document.body.insertBefore(divNext, document.body.firstChild);
document.getElementById('busyIndicatorNext').innerHTML ="Indic Input Web loaded";
document.getElementById("busyIndicatorNext").setAttribute(
 "style", "margin: auto;font-size: 16px;background-color:#1CDCE3!important; color:#000; padding-left: 10px;font-family: arial; top: 200 px; fmargin: auto; z-index: 1000;  width: 180px; height: 24px;  no-repeat; cursor: wait;");

 setTimeout(function(){var element = document.getElementById('busyIndicatorNext');  element.parentNode.removeChild(element); },1000);
 }







		

