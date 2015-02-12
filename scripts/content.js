var content = document.getElementById("content");
var bold_item = document.getElementById("bold_type");
var select = null;
var select_content = "";
var ranger = null;
content.addEventListener("blur", function(){
    select = getSelect();
    if(select.text) select_content = select.text;
    else select_content = select.toString();
    ranger = getRanger(select);
    //ranger.setStart(select.anchorNode, select.anchorOffset); 
    //ranger.setEnd(select.focusNode, select.focusOffset); 
    console.log(ranger);
});
bold_item.addEventListener("click", function(){
    if(!select_content){
        hasClass(content, "bold") ? dropClass(content, "bold") : addClass(content, "bold");
    }
    else {
        if(hasClass(content, "bold")) return;
    }
    content.focus();
}, false);
var getSelect = function(){
    if(window.getSelection) return window.getSelection();
    if(document.selection) return document.selection.createRange();
};
var getRanger = function(_select){
    if(_select.getRangeAt) return _select.getRangeAt(0);
    else if(document.createRange) return document.createRange();
    else return _select;
};
var hasClass = function(elem, className){
    if(elem && elem.className){
        return elem.className.indexOf(" " + className) > 0 || elem.className.indexOf(className + " ") > 0
    }
    return false;
};
var addClass = function(elem, className){
    if (elem.classList)  elem.classList.add(className);
    else elem.className = className + ' ' + obj.className;
};
var dropClass = function(elem, className){
    if (elem.classList) elem.classList.remove(str);
    else {
        if(elem.className.indexOf(" " + className) > 0) elem.className = elem.className.replace(" " + className, "");
        else if(elem.className.indexOf(className + " ") > 0) elem.className = elem.className.replace(className + " ", "");
    }
};