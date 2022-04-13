
let input ;
const form = document.aspnetForm;

input = document.createElement("input");
input.setAttribute("name", "__EVENTTARGET");
input.setAttribute("value", "");
input.setAttribute("type", "hidden");
form.prepend(input);


input = document.createElement("input");
input.setAttribute("name", "__EVENTARGUMENT");
input.setAttribute("value", "");
input.setAttribute("type", "hidden");
form.prepend(input);