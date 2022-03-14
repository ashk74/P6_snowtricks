let trickName = document.getElementById("trick_name");
let trickNameResult = document.getElementById("trick-name-result");
trickName.oninput = countFieldChar(trickName, trickNameResult, 3);

let trickContent = document.getElementById("trick_content");
let trickContentResult = document.getElementById("trick-content-result");
trickContent.oninput = countFieldChar(trickContent, trickContentResult, 50);
