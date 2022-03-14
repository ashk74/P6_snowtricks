let registrationFullname = document.getElementById("registration_form_fullname");
let registrationFullnameResult = document.getElementById("registration-fullname-result");
registrationFullname.oninput = countFieldChar(registrationFullname, registrationFullnameResult, 5);

let registrationPasswordFirst = document.getElementById("registration_form_password_first");
let registrationPasswordFirstResult = document.getElementById("registration-password-first-result");
registrationPasswordFirst.oninput = countFieldChar(registrationPasswordFirst, registrationPasswordFirstResult, 8);

let registrationPasswordSecond = document.getElementById("registration_form_password_second");
let registrationPasswordSecondResult = document.getElementById("registration-password-second-result");
registrationPasswordSecond.oninput = countFieldChar(registrationPasswordSecond, registrationPasswordSecondResult, 8);
